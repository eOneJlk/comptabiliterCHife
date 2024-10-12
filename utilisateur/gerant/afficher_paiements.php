<?php
// Inclure le fichier de connexion à la base de données
require_once '../../dbconnexion.php';

// Vérifier si la connexion a réussi
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Requête SQL pour sélectionner toutes les entrées de la table 'paiements'
$sql = "SELECT * FROM paiements";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Paiements</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fafafa;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            background-color: #fff;
        }
        th, td {
            border: 1px solid #ddd;
            text-align: left;
            padding: 12px;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .container {
            max-width: 1200px;
            margin: auto;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Liste des Paiements</h1>
        <?php
        if ($result) {
            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<thead>";
                echo "<tr>";
                echo "<th>ID</th>";
                echo "<th>Date de Paiement</th>";
                echo "<th>Nom de l'Agent</th>";
                echo "<th>Catégorie</th>";
                echo "<th>Montant (€)</th>";
                echo "<th>Date de Création</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                
                // Parcourir chaque ligne de résultat
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_paiement']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_agent']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['categorie']) . "</td>";
                    echo "<td>" . htmlspecialchars(number_format($row['montant'], 2, ',', ' ')) . " €</td>";
                    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                    echo "</tr>";
                }
                
                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<p class='error'>Aucun paiement trouvé.</p>";
            }
        } else {
            echo "<p class='error'>Erreur lors de l'exécution de la requête: " . htmlspecialchars($conn->error) . "</p>";
        }

        // Libérer les résultats
        if ($result) {
            $result->free();
        }

        // Fermer la connexion
        $conn->close();
        ?>
    </div>
</body>
</html>
