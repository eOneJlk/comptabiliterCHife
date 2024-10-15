<?php
require_once '../../dbconnexion.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $stock_location = $_POST['stock_location'];
    $agent_id = $_SESSION['agent_id'];

    $sql = "UPDATE produits SET quantite = ?, emplacement_stock = ?, date_modification = NOW(), id_modificateur = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isii", $quantity, $stock_location, $agent_id, $product_id);

    if ($stmt->execute()) {
        echo "Produit mis à jour avec succès.";
    } else {
        echo "Erreur lors de la mise à jour du produit : " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}

header("Location: stock.php");
exit();
?>

