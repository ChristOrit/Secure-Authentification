<?php

use PHPUnit\Framework\TestCase;

class Testeur extends TestCase
{
    // Test 1: Email Validation
    public function testValidEmailFormat()
    {
        $email = "user@example.com";
        $this->assertTrue(filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
    }

    public function testInvalidEmailFormat()
    {
        $email = "user@";
        $this->assertFalse(filter_var($email, FILTER_VALIDATE_EMAIL));
    }

    // Test 2: Password Complexity
    public function testPasswordMeetsComplexity()
    {
        $password = "Strong@123";
        $this->assertGreaterThanOrEqual(8, strlen($password)); 
        $this->assertMatchesRegularExpression('/[A-Z]/', $password);
        $this->assertMatchesRegularExpression('/[a-z]/', $password);
        $this->assertMatchesRegularExpression('/[0-9]/', $password);
        $this->assertMatchesRegularExpression('/[@$!%*?&#]/', $password);
    }

    public function testPasswordFailsComplexity()
    {
        $password = "weakpass";
        $this->assertTrue(strlen($password) < 8 || !preg_match('/[@$!%*?&#]/', $password));

        $password2 = "weakpass!";
        $this->assertTrue(strlen($password2) < 8 || !preg_match('/[A-Z]/', $password2) || !preg_match('/[0-9]/', $password2));

        $password3 = "WEAKPASS1!";
        $this->assertTrue(strlen($password3) < 8 || !preg_match('/[a-z]/', $password3));
    }

    // Test 3: Duplicate Account
    private function createAccount($email, $password)
    {
        $existingEmails = ["user@example.com"];
        if (in_array($email, $existingEmails)) {
            return false;
        }
        return true;
    }

    public function testDuplicateAccountCreation()
    {
        $existingEmail = "user@example.com";
        $result = $this->createAccount($existingEmail, "password123");
        $this->assertFalse($result, "Un compte avec cet email existe déjà !");
    }

    // Test 4: Field Length Limit
    private function validateFieldLength($field)
    {
        $maxLength = 255;
        return strlen($field) <= $maxLength;
    }

    public function testFieldLengthLimit()
    {
        $username = str_repeat("a", 256);
        $result = $this->validateFieldLength($username);
        $this->assertFalse($result, "Le champ dépasse la limite autorisée !");
    }

    // Test 5: Account Lock
    private function attemptLogin($email, $password)
    {
        static $failedAttempts = 0;
        $lockedAccounts = ["user@example.com"];
        $correctPassword = "password123";

        if (in_array($email, $lockedAccounts)) {
            return false;
        }

        if ($password !== $correctPassword) {
            $failedAttempts++;
            if ($failedAttempts >= 3) {
                return false;
            }
            return false;
        }

        $failedAttempts = 0;
        return true;
    }

    public function testAccountLockAfterFailedAttempts()
    {
        $email = "user@example.com";
        for ($i = 0; $i < 3; $i++) {
            $result = $this->attemptLogin($email, "wrongpassword");
        }
        $this->assertFalse($result, "Le compte doit être verrouillé après 3 tentatives échouées !");
    }

    // Test 6: Session Management
    private function isSessionExpired()
    {
        $timeoutDuration = 1800;
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeoutDuration) {
            return true;
        }
        return false;
    }

    public function testSessionExpiration()
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['last_activity'] = time() - 1801;
        $result = $this->isSessionExpired();
        $this->assertTrue($result, "La session doit expirer après 30 minutes d'inactivité !");
    }

    public function testSessionRegeneration()
    {
        session_start();
        $oldSessionId = session_id();
        session_regenerate_id();
        $newSessionId = session_id();
        $this->assertNotEquals($oldSessionId, $newSessionId, "L'ID de session doit être régénéré !");
    }

    // Test 7: Input Validation
    public function sanitizeInput($input)
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    public function testSanitizeInput()
    {
        $input = "<script>alert('XSS');</script>";
        $sanitized = $this->sanitizeInput($input); 
        $this->assertEquals("&lt;script&gt;alert(&#039;XSS&#039;);&lt;/script&gt;", $sanitized, "L'entrée doit être correctement filtrée !");
    }
}
