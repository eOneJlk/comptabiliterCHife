<?php
session_start();
require_once '../../dbconnexion.php';

// Vérification de l'autorisation
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'caissier')) {
    header("Location: ../../acces_refuse.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $transaction_id = $_POST['transaction_id'];
    $date = $_POST['date'];
    $type = $_POST['type'];
    $montant = $_POST['montant'];
    $details = $_POST['details'];
    $compte = $_POST['compte'];

    $query = "UPDATE transactions_caisse SET date = ?, type = ?, montant = ?, details = ?, compte = ? WHERE id = ? AND etat = 'pending'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdssi", $date, $type, $montant, $details, $compte, $transaction_id);

    if ($stmt->execute()) {
        echo "Transaction mise à jour avec succès.";
        // Rediriger vers la page de la caisse après un court délai
        header("Refresh: 2; URL=caisse.php");
    } else {
        echo "Erreur lors de la mise à jour de la transaction : " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Méthode non autorisée.";
}

$conn->close();
?>
