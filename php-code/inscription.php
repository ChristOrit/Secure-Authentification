<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="pico.min.css">
</head>
<body>
    <div class="container">
        <h1>Inscription</h1>
        <form method="POST">
            <label for="firstname">Prénom:</label>
            <input type="text" id="firstname" name="firstname" required>

            <label for="lastname">Nom:</label>
            <input type="text" id="lastname" name="lastname" required>
            
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">S'inscrire</button>
        </form>
    </div>
</body>
</html>

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (strlen($password) < 8 || strlen($password) > 15) {
        echo "Le mot de passe doit comporter entre 8 et 15 caractères.<br>";
    } elseif (strlen($username) < 6 || strlen($username) > 10) {
        echo "Le nom d'utilisateur doit comporter entre 6 et 10 caractères.<br>";
    } elseif ($username == $firstname || $username == $lastname) {
        echo "Le nom d'utilisateur ne peut pas être le même que le prénom ou le nom.<br>";
    } elseif ($firstname == $lastname) {
        echo "Le prénom et le nom ne peuvent pas être identiques.<br>";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $db = new SQLite3('notrebase.db');

        if (!$db) {
            die("Erreur de connexion à la base de données : " . $db->lastErrorMsg());
        }

        $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
        if (!$result->fetchArray()) {
            die("La table 'users' n'existe pas dans la base de données.");
        }

        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $existingUser = $stmt->execute()->fetchArray();

        if ($existingUser) {
            echo "Erreur : Ce nom d'utilisateur est déjà pris.<br>";
        } else {
            $stmt = $db->prepare("INSERT INTO users (firstname, lastname, username, password) VALUES (:firstname, :lastname, :username, :password)");
            $stmt->bindValue(':firstname', $firstname, SQLITE3_TEXT);
            $stmt->bindValue(':lastname', $lastname, SQLITE3_TEXT);
            $stmt->bindValue(':username', $username, SQLITE3_TEXT);
            $stmt->bindValue(':password', $hashedPassword, SQLITE3_TEXT);

            if ($stmt->execute()) {
                echo "Utilisateur inscrit avec succès.<br>";
            } else {
                echo "Erreur lors de l'insertion : " . $db->lastErrorMsg() . "<br>";
            }
        }
    }
}
?>
