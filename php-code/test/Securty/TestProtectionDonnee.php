<?php

use PHPUnit\Framework\TestCase;

class TestProtectionDonnee extends TestCase
{
    public function testSensitiveDataEncryption()
    {
        $data = "Information sensible";
        $key = 'secretkey1234567';
        $encryptedData = openssl_encrypt($data, 'AES-128-ECB', $key);
        $decryptedData = openssl_decrypt($encryptedData, 'AES-128-ECB', $key);

        $this->assertNotEquals($data, $encryptedData, "Les données ne doivent pas être stockées en clair.");
        $this->assertEquals($data, $decryptedData, "Les données doivent être récupérées correctement après chiffrement.");
    }
}
