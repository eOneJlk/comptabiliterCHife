<?php

require_once '../../dbconnexion.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['agent_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: ../../login.php");
    exit();
}

// Vérifier le rôle de l'utilisateur
$roles_autorises = ['admin','gerant'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles_autorises)) {
    // Rediriger vers une page d'erreur ou la page d'accueil si l'utilisateur n'a pas le bon rôle
    header("Location: ../../acces_refuse.php");
    exit();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sécurisation des entrées utilisateur
    $nom = htmlspecialchars($conn->real_escape_string($_POST['nom']));
    $prenom = htmlspecialchars($conn->real_escape_string($_POST['prenom']));
    $email = htmlspecialchars($conn->real_escape_string($_POST['email']));
    $telephone = htmlspecialchars($conn->real_escape_string($_POST['telephone']));
    $adresse = htmlspecialchars($conn->real_escape_string($_POST['adresse']));
    $departement = htmlspecialchars($conn->real_escape_string($_POST['departement']));
    $role = htmlspecialchars($conn->real_escape_string($_POST['role']));
    $date_embauche = htmlspecialchars($conn->real_escape_string($_POST['date_embauche']));
    $mot_de_passe = htmlspecialchars($conn->real_escape_string($_POST['mot_de_passe']));

    // Hashage du mot de passe fourni par l'utilisateur
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_BCRYPT);

    // Requête SQL pour insérer un nouvel agent dans la base de données
    $sql = "INSERT INTO agents (nom, prenom, email, telephone, adresse, departement, role, date_embauche, mot_de_passe)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Préparation de la requête
    if ($stmt = $conn->prepare($sql)) {
        // Lier les paramètres à la requête préparée
        $stmt->bind_param("sssssssss", $nom, $prenom, $email, $telephone, $adresse, $departement, $role, $date_embauche, $mot_de_passe_hash);

        // Exécuter la requête
        if ($stmt->execute()) {
            echo "Agent créé avec succès!";
            header('Location: gerant.php');
            exit();
        } else {
            echo "Erreur lors de la création de l'agent : " . $conn->error;
        }

        // Fermer la requête préparée
        $stmt->close();
    } else {
        echo "Erreur lors de la préparation de la requête : " . $conn->error;
    }

    // Fermer la connexion à la base de données
    $conn->close();
}
?>
