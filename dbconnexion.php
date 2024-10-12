<?php
// Informations de connexion à la base de données
$host = 'localhost';       // Hôte de la base de données
$user = 'root'; // Nom d'utilisateur MySQL
$password = ''; // Mot de passe MySQL
$database = 'db_hostel';     // Nom de la base de données

// Connexion à la base de données MySQL avec mysqli
$conn = new mysqli($host, $user, $password, $database);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}
?>
