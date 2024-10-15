<?php
require_once '../../dbconnexion.php';
session_start();
$roles_autorises = ['admin','stock'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles_autorises)) {
    // Rediriger vers une page d'erreur ou la page d'accueil si l'utilisateur n'a pas le bon rôle
    header("Location: ../../acces_refuse.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $stock_location = $_POST['stock_location'];
    $agent_id = $_SESSION['agent_id']; // Assurez-vous que l'ID de l'agent est stocké dans la session

    $sql = "INSERT INTO produits (date_entree, nom_produit, quantite, emplacement_stock, date_modification, id_modificateur) VALUES (?, ?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisi", $date, $product_name, $quantity, $stock_location, $agent_id);

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
