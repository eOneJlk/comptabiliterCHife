<?php
session_start();
require_once 'dbconnexion.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['agent_id'])) {
    header("Location: login.php");
    exit();
}

// Récupérer les informations de l'agent connecté
$agent_id = $_SESSION['agent_id'];
$sql = "SELECT * FROM agents WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$result = $stmt->get_result();
$agent = $result->fetch_assoc();

// Traitement du formulaire de mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Mise à jour du profil
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];

        $update_sql = "UPDATE agents SET nom = ?, prenom = ?, email = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $nom, $prenom, $email, $agent_id);
        $update_stmt->execute();

        // Rediriger pour rafraîchir les données
        header("Location: profil.php");
        exit();
    } elseif (isset($_POST['change_password'])) {
        // Changement de mot de passe
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (password_verify($current_password, $agent['mot_de_passe'])) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $password_sql = "UPDATE agents SET mot_de_passe = ? WHERE id = ?";
                $password_stmt = $conn->prepare($password_sql);
                $password_stmt->bind_param("si", $hashed_password, $agent_id);
                $password_stmt->execute();

                $success_message = "Mot de passe mis à jour avec succès.";
            } else {
                $error_message = "Les nouveaux mots de passe ne correspondent pas.";
            }
        } else {
            $error_message = "Mot de passe actuel incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil de l'utilisateur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .logout-btn {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            cursor: pointer;
            border-radius: 5px;
        }
        main {
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }
        h1, h2 {
            color: #333;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
        form {
            background-color: #fff;
            padding: 1rem;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            cursor: pointer;
            border-radius: 3px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Chife Hotel</div>
        <a href="logout.php" class="logout-btn">Déconnexion</a>
    </header>

    <main>
        <h1>Profil de <?php echo htmlspecialchars($agent['prenom'] . ' ' . $agent['nom']); ?></h1>

        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <section id="profile-info">
            <h2>Informations du profil</h2>
            <form action="profil.php" method="POST">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($agent['nom']); ?>" required>

                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($agent['prenom']); ?>" required>

                <label for="email">Email :</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($agent['email']); ?>" required>

                <button type="submit" name="update_profile">Mettre à jour le profil</button>
            </form>
        </section>

        <section id="change-password">
            <h2>Changer le mot de passe</h2>
            <form action="profil.php" method="POST">
                <label for="current_password">Mot de passe actuel :</label>
                <input type="password" id="current_password" name="current_password" required>

                <label for="new_password">Nouveau mot de passe :</label>
                <input type="password" id="new_password" name="new_password" required>

                <label for="confirm_password">Confirmer le nouveau mot de passe :</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <button type="submit" name="change_password">Changer le mot de passe</button>
            </form>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <p>&copy; 2024 Chife Hotel. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
