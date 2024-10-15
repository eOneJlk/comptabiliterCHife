<?php
// Inclure la connexion à la base de données
require_once '../../dbconnexion.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['agent_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: ../../login.php");
    exit();
}

// Vérifier le rôle de l'utilisateur
$roles_autorises = ['admin']; // Ajoutez ou retirez des rôles selon vos besoins
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles_autorises)) {
    // Rediriger vers une page d'erreur ou la page d'accueil si l'utilisateur n'a pas le bon rôle
    header("Location: ../../acces_refuse.php");
    exit();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer et sécuriser les données du formulaire
    $date = htmlspecialchars($conn->real_escape_string($_POST['date']));
    $product_name = htmlspecialchars($conn->real_escape_string($_POST['product_name']));
    $quantity = htmlspecialchars($conn->real_escape_string($_POST['quantity']));
    $stock_location = htmlspecialchars($conn->real_escape_string($_POST['stock_location']));

    // Requête SQL pour insérer le produit dans la base de données
    $sql = "INSERT INTO produits (date_entree, nom_produit, quantite, emplacement_stock) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $date, $product_name, $quantity, $stock_location);

    if ($stmt->execute()) {
        echo "Produit enregistré avec succès.";
    } else {
        echo "Erreur lors de l'enregistrement du produit : " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}

header("Location: stock.php");
exit();
?>
