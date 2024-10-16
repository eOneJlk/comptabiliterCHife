<?php
session_start(); // Si tu utilises une session pour gérer l'authentification des agents

// Inclure la connexion à la base de données
include '../../dbconnexion.php';

// Vérification si les données du formulaire sont soumises
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $nom = $_POST['nom'];
    $nombre_check_out = $_POST['nombre_check_out'];
    $nombre_check_in = $_POST['nombre_check_in'];
    $chambre_disponible = $_POST['chambre_disponible'];
    $contenu = $_POST['contenu'];
    $entree_cash = $_POST['entree_cash'];
    $credit = $_POST['credit'];
    $entree_airtel_money = $_POST['entree_airtel_money'];
    $entree_carte_pos = $_POST['entree_carte_pos'];
    $agent_id = $_SESSION['agent_id']; // On suppose que l'agent est connecté et son ID est stocké dans la session

    // Vérification si un rapport existe déjà pour cette date
    $check_sql = "SELECT COUNT(*) as count FROM rapports WHERE date = ? AND agent_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $date, $agent_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        echo "Un rapport a déjà été soumis pour cette date.";
        header("Location: agent.php?error=duplicate");
        exit();
    }

    // Préparation de la requête d'insertion
    $sql = "INSERT INTO rapports (date, nom, nombre_check_out, nombre_check_in, chambre_disponible, contenu, entree_cash, credit, entree_airtel_money, entree_carte_pos, agent_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);

    // Liaison des paramètres et exécution de la requête
    $stmt->bind_param("ssiiissdddi", $date, $nom, $nombre_check_out, $nombre_check_in, $chambre_disponible, $contenu, $entree_cash, $credit, $entree_airtel_money, $entree_carte_pos, $agent_id);
    
    if ($stmt->execute()) {
        echo "Rapport soumis avec succès.";
        header("Location: agent.php");
        exit();
    } else {
        echo "Erreur lors de la soumission : " . $conn->error;
    }
    
    // Fermeture de la déclaration et de la connexion
    $stmt->close();
    $conn->close();
}
?>
