<?php
include('../../dbconnexion.php');

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $stock_location = $_POST['stock_location'];
    $date_sortie = $_POST['date_sortie'];

    // Vérifier si le produit existe dans la base de données
    $sql = "SELECT quantite FROM produits WHERE nom_produit='$product_name' AND emplacement_stock='$stock_location'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Récupérer la quantité actuelle du produit
        $row = $result->fetch_assoc();
        $current_quantity = $row['quantite'];

        // Vérifier si la quantité à retirer est disponible
        if ($quantity <= $current_quantity) {
            // Mettre à jour la quantité du produit et la date de sortie
            $new_quantity = $current_quantity - $quantity;
            $update_sql = "UPDATE produits SET quantite=$new_quantity, date_sortie='$date_sortie' WHERE nom_produit='$product_name' AND emplacement_stock='$stock_location'";

            if ($conn->query($update_sql) === TRUE) {
                echo "Le produit a été retiré avec succès.";
                header('Location: stock.php');
            } else {
                echo "Erreur lors de la mise à jour : " . $conn->error;
            }
        } else {
            echo "Quantité insuffisante pour retirer $quantity.";
        }
    } else {
        echo "Le produit n'existe pas.";
    }

    // Fermer la connexion
    $conn->close();
}
?>
