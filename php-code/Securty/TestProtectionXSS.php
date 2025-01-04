<?php

use PHPUnit\Framework\TestCase;

class TestProtectionXSS extends TestCase
{
    public function testXssProtection()
    {
        $username = "<script>alert('XSS')</script>";
        $output = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

        $this->assertStringNotContainsString("<script>", $output, "Les scripts ne doivent pas être interprétés.");
        $this->assertStringContainsString("&lt;script&gt;", $output, "Les caractères spéciaux doivent être encodés.");
    }
}

