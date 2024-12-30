<?php

use PHPUnit\Framework\TestCase;

class DevPrinTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        // Connexion à la base de données SQLite
        $this->db = new SQLite3('notrebase.db');

        // Réinitialiser la table users avant chaque test
        $this->db->exec("DELETE FROM users");
    }

    protected function tearDown(): void
    {
        // Fermer la connexion après chaque test
        $this->db->close();
    }

    public function testCreationCompteReussie()
    {
        // Données simulées pour le formulaire
        $_POST['firstname'] = 'Jane';
        $_POST['lastname'] = 'Doe';
        $_POST['username'] = 'janedoe';
        $_POST['password'] = 'SecurePass123';

        // Inclure le fichier contenant la logique d'inscription
        ob_start();
        include 'inscription.php'; // Assurez-vous que ce fichier est au bon chemin
        $output = ob_get_clean();

        // Vérifier que le message de confirmation est affiché
        $this->assertStringContainsString("Inscription réussie", $output);

        // Vérifier les données dans la base
        $result = $this->db->query("SELECT * FROM users WHERE username = 'janedoe'");
        $user = $result->fetchArray(SQLITE3_ASSOC);

        $this->assertNotFalse($user, "L'utilisateur doit exister dans la base.");
        $this->assertEquals('Jane', $user['firstname']);
        $this->assertEquals('Doe', $user['lastname']);
        $this->assertTrue(password_verify('SecurePass123', $user['password']), "Le mot de passe doit être correctement haché.");
    }
}
