<?php

use PHPUnit\Framework\TestCase;

class PasswordSecurityTest extends TestCase
{
    public function testPasswordHashing()
    {
        $password = "SecurePassword123!";
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $this->assertNotEquals($password, $hashedPassword, "Le mot de passe ne doit pas être stocké en clair.");
        $this->assertTrue(password_verify($password, $hashedPassword), "Le mot de passe haché doit être vérifiable.");
    }
}
