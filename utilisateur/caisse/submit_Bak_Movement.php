<?php
require_once '../../dbconnexion.php';

// Récupération des données du formulaire
$transaction_type = $_POST['transaction_type'];
$amount = $_POST['amount'];
$date = $_POST['date'];
$invoice_number = $_POST['invoice_number'];
$slip_number = $_POST['slip_number'];

// Préparation de la requête SQL
$sql = "INSERT INTO transactions_bancaires (transaction_type, amount, date, invoice_number, slip_number) VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sdsss", $transaction_type, $amount, $date, $invoice_number, $slip_number);

// Exécution de la requête
if ($stmt->execute()) {
    echo "Transaction bancaire enregistrée avec succès";
} else {
    echo "Erreur lors de l'enregistrement de la transaction bancaire : " . $stmt->error;
}

$stmt->close();

// Redirection vers la page principale après 3 secondes
header("refresh:3;url=caisse.php");
?>
