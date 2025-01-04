<?php

use PHPUnit\Framework\TestCase;

class DevPrinTest extends TestCase
{
    private $db;

    // InscriptionTest
    public function testCompteCreation()
    {
        $this->db = new SQLite3('notrebase.db');

        $firstname = 'John';
        $lastname = 'Doe';
        $username = 'johndoe';
        $password = 'Password123';

        $stmt = $this->db->prepare("INSERT INTO users (firstname, lastname, username, password) VALUES (:firstname, :lastname, :username, :password)");
        $stmt->bindValue(':firstname', $firstname, SQLITE3_TEXT);
        $stmt->bindValue(':lastname', $lastname, SQLITE3_TEXT);
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), SQLITE3_TEXT);
        $stmt->execute();

        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $userData = $result->fetchArray(SQLITE3_ASSOC);

        $stmt = $this->db->prepare("DELETE FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->execute();

        $this->assertNotNull($userData);
        $this->assertEquals($firstname, $userData['firstname']);
        $this->assertEquals($lastname, $userData['lastname']);
    }

    // ConnexionTest
    public function testConnexionValide()
    {
        $this->db = new SQLite3('notrebase.db');

        $username = 'johndoe';
        $password = 'Password123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("INSERT INTO users (firstname, lastname, username, password) VALUES (:firstname, :lastname, :username, :password)");
        $stmt->bindValue(':firstname', 'John', SQLITE3_TEXT);
        $stmt->bindValue(':lastname', 'Doe', SQLITE3_TEXT);
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':password', $hashedPassword, SQLITE3_TEXT);
        $stmt->execute();

        $inputPassword = 'Password123';
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $user = $result->fetchArray(SQLITE3_ASSOC);

        $this->assertNotNull($user);
        $this->assertTrue(password_verify($inputPassword, $user['password']));

        $stmt = $this->db->prepare("DELETE FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->execute();
    }

    // DeconnexionTest
    public function testDeconnexion()
    {
        session_start();
        $_SESSION = [
            'username' => 'johndoe',
            'is_admin' => 0
        ];

        $this->assertArrayHasKey('username', $_SESSION);

        session_unset();
        session_destroy();
        session_write_close();

        $this->assertEmpty($_SESSION);
    }

    // ProfileUpdateTest
    public function testProfileUpdate()
    {
        $db = new SQLite3(':memory:');

        $db->exec("CREATE TABLE users (
            id INTEGER PRIMARY KEY,
            username TEXT NOT NULL,
            email TEXT NOT NULL
        )");

        $db->exec("INSERT INTO users (username, email) VALUES ('testuser', 'oldemail@example.com')");

        $username = "testuser";
        $newEmail = "newemail@example.com";

        $stmt = $db->prepare("UPDATE users SET email = :email WHERE username = :username");
        $stmt->bindValue(':email', $newEmail, SQLITE3_TEXT);
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->execute();

        $stmt = $db->prepare("SELECT email FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $user = $result->fetchArray(SQLITE3_ASSOC);

        $this->assertEquals($newEmail, $user['email']);
    }

    // ErrorMessageTest
   // public function testErrorMessageOnLoginFailure()
    //{
      //  $username = 'invalidUser';
        //$password = 'invalidPass';
        //$output = '';

        //ob_start();
       // if (!$username || !$password) {
         //   echo "<p>Nom d'utilisateur ou mot de passe incorrect.</p>";
       // }
        //$output = ob_get_clean();

       // $this->assertStringContainsString("Nom d'utilisateur ou mot de passe incorrect.", $output);
    //}

    // NavigationTest
    public function testAdminRedirection()
    {
        $_SESSION = [
            'username' => 'adminuser',
            'is_admin' => 1
        ];

        ob_start();
        $this->simulateRedirection();
        $output = ob_get_clean();

        $this->assertStringContainsString("admin-gestion.php", $output);
    }

    public function testUserNavigation()
    {
        $_SESSION = [
            'username' => 'normaluser',
            'is_admin' => 0
        ];

        ob_start();
        $this->simulateRedirection();
        $output = ob_get_clean();

        $this->assertEmpty($output);
    }

    private function simulateRedirection()
    {
        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
            echo "Location: admin-gestion.php";
        }
    }

    // DataPersistenceTest
    public function testSaveAndRetrieveUser()
    {
        $this->db = new SQLite3(':memory:');
        $this->db->exec("CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            firstname TEXT,
            lastname TEXT,
            username TEXT UNIQUE,
            password TEXT,
            is_admin INTEGER
        )");

        $stmt = $this->db->prepare("INSERT INTO users (firstname, lastname, username, password, is_admin) VALUES (:firstname, :lastname, :username, :password, 0)");
        $stmt->bindValue(':firstname', 'John', SQLITE3_TEXT);
        $stmt->bindValue(':lastname', 'Doe', SQLITE3_TEXT);
        $stmt->bindValue(':username', 'johndoe', SQLITE3_TEXT);
        $stmt->bindValue(':password', password_hash('Password1', PASSWORD_DEFAULT), SQLITE3_TEXT);
        $stmt->execute();

        $result = $this->db->query("SELECT * FROM users WHERE username = 'johndoe'");
        $user = $result->fetchArray(SQLITE3_ASSOC);

        $this->assertNotEmpty($user);
        $this->assertSame('John', $user['firstname']);
        $this->assertSame('Doe', $user['lastname']);
        $this->assertSame('johndoe', $user['username']);
    }
}
