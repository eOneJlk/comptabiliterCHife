<?php
session_start();
require_once '../../dbconnexion.php';

// Vérification de l'autorisation
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'caissier')) {
    header("Location: ../../acces_refuse.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['transaction_id'])) {
    $transaction_id = $_POST['transaction_id'];
    
    // Supprimer la transaction
    $query = "DELETE FROM transactions_caisse WHERE id = ? AND etat = 'pending'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $transaction_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Transaction supprimée avec succès.";
            header("Location: caisse.php");
        } else {
            echo "Aucune transaction en attente trouvée avec cet ID.";
        }
    } else {
        echo "Erreur lors de la suppression de la transaction.";
    }
    
    $stmt->close();
} else {
    echo "ID de transaction non fourni.";
}

$conn->close();
?>
