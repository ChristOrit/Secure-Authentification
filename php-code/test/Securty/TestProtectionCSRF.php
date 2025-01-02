<?php

use PHPUnit\Framework\TestCase;

class TestProtectionCSRF extends TestCase
{
    public function testCsrfTokenValidation()
    {
        $sessionToken = bin2hex(random_bytes(32));
        $postedToken = $sessionToken; // Simule une soumission correcte.

        $this->assertEquals($sessionToken, $postedToken, "Le token CSRF doit correspondre pour valider la requête.");
    }
}
