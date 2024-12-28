<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" href="pico.min.css">
</head>
<body>
    <div class="container">
        <?php
        session_start();
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        echo "<h1>Bienvenue, " . htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') . " !</h1>";
        echo "<h2>Page de gestion des utilisateurs</h2>";

        try {
            $db = new SQLite3('notrebase.db');
            if (!$db) {
                die("Erreur de connexion à la base de données : " . $db->lastErrorMsg());
            }

            
            if (isset($_GET['delete_id'])) {
                $delete_id = intval($_GET['delete_id']);
                $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
                $stmt->bindValue(':id', $delete_id, SQLITE3_INTEGER);
                if ($stmt->execute()) {
                    echo "<p>Utilisateur supprimé avec succès.</p>";
                } else {
                    echo "<p>Erreur lors de la suppression de l'utilisateur.</p>";
                }
            }

            $result = $db->query("SELECT id, username, is_admin FROM users");
            echo "<table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom d'utilisateur</th>
                            <th>Administrateur</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>";

            while ($user = $result->fetchArray(SQLITE3_ASSOC)) {
                echo "<tr>
                        <td>" . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . "</td>
                        <td>" . htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') . "</td>
                        <td>" . ($user['is_admin'] ? 'Oui' : 'Non') . "</td>
                        <td>
                            <a href='inscription.php?edit_id=" . $user['id'] . "'>Modifier</a> | 
                            <a href='admin-gestion.php?delete_id=" . $user['id'] . "' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');\">Supprimer</a>
                        </td>
                      </tr>";
            }

            echo "</tbody>
                  </table>";
            echo "<p><a href='inscription.php'>Ajouter un nouvel utilisateur</a></p>";
        } catch (Exception $e) {
            echo "<p>Erreur : " . $e->getMessage() . "</p>";
        }
        ?>
    </div>
</body>
</html>
