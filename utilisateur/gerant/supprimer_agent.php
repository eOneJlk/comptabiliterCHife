<?php
require_once '../../dbconnexion.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['agent_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Vérifier si l'ID de l'agent à supprimer est fourni
if (!isset($_GET['id'])) {
    header("Location: personnel.php");
    exit();
}

$agent_id = $_GET['id'];

// Vérifier si l'agent existe
$check_sql = "SELECT id FROM agents WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $agent_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo "Agent non trouvé.";
    exit();
}

// Supprimer l'agent
$delete_sql = "DELETE FROM agents WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $agent_id);

if ($delete_stmt->execute()) {
    echo "Agent supprimé avec succès.";
    header("Location: personnel.php");
    exit();
} else {
    echo "Erreur lors de la suppression de l'agent : " . $conn->error;
}

$conn->close();
?>