<?php
use PHPUnit\Framework\TestCase;

class DeconnexionTest extends TestCase
{
    public function testDeconnexion()
    {
      
        $_SESSION['user_id'] = 12;

        
        session_destroy();

        
        $this->assertNull($_SESSION['user_id']); 
    }
}
