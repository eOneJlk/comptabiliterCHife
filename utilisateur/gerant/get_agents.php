<?php
require_once '../../dbconnexion.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['agent_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: ../../login.php");
    exit();
}
$roles_autorises = ['admin','gerant'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles_autorises)) {
    // Rediriger vers une page d'erreur ou la page d'accueil si l'utilisateur n'a pas le bon rôle
    header("Location: ../../acces_refuse.php");
    exit();
}
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
