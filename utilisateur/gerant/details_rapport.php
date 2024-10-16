<?php
// Assurez-vous d'avoir une connexion à la base de données établie
include '../../dbconnexion.php';

// Vérifiez si l'ID du rapport est fourni dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID du rapport non spécifié.");
}

$rapport_id = intval($_GET['id']);

// Récupérez les détails du rapport
$sql = "SELECT * FROM rapports WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $rapport_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Rapport non trouvé.");
}

$rapport = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Rapport</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .detail-row {
            margin-bottom: 10px;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-link:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Détails du Rapport</h1>
        
        <div class="detail-row">
            <span class="detail-label">Date :</span> <?php echo htmlspecialchars($rapport['date']); ?>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Agent :</span>
            <?php
            $agent_column = null;
            foreach ($rapport as $key => $value) {
                if (strpos(strtolower($key), 'agent') !== false) {
                    $agent_column = $key;
                    break;
                }
            }
            if ($agent_column !== null && !empty($rapport[$agent_column])) {
                $agent_id = $rapport[$agent_column];
                $sql_agent = "SELECT nom, prenom FROM agents WHERE id = ?";
                $stmt = $conn->prepare($sql_agent);
                $stmt->bind_param("i", $agent_id);
                $stmt->execute();
                $result_agent = $stmt->get_result();
                if ($result_agent->num_rows > 0) {
                    $agent = $result_agent->fetch_assoc();
                    echo htmlspecialchars($agent['nom'] . ' ' . $agent['prenom']);
                } else {
                    echo "Agent inconnu";
                }
            } else {
                echo "Information d'agent non disponible";
            }
            ?>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Nombre de Check Out :</span> <?php echo htmlspecialchars($rapport['nombre_check_out']); ?>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Nombre de Check In :</span> <?php echo htmlspecialchars($rapport['nombre_check_in']); ?>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Chambres Disponibles :</span> <?php echo htmlspecialchars($rapport['chambre_disponible']); ?>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Entrée Cash :</span> <?php echo number_format($rapport['entree_cash'], 2, ',', ' ') . " $"; ?>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Crédit :</span> <?php echo number_format($rapport['credit'], 2, ',', ' ') . " $"; ?>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Entrée Airtel Money :</span> <?php echo number_format($rapport['entree_airtel_money'], 2, ',', ' ') . " $"; ?>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Entrée Carte POS :</span> <?php echo number_format($rapport['entree_carte_pos'], 2, ',', ' ') . " $"; ?>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Total Entrées :</span>
            <?php
            $total_entrees = $rapport['entree_cash'] + $rapport['credit'] + $rapport['entree_airtel_money'] + $rapport['entree_carte_pos'];
            echo number_format($total_entrees, 2, ',', ' ') . " $";
            ?>
        </div>
        
        <a href="gerant.php" class="back-link">Retour à la liste des rapports</a>
    </div>
</body>
</html>
