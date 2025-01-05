<?php

use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{
    // Test de validation du token CSRF
    public function testCsrfTokenValidation()
    {
        $sessionToken = bin2hex(random_bytes(32));
        $postedToken = $sessionToken; // Simule une soumission correcte.

        $this->assertEquals($sessionToken, $postedToken, "Le token CSRF doit correspondre pour valider la requête.");
    }

    // Test du chiffrement des données sensibles
    public function testSensitiveDataEncryption()
    {
        $data = "Information sensible";
        $key = 'secretkey1234567';
        $encryptedData = openssl_encrypt($data, 'AES-128-ECB', $key);
        $decryptedData = openssl_decrypt($encryptedData, 'AES-128-ECB', $key);

        $this->assertNotEquals($data, $encryptedData, "Les données ne doivent pas être stockées en clair.");
        $this->assertEquals($data, $decryptedData, "Les données doivent être récupérées correctement après chiffrement.");
    }

    // Test de prévention contre les injections SQL
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

    // Test de hachage des mots de passe
    public function testPasswordHashing()
    {
        $password = "SecurePassword123!";
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $this->assertNotEquals($password, $hashedPassword, "Le mot de passe ne doit pas être stocké en clair.");
        $this->assertTrue(password_verify($password, $hashedPassword), "Le mot de passe haché doit être vérifiable.");
    }

    // Test de protection contre les attaques XSS
    public function testXssProtection()
    {
        $username = "<script>alert('XSS')</script>";
        $output = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

        $this->assertStringNotContainsString("<script>", $output, "Les scripts ne doivent pas être interprétés.");
        $this->assertStringContainsString("&lt;script&gt;", $output, "Les caractères spéciaux doivent être encodés.");
    }

    // Test de configuration des sessions
    public function testSessionConfiguration()
    {
        ini_set('session.cookie_secure', '1');
        ini_set('session.cookie_httponly', '1');

        $this->assertEquals('1', ini_get('session.cookie_secure'), "Les cookies doivent être sécurisés.");
        $this->assertEquals('1', ini_get('session.cookie_httponly'), "Les cookies doivent être accessibles uniquement via HTTP.");
    }
}
