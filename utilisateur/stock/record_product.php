<?php
// Inclure la connexion à la base de données
include('../../dbconnexion.php');

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_entree = $_POST['date'];
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $stock_location = $_POST['stock_location'];

    // Insérer le produit dans la base de données
    $sql = "INSERT INTO produits (date_entree, nom_produit, quantite, emplacement_stock) VALUES ('$date_entree', '$product_name', $quantity, '$stock_location')";

    if ($conn->query($sql) === TRUE) {
        echo "Produit enregistré avec succès.";
        header('Location: stock.php');
    } else {
        echo "Erreur lors de l'enregistrement : " . $conn->error;
    }

    // Fermer la connexion
    $conn->close();
}
?>
