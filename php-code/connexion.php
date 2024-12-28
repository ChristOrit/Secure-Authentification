<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="pico.min.css">
</head>
<body>
    <div class="container">
        <h1>Connexion</h1>
        <form method="POST">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Se connecter</button>
        </form>
    </div>

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $db = new SQLite3('notrebase.db');

        if (!$db) {
            die("Erreur de connexion à la base de données : " . $db->lastErrorMsg());
        }

        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();

        $user = $result->fetchArray(SQLITE3_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['username'] = $username;

            if ($user['is_admin'] == 1) {
                header("Location: admin-gestion.php");
                exit;
            } else {
                echo "<p>Compte créé avec succès. Bienvenue, $username!</p>";
            }
        } else {
            echo "<p>Nom d'utilisateur ou mot de passe incorrect.</p>";
        }
    } catch (Exception $e) {
        echo "<p>Erreur : " . $e->getMessage() . "</p>";
    }
}
?>
</body>
</html>
