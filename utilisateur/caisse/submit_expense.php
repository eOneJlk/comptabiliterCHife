<?php
include '../../dbconnexion.php';

// Vérifier si les données ont été soumises via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];

    // Préparer la requête d'insertion
    $stmt = $conn->prepare("INSERT INTO depenses (date, nom, description, amount) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $date, $nom, $description, $amount);

    // Exécuter la requête
    if ($stmt->execute()) {
        echo "Nouvelle dépense ajoutée avec succès.";
        header("Location: caisse.php");
        exit();
    } else {
        echo "Erreur: " . $stmt->error;
    }

    // Fermer la connexion
    $stmt->close();
    $conn->close();
}
?>
