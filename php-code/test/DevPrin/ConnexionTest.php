<?php
use PHPUnit\Framework\TestCase;

class ConnexionTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        $this->db = new SQLite3('notrebase.db');
    }

    public function testConnexionValide()
    {
        
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
    }
}
