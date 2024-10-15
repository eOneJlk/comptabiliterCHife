<?php
session_start();
require_once '../../dbconnexion.php';

// Vérification du rôle et de l'authentification
$roles_autorises = ['admin', 'gerant'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles_autorises)) {
    header("Location: ../../acces_refuse.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $agent_id = $_POST['agent_id'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo "Les mots de passe ne correspondent pas.";
        exit();
    }

    // Hachage du nouveau mot de passe
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Vérification de l'existence de l'agent
    $check_query = "SELECT id FROM agents WHERE id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $agent_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        echo "Agent non trouvé.";
        $check_stmt->close();
        exit();
    }
    $check_stmt->close();

    // Mise à jour du mot de passe
    $update_query = "UPDATE agents SET password = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $hashed_password, $agent_id);

    if ($update_stmt->execute()) {
        echo "Mot de passe mis à jour avec succès.";
    } else {
        echo "Erreur lors de la mise à jour du mot de passe : " . $update_stmt->error;
    }

    $update_stmt->close();
} else {
    echo "Méthode non autorisée.";
}

$conn->close();
?>
