<?php
session_start();
include 'dbconnexion.php';
if (!isset($_SESSION['agent_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès Refusé</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .access-denied {
            text-align: center;
            padding: 50px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 50px auto;
        }
        .access-denied h1 {
            color: #dc3545;
            margin-bottom: 20px;
        }
        .access-denied p {
            color: #6c757d;
            margin-bottom: 20px;
        }
        .access-denied a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .access-denied a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="access-denied">
        <h1>Accès Refusé</h1>
        <p>Désolé, vous n'avez pas les autorisations nécessaires pour accéder à cette page.</p>
        <a href="index.php">Retour à la page d'accueil</a>
    </div>
</body>
</html>
