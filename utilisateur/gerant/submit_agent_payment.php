<?php
require_once '../../dbconnexion.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sécurisation des entrées utilisateur
    $date = htmlspecialchars($conn->real_escape_string($_POST['date']));
    $agent_name = htmlspecialchars($conn->real_escape_string($_POST['agent_name']));
    $category = htmlspecialchars($conn->real_escape_string($_POST['category']));
    $amount = htmlspecialchars($conn->real_escape_string($_POST['amount']));

    // Requête SQL pour insérer le paiement dans la base de données
    $sql = "INSERT INTO paiements (date_paiement, nom_agent, categorie, montant)
            VALUES (?, ?, ?, ?)";

    // Préparation de la requête
    if ($stmt = $conn->prepare($sql)) {
        // Lier les paramètres
        $stmt->bind_param("sssd", $date, $agent_name, $category, $amount);

        // Exécuter la requête
        if ($stmt->execute()) {
            echo "Paiement enregistré avec succès!";
            header('Location: gerant.html');
        } else {
            echo "Erreur lors de l'enregistrement du paiement : " . $conn->error;
        }

        // Fermer la requête préparée
        $stmt->close();
    } else {
        echo "Erreur lors de la préparation de la requête : " . $conn->error;
    }

    // Fermer la connexion à la base de données
    $conn->close();
}

// Afficher la liste des paiements
?>

<!-- Liste des paiements -->

