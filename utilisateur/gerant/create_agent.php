<?php

require_once '../../dbconnexion.php';;

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

    // Requête SQL pour insérer un nouvel agent dans la base de données
    $sql = "INSERT INTO agents (nom, prenom, email, telephone, adresse, departement, role, date_embauche)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Préparation de la requête
    if ($stmt = $conn->prepare($sql)) {
        // Lier les paramètres
        $stmt->bind_param("ssssssss", $nom, $prenom, $email, $telephone, $adresse, $departement, $role, $date_embauche);

        // Exécuter la requête
        if ($stmt->execute()) {
            echo "Agent créé avec succès!";
            header('Location: gerant.html');
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
