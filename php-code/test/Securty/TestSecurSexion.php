<?php

use PHPUnit\Framework\TestCase;

class TestSecurSexion extends TestCase
{
    public function testSessionConfiguration()
    {
        ini_set('session.cookie_secure', '1');
        ini_set('session.cookie_httponly', '1');

        $this->assertEquals('1', ini_get('session.cookie_secure'), "Les cookies doivent être sécurisés.");
        $this->assertEquals('1', ini_get('session.cookie_httponly'), "Les cookies doivent être accessibles uniquement via HTTP.");
    }
}
