<?php
include('../../dbconnexion.php');
session_start();
$roles_autorises = ['admin','stock'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles_autorises)) {
    // Rediriger vers une page d'erreur ou la page d'accueil si l'utilisateur n'a pas le bon rôle
    header("Location: ../../acces_refuse.php");
    exit();
}
if(isset($_POST["query"])) {
    $output = '';
    $query = "SELECT DISTINCT nom_produit FROM produits WHERE nom_produit LIKE '%" . $_POST["query"] . "%' LIMIT 10";
    $result = $conn->query($query);
    $output = '<ul class="list-unstyled">';
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $output .= '<li class="suggestion">'.$row["nom_produit"].'</li>';
        }
    } else {
        $output .= '<li>Produit non trouvé</li>';
    }
    $output .= '</ul>';
    echo $output;
}

$conn->close();
?>