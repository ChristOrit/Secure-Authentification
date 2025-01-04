<?php

use PHPUnit\Framework\TestCase;

class InscriptionTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
       
        $this->db = new SQLite3('notrebase.db');
    }

    public function testCompteCreation()
    {
       
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

        $this->assertNotNull($userData); 
        $this->assertEquals($firstname, $userData['firstname']);  
        $this->assertEquals($lastname, $userData['lastname']); 
    }
}
