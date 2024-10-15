<?php
session_start();
require_once '../../dbconnexion.php';

// Vérification de l'autorisation
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'caissier')) {
    header("Location: ../../acces_refuse.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des données du formulaire
    $transaction_id = $_POST['transaction_id'];
    $date = $_POST['date'];
    $transaction_type = $_POST['transaction_type'];
    $amount = $_POST['amount'];
    $invoice_number = $_POST['invoice_number'];
    $slip_number = $_POST['slip_number'];

    // Validation des données (vous pouvez ajouter plus de validations si nécessaire)
    if (empty($transaction_id) || empty($date) || empty($transaction_type) || empty($amount)) {
        echo "Tous les champs obligatoires doivent être remplis.";
        exit();
    }

    // Préparation de la requête SQL
    $query = "UPDATE transactions_bancaires 
              SET date = ?, transaction_type = ?, amount = ?, invoice_number = ?, slip_number = ? 
              WHERE id = ? AND etat = 'pending'";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdssi", $date, $transaction_type, $amount, $invoice_number, $slip_number, $transaction_id);

    // Exécution de la requête
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "La transaction bancaire a été mise à jour avec succès.";
            // Redirection vers la page de la caisse après un court délai
            header("Refresh: 2; URL=caisse.php");
        } else {
            echo "Aucune modification n'a été effectuée. La transaction est peut-être déjà approuvée ou n'existe pas.";
        }
    } else {
        echo "Erreur lors de la mise à jour de la transaction : " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Méthode non autorisée.";
}

$conn->close();
?>
