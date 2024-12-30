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
        <h1><?php echo isset($_GET['edit_id']) ? "Modifier l'utilisateur" : "Inscription"; ?></h1>
        <?php
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        
        try {
            $db = new SQLite3('notrebase.db');
        } catch (Exception $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }

     
        $isEdit = isset($_GET['edit_id']);
        $userData = null;

        if ($isEdit) {
            $edit_id = intval($_GET['edit_id']);
            $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindValue(':id', $edit_id, SQLITE3_INTEGER);
            $result = $stmt->execute();
            $userData = $result->fetchArray(SQLITE3_ASSOC);

            if (!$userData) {
                echo "<p>Utilisateur introuvable.</p>";
                exit;
            }
        }

       
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $username = $_POST['username'];
            $password = isset($_POST['password']) ? $_POST['password'] : null;

            if (empty($firstname) || empty($lastname) || empty($username) || ($isEdit ? false : empty($password))) {
                echo "<p>Veuillez remplir tous les champs requis.</p>";
            } else {
               
                if (!$isEdit || !empty($password)) {
                    if (!preg_match("/^(?=.*[A-Z])(?=.*\d).{8,}$/", $password)) {
                        echo "<p>Le mot de passe doit comporter au moins 8 caractères, une majuscule et un chiffre.</p>";
                        exit;
                    }
                }

                try {
                    if ($isEdit) {
                        
                        if (!empty($password)) {
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            $stmt = $db->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, username = :username, password = :password WHERE id = :id");
                            $stmt->bindValue(':password', $hashed_password, SQLITE3_TEXT);
                        } else {
                            $stmt = $db->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, username = :username WHERE id = :id");
                        }
                        $stmt->bindValue(':id', $edit_id, SQLITE3_INTEGER);
                    } else {
                     
                        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
                        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
                        $result = $stmt->execute();
                        $count = $result->fetchArray(SQLITE3_ASSOC)['COUNT(*)'];

                        if ($count > 0) {
                            echo "<p>Ce nom d'utilisateur est déjà utilisé. Veuillez en choisir un autre.</p>";
                            exit;
                        }

                     
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("INSERT INTO users (firstname, lastname, username, password, is_admin) VALUES (:firstname, :lastname, :username, :password, 0)");
                        $stmt->bindValue(':password', $hashed_password, SQLITE3_TEXT);
                    }

                    $stmt->bindValue(':firstname', $firstname, SQLITE3_TEXT);
                    $stmt->bindValue(':lastname', $lastname, SQLITE3_TEXT);
                    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
                    $stmt->execute();

                    echo "<p>" . ($isEdit ? "Utilisateur modifié avec succès !" : "Inscription réussie ") . "</p>";
                    echo "<p><a href='connexion.php'>Connectez vous maintenant</a></p>";
                    exit;
                } catch (Exception $e) {
                    echo "<p>Erreur : " . $e->getMessage() . "</p>";
                }
            }
        }
        ?>

        <form method="POST">
            <label for="firstname">Prénom:</label>
            <input type="text" id="firstname" name="firstname" value="<?php echo $isEdit ? htmlspecialchars($userData['firstname'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>

            <label for="lastname">Nom:</label>
            <input type="text" id="lastname" name="lastname" value="<?php echo $isEdit ? htmlspecialchars($userData['lastname'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>

            <label for="username">Nom d'utilisateur:</label>
            <input type="text" id="username" name="username" value="<?php echo $isEdit ? htmlspecialchars($userData['username'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>

            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" <?php echo $isEdit ? '' : 'required'; ?>>
            <small><?php echo $isEdit ? "Laissez vide pour ne pas modifier le mot de passe." : "Doit contenir au moins 8 caractères, une majuscule et un chiffre."; ?></small>

            <button type="submit"><?php echo $isEdit ? 'Modifier' : "S'inscrire"; ?></button>
        </form>
    </div>
</body>
</html>
