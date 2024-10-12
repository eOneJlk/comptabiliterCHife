<?php
// Inclure la connexion à la base de données
require_once '../../dbconnexion.php';

// Vérifier si une recherche est effectuée
if (isset($_GET['query'])) {
    $search = $_GET['query'];

    // Requête SQL pour récupérer les noms d'agents correspondant à la recherche
    $sql = "SELECT nom, prenom FROM agents WHERE nom LIKE ? OR prenom LIKE ? LIMIT 10";
    $stmt = $conn->prepare($sql);
    $like_search = "%".$search."%";
    $stmt->bind_param("ss", $like_search, $like_search);
    $stmt->execute();
    $result = $stmt->get_result();

    $agents = [];
    while ($row = $result->fetch_assoc()) {
        $agents[] = $row['nom'] . ' ' . $row['prenom'];
    }

    // Retourner les résultats au format JSON
    echo json_encode($agents);
}

// Fermer la connexion
$conn->close();
?>
