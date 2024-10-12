<?php
include('../../dbconnexion.php');

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