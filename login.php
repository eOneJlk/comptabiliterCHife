<?php
session_start();
require_once 'dbconnexion.php';

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['agent_id'])) {
    header("Location: index.php");
    exit();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer et sécuriser les données du formulaire
    $email = htmlspecialchars($conn->real_escape_string($_POST['email']));
    $mot_de_passe = $_POST['mot_de_passe'];

    // Requête SQL pour récupérer l'agent par email, y compris le rôle
    $sql = "SELECT id, email, mot_de_passe, role FROM agents WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $db_email, $db_mot_de_passe, $role);
            $stmt->fetch();

            if (password_verify($mot_de_passe, $db_mot_de_passe)) {
                // Si le mot de passe est correct, démarrer une session
                session_start();
                $_SESSION['agent_id'] = $id;
                $_SESSION['email'] = $db_email;
                $_SESSION['role'] = $role; 

                // Rediriger selon le rôle
                if ($role === 'admin') {
                    header("Location: index.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error_message = "Mot de passe incorrect.";
            }
        } else {
            $error_message = "Aucun compte trouvé avec cet email.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chife Hotel Login</title>
    <link rel="stylesheet" href="assets/css/style_login.css">
</head>
<body>
<div class="login-container">
    <div class="logo">
        <img src="assets/img/logo_2.png" alt="Chife Hotel Logo">
    </div>
    <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="background-color: #f9f9f9; padding: 20px; border-radius: 5px; max-width: 400px; margin: 20px auto; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="email" style="font-weight: bold; display: block; margin-bottom: 8px;">Email</label>
            <input type="email" id="email" name="email" required style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="mot_de_passe" style="font-weight: bold; display: block; margin-bottom: 8px;">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer;">Se connecter</button>
    </form>
    <footer>
        <div class="footer">
            <a href="https://www.facebook.com/chifehotel"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com/chifehotel"><i class="fab fa-instagram"></i></a>
            <a href="https://www.twitter.com/chifehotel"><i class="fab fa-twitter"></i></a>
        </div>
    </footer>
</div>
</body>
</html>
