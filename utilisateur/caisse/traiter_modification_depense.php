<?php
session_start();
require_once '../../dbconnexion.php';

// Vérification de l'autorisation
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'caissier')) {
    header("Location: ../../acces_refuse.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des données du formulaire
    $depense_id = $_POST['depense_id'];
    $date = $_POST['date'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];

    // Validation des données (vous pouvez ajouter plus de validations si nécessaire)
    if (empty($depense_id) || empty($date) || empty($nom) || empty($amount)) {
        echo "Tous les champs obligatoires doivent être remplis.";
        exit();
    }

    // Préparation de la requête SQL
    $query = "UPDATE depenses 
              SET date = ?, nom = ?, description = ?, amount = ? 
              WHERE id = ? AND etat = 'pending'";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssdi", $date, $nom, $description, $amount, $depense_id);

    // Exécution de la requête
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "La dépense a été mise à jour avec succès.";
            // Redirection vers la page de la caisse après un court délai
            header("Refresh: 2; URL=caisse.php");
        } else {
            echo "Aucune modification n'a été effectuée. La dépense est peut-être déjà approuvée ou n'existe pas.";
        }
    } else {
        echo "Erreur lors de la mise à jour de la dépense : " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Méthode non autorisée.";
}

$conn->close();
?>
