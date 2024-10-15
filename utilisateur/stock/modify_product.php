<?php
require_once '../../dbconnexion.php';
session_start();
$roles_autorises = ['admin','stock'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles_autorises)) {
    // Rediriger vers une page d'erreur ou la page d'accueil si l'utilisateur n'a pas le bon rôle
    header("Location: ../../acces_refuse.php");
    exit();
}
// Vérifier si l'utilisateur est connecté et a les droits nécessaires
if (!isset($_SESSION['agent_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $new_quantity = $_POST['new_quantity'];

    // Vérifier que les valeurs sont valides
    if (!is_numeric($product_id) || !is_numeric($new_quantity) || $new_quantity < 0) {
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
        exit;
    }

    // Mettre à jour la quantité du produit
    $sql = "UPDATE produits SET quantite = ?, date_modification = NOW(), id_modificateur = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $new_quantity, $_SESSION['agent_id'], $product_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}

$conn->close();

