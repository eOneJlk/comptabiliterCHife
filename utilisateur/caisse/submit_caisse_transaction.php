<?php
include '../../dbconnexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des données du formulaire
    $date = $_POST['date'];
    $type = $_POST['type'];
    $montant = $_POST['montant'];
    $details = $_POST['details'];
    $compte = $_POST['compte'];

    // Préparer et exécuter la requête SQL pour insérer la transaction
    $sql = "INSERT INTO transactions_caisse (date, type, montant, details, compte) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdss", $date, $type, $montant, $details, $compte);

    if ($stmt->execute()) {
        echo "Transaction enregistrée avec succès.";
        header("location:caisse.php");
        exit();
    } else {
        echo "Erreur: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
