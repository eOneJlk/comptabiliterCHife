<?php
session_start();
require_once '../../dbconnexion.php';

// Vérifier si l'agent est connecté
if (!isset($_SESSION['agent_id'])) {
    header("Location: login.php");
    exit();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mot_de_passe_actuel = $_POST['mot_de_passe_actuel'];
    $nouveau_mot_de_passe = $_POST['nouveau_mot_de_passe'];
    $confirmer_mot_de_passe = $_POST['confirmer_mot_de_passe'];

    // Vérifier si les mots de passe correspondent
    if ($nouveau_mot_de_passe !== $confirmer_mot_de_passe) {
        echo "Les mots de passe ne correspondent pas.";
        exit();
    }

    // Récupérer l'ID de l'agent depuis la session
    $agent_id = $_SESSION['agent_id'];

    // Requête SQL pour récupérer l'ancien mot de passe
    $sql = "SELECT mot_de_passe FROM agents WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $agent_id);
        $stmt->execute();
        $stmt->bind_result($db_mot_de_passe);
        $stmt->fetch();

        // Vérifier si le mot de passe actuel est correct
        if (password_verify($mot_de_passe_actuel, $db_mot_de_passe)) {
            // Hacher le nouveau mot de passe
            $nouveau_mot_de_passe_hash = password_hash($nouveau_mot_de_passe, PASSWORD_BCRYPT);

            // Mettre à jour le mot de passe dans la base de données
            $sql_update = "UPDATE agents SET mot_de_passe = ? WHERE id = ?";
            if ($stmt_update = $conn->prepare($sql_update)) {
                $stmt_update->bind_param("si", $nouveau_mot_de_passe_hash, $agent_id);
                if ($stmt_update->execute()) {
                    echo "Mot de passe changé avec succès!";
                } else {
                    echo "Erreur lors de la mise à jour du mot de passe.";
                }
                $stmt_update->close();
            }
        } else {
            echo "Le mot de passe actuel est incorrect.";
        }

        $stmt->close();
    }
}

// Fermer la connexion
$conn->close();
?>

<!-- Formulaire de changement de mot de passe -->
<form action="change_password.php" method="post">
    <label for="mot_de_passe_actuel">Mot de passe actuel:</label>
    <input type="password" id="mot_de_passe_actuel" name="mot_de_passe_actuel" required><br>
    
    <label for="nouveau_mot_de_passe">Nouveau mot de passe:</label>
    <input type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe" required><br>
    
    <label for="confirmer_mot_de_passe">Confirmer le nouveau mot de passe:</label>
    <input type="password" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" required><br>
    
    <button type="submit">Changer le mot de passe</button>
</form>
