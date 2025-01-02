<?php

use PHPUnit\Framework\TestCase;

class TestProtectioninjectionSQL extends TestCase
{
    public function testSqlInjectionPrevention()
    {
        $username = "test' OR '1'='1";
        $db = new SQLite3(':memory:');
        $db->exec("CREATE TABLE users (username TEXT, password TEXT)");
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);

        $result = $stmt->execute();

        $this->assertFalse($result->fetchArray(), "Le système doit empêcher les injections SQL.");
    }
}
