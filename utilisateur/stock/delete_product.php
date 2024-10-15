<?php
require_once '../../dbconnexion.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $agent_id = $_SESSION['agent_id'];

    $sql = "UPDATE produits SET date_suppression = NOW(), id_modificateur = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $agent_id, $product_id);

    if ($stmt->execute()) {
        echo "Produit supprimé avec succès.";
    } else {
        echo "Erreur lors de la suppression du produit : " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}

header("Location: stock.php");
exit();
?>

