<?php
session_start();
include '../../dbconnexion.php';
$roles_autorises = ['admin','gerant']; // Ajoutez les rôles autorisés à accéder à la gerant
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles_autorises)) {
    // Rediriger vers une page d'erreur ou la page d'accueil si l'utilisateur n'a pas le bon rôle
    echo "<script>window.location.href = '../../acces_refuse.php';</script>";
    exit();
}

// Traitement des actions d'approbation ou de rejet
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['operation_id']) && isset($_POST['operation_type'])) {
    $action = $_POST['action'];
    $operation_id = $_POST['operation_id'];
    $operation_type = $_POST['operation_type'];
    
    $new_state = ($action == 'approve') ? 'approved' : 'rejected';
    
    // Déterminer la table à mettre à jour en fonction du type d'opération
    $table_name = '';
    switch ($operation_type) {
        case 'Caisse':
            $table_name = 'transactions_caisse';
            break;
        case 'Banque':
            $table_name = 'transactions_bancaires';
            break;
        case 'Dépense':
            $table_name = 'depenses';
            break;
    }
    
    if (!empty($table_name)) {
        $sql_update = "UPDATE $table_name SET etat = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("si", $new_state, $operation_id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Opération " . ($new_state == 'approved' ? 'approuvée' : 'rejetée') . " avec succès.');</script>";
        } else {
            echo "<script>alert('Erreur lors de la mise à jour de l'état de l'opération.');</script>";
        }
        
        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Hôtel</title>
    <link rel="stylesheet" href="assets/css/style_utilisateur.css">
    <link rel="stylesheet" href="assets/css/style_gerant.css">
</head>
<body>
    <header>
        <nav>
            <a class="logo" href="#home"><img src="assets/img/logo_2.png" alt="Logo" style="width: 100px; height: 100px; border-radius: 20px; margin: 10px;"></a>
              <ul class="nav-links" style="display: flex; justify-content: space-between; align-items: center; padding: 0; margin: 0;">
                <li style="list-style-type: none; margin: 10px;"><a href="index.php" style="text-decoration: none; color: #333; font-weight: bold;">Admin</a></li>
                <li style="list-style-type: none; margin: 10px;"><a href="personnel.php" style="text-decoration: none; color: #333; font-weight: bold;">Gestion du Personnel</a></li>
                <li style="list-style-type: none; margin: 10px;"><a href="#dashboard-section" style="text-decoration: none; color: #333; font-weight: bold;">Graphiques</a></li>
              </ul>
        </nav>
    </header>
    <main>
        <h1>Dashboard Hôtel</h1>
        <section class="dashboard-section">
            <h2>Statistiques Actuelles</h2>
            <div class="stats">
                <div class="stat">
                    <p>Chambres Occupées: <span>50</span></p>
                </div>
                <div class="stat">
                    <p>Chambres Disponibles: <span>100</span></p>
                </div>
                <div class="stat">
                    <p>Nombre de Réservations: <span>200</span></p>
                </div>
                <div class="stat">
                    <p>Nombre d'Employés: <span>50</span></p>
                </div>
            </div>
        </section>
        <section class="dashboard-section">
            <h2>Graphiques</h2>
            <div style="display: flex; flex-wrap: wrap; justify-content: space-between;">
                <div style="width: 100%; display: flex; justify-content: space-between; margin-bottom: 20px;">
                    <div class="graphique" style="width: 48%;">
                        <canvas id="graphique-1"></canvas>
                    </div>
                    <div class="graphique" style="width: 48%;">
                        <canvas id="graphique-2"></canvas>
                    </div>
                </div>
                <div style="width: 100%; display: flex; justify-content: space-between;">
                    <div class="graphique" style="width: 48%;">
                        <canvas id="graphique-3"></canvas>
                    </div>
                    <div class="graphique" style="width: 48%;">
                        <canvas id="graphique-4"></canvas>
                    </div>
                </div>
            </div>
        </section>

        <table style="width: 100%; border-collapse: collapse; ">
            <thead>
                 <tr>
                     <th style="border: 1px solid #ddd; padding: 8px;">Date</th>
                     <th style="border: 1px solid #ddd; padding: 8px;">Agent</th>
                     <th style="border: 1px solid #ddd; padding: 8px;">Nombre de Check Out</th>
                     <th style="border: 1px solid #ddd; padding: 8px;">Nombre de Check In</th>
                     <th style="border: 1px solid #ddd; padding: 8px;">Chambres Disponibles</th>
                     <th style="border: 1px solid #ddd; padding: 8px;">Entrée Cash</th>
                     <th style="border: 1px solid #ddd; padding: 8px;">Crédit</th>
                     <th style="border: 1px solid #ddd; padding: 8px;">Entrée Airtel Money</th>
                     <th style="border: 1px solid #ddd; padding: 8px;">Entrée Carte POS</th>
                     <th style="border: 1px solid #ddd; padding: 8px;">Total Entrées</th>
                     <th style="border: 1px solid #ddd; padding: 8px;">Détails</th>
                 </tr>
            </thead>
            <tbody>
                 <?php
                 
                 // Assurez-vous d'avoir une connexion à la base de données établie
                 include '../../dbconnexion.php';
            
                 $roles_autorises = ['admin','gerant']; // Ajoutez les rôles autorisés à accéder à la gerant
                if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles_autorises)) {
                    // Rediriger vers une page d'erreur ou la page d'accueil si l'utilisateur n'a pas le bon rôle
                    echo "<script>window.location.href = '../../acces_refuse.php';</script>";
                    exit();
                }

                 $sql = "SELECT * FROM rapports ORDER BY date DESC";
                 $result = $conn->query($sql);

                 if ($result->num_rows > 0) {
                     while($row = $result->fetch_assoc()) {
                         echo "<tr>";
                         echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . $row['date'] . "</td>";
                         echo "<td style='border: 1px solid #ddd; padding: 8px;'>";
                         // Vérifiez si une colonne faisant référence à l'agent existe
                         $agent_column = null;
                         foreach ($row as $key => $value) {
                             if (strpos(strtolower($key), 'agent') !== false) {
                                 $agent_column = $key;
                                 break;
                             }
                         }
                         if ($agent_column !== null && !empty($row[$agent_column])) {
                             $agent_id = $row[$agent_column];
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
                         echo "</td>";
                         echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . $row['nombre_check_out'] . "</td>";
                         echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . $row['nombre_check_in'] . "</td>";
                         echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . $row['chambre_disponible'] . "</td>";
                         echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . number_format($row['entree_cash'], 2, ',', ' ') . " $</td>";
                         echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . number_format($row['credit'], 2, ',', ' ') . " $</td>";
                         echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . number_format($row['entree_airtel_money'], 2, ',', ' ') . " $</td>";
                         echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . number_format($row['entree_carte_pos'], 2, ',', ' ') . " $</td>";
                         $total_entrees = $row['entree_cash'] + $row['credit'] + $row['entree_airtel_money'] + $row['entree_carte_pos'];
                         echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . number_format($total_entrees, 2, ',', ' ') . " $</td>";
                         echo "<td style='border: 1px solid #ddd; padding: 8px;'>";
                         echo "<div style='display: flex; justify-content: space-between;'>";
                         echo "<a href='details_rapport.php?id=" . $row['id'] . "' style='text-decoration: none; flex: 1; margin-right: 5px;'>";
                         echo "<button style='background-color: #4CAF50; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer; width: 100%;'>Voir détails</button>";
                         echo "</a>";
                         echo "<a href='generer_rapport_pdf.php?id=" . $row['id'] . "' style='text-decoration: none; flex: 1;'>";
                         echo "<button style='background-color: #007bff; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer; width: 100%;'>Télécharger PDF</button>";
                         echo "</a>";
                         echo "</div>";
                         echo "</td>";
                         echo "</tr>";
                     }
                 } else {
                     echo "<tr><td colspan='11' style='border: 1px solid #ddd; padding: 8px; text-align: center;'>Aucun rapport disponible.</td></tr>";
                 }
                 ?>
    </tbody>
    </table>
    
    <h2>Opérations de caisse</h2>
    <table style="border-collapse: collapse; width: 100%; margin-top: 20px;">
        <thead>
            <tr>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Date</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Type d'opération</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Montant</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Description</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Compte</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
           $sql_operations = "SELECT * FROM (
            SELECT id, date, 'Caisse' AS type, type AS sous_type, montant, details AS description, compte, etat FROM transactions_caisse
            UNION ALL
            SELECT id, date, 'Banque' AS type, transaction_type AS sous_type, amount AS montant, CONCAT('Facture: ', invoice_number, ', Bordereau: ', slip_number) AS description, '' AS compte, etat FROM transactions_bancaires
            UNION ALL
            SELECT id, date, 'Dépense' AS type, '' AS sous_type, amount AS montant, description, nom AS compte, etat FROM depenses
        ) AS operations ORDER BY date DESC LIMIT 20";
        
        $result_operations = $conn->query($sql_operations);
        
        if ($result_operations->num_rows > 0) {
            while($row = $result_operations->fetch_assoc()) {
                echo "<tr>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['date']) . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['type']) . " (" . htmlspecialchars($row['sous_type']) . ")</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . number_format($row['montant'], 2, ',', ' ') . " $</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['compte']) . "</td>";
                
                $etat_style = '';
                switch ($row['etat']) {
                    case 'pending':
                        $etat_style = 'background-color: #FFD700; color: black;';
                        break;
                    case 'approved':
                        $etat_style = 'background-color: #4CAF50; color: white;';
                        break;
                    case 'rejected':
                        $etat_style = 'background-color: #f44336; color: white;';
                        break;
                    default:
                        $etat_style = 'background-color: #808080; color: white;';
                }
                
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>";
                echo "<span style='padding: 5px 10px; border-radius: 3px; " . $etat_style . "'>" . ucfirst(htmlspecialchars($row['etat'])) . "</span>";
        
                if ($row['etat'] == 'pending') {
                    echo "<form method='POST' style='display: inline;'>";
                    echo "<input type='hidden' name='operation_id' value='" . $row['id'] . "'>";
                    echo "<input type='hidden' name='operation_type' value='" . $row['type'] . "'>";
                    echo "<button type='submit' name='action' value='approve' style='margin-left: 5px; background-color: #4CAF50; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;'>Approuver</button>";
                    echo "<button type='submit' name='action' value='reject' style='margin-left: 5px; background-color: #f44336; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;'>Rejeter</button>";
                    echo "</form>";
                }
        
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6' style='border: 1px solid #ddd; padding: 8px; text-align: center;'>Aucune opération disponible.</td></tr>";
        }
            ?>
        </tbody>
    </table>
    </main>

    <footer>
      <div class="footer-content">
          <style>
              .footer-content {
                  background-color: #e7e5e5;
                  padding: 10px 0;
                  text-align: center;
                  color: #000000;
              }
              .footer-content .logo {
                  width: 100px;
                  height: auto;
                  margin-bottom: 20px;
              }
              .footer-content p {
                  margin-bottom: 20px;
              }
              .footer-content .social-links {
                  display: flex;
                  justify-content: center;
                  align-items: center;
              }
              .footer-content .social-links a {
                  margin: 0 10px;
                  color: #333;
                  transition: color 0.3s ease;
              }
              .footer-content .social-links a:hover {
                  color: #57a3f3;
              }
          </style>
          <img src="assets/img/logo_2.png" alt="Company Logo" class="logo">
          <p>&copy; 2024 Chife Hotel. All rights reserved.</p>
          <div class="social-links">
              <a href="https://www.facebook.com/" target="_blank"><i class="fab fa-facebook-f"></i></a>
              <a href="https://www.twitter.com/" target="_blank"><i class="fab fa-twitter"></i></a>
              <a href="https://www.instagram.com/" target="_blank"><i class="fab fa-instagram"></i></a>
              <a href="https://www.linkedin.com/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
          </div>
      </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
        // Ajout de code pour les graphiques
        const graphique1 = document.getElementById('graphique-1').getContext('2d');
        const graphique2 = document.getElementById('graphique-2').getContext('2d');
        
        <?php
        $sql_chambres_occupees = "SELECT DATE(date) as jour, SUM(nombre_check_in - nombre_check_out) as chambres_occupees 
                                  FROM rapports 
                                  WHERE date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) 
                                  GROUP BY MONTH(date) 
                                  ORDER BY jour ASC 
                                  LIMIT 6";
        $result_chambres_occupees = $conn->query($sql_chambres_occupees);

        $labels = [];
        $data_chambres_occupees = [];

        while ($row = $result_chambres_occupees->fetch_assoc()) {
            $labels[] = date('M', strtotime($row['jour']));
            $data_chambres_occupees[] = $row['chambres_occupees'];
        }
        ?>

        const data1 = {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Chambres Occupées',
                data: <?php echo json_encode($data_chambres_occupees); ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                fill: true
            }]
        };
        
        <?php
$sql_reservations = "SELECT DATE(date) as jour, SUM(nombre_check_in) as reservations 
                     FROM rapports 
                     WHERE date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) 
                     GROUP BY MONTH(date) 
                     ORDER BY jour ASC 
                     LIMIT 6";
$result_reservations = $conn->query($sql_reservations);

$labels = [];
$data_reservations = [];

while ($row = $result_reservations->fetch_assoc()) {
    $labels[] = date('M', strtotime($row['jour']));
    $data_reservations[] = $row['reservations'];
}
?>

const data2 = {
    labels: <?php echo json_encode($labels); ?>,
    datasets: [{
        label: 'Nombre de réservations mensuelles',
        data: <?php echo json_encode($data_reservations); ?>,
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
    }]
};
        const options = {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        };
        
        new Chart(graphique1, {
            type: 'bar',
            data: data1,
            options: options
        });
        
        new Chart(graphique2, {
            type: 'line',
            data: data2,
            options: options
        });

            // Graphique 3 : Répartition des transactions financières
            const graphique3 = document.getElementById('graphique-3').getContext('2d');

// Récupération des données depuis la base de données
<?php
$sql_totaux = "SELECT 
    (SELECT SUM(amount) FROM transactions_bancaires WHERE etat = 'approved') as total_banque,
    (SELECT SUM(montant) FROM transactions_caisse WHERE etat = 'approved') as total_caisse,
    (SELECT SUM(amount) FROM depenses WHERE etat = 'approved') as total_depenses";
$result_totaux = $conn->query($sql_totaux);
$row_totaux = $result_totaux->fetch_assoc();

$total_banque = $row_totaux['total_banque'] ?? 0;
$total_caisse = $row_totaux['total_caisse'] ?? 0;
$total_depenses = $row_totaux['total_depenses'] ?? 0;
?>

const data3 = {
    labels: ['Transactions bancaires', 'Transactions de caisse', 'Dépenses'],
    datasets: [{
        data: [<?php echo $total_banque; ?>, <?php echo $total_caisse; ?>, <?php echo $total_depenses; ?>],
        backgroundColor: ['#36A2EB', '#FFCE56', '#FF6384'],
    }]
};

new Chart(graphique3, {
    type: 'pie',
    data: data3,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Répartition des transactions financières'
            }
        }
    }
});

// Graphique 4 : Mouvements financiers sur les 30 derniers jours
const graphique4 = document.getElementById('graphique-4').getContext('2d');

<?php
// Récupération des données pour les 30 derniers jours
$sql_mouvements = "
    SELECT 
        DATE(date) as jour,
        SUM(CASE WHEN type = 'sortie' THEN montant ELSE 0 END) as sorties_caisse,
        SUM(CASE WHEN type = 'entree' THEN montant ELSE 0 END) as entrees_rapports,
        0 as mouvements_banque
    FROM transactions_caisse
    WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY DATE(date)
    
    UNION ALL
    
    SELECT 
        DATE(date) as jour,
        0 as sorties_caisse,
        0 as entrees_rapports,
        SUM(amount) as mouvements_banque
    FROM transactions_bancaires
    WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY DATE(date)
    
    ORDER BY jour ASC
";

$result_mouvements = $conn->query($sql_mouvements);

$labels = [];
$sorties_caisse = [];
$entrees_rapports = [];
$mouvements_banque = [];

$data_by_day = [];
for ($i = 29; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $data_by_day[$day] = ['sorties_caisse' => 0, 'entrees_rapports' => 0, 'mouvements_banque' => 0];
}

while ($row = $result_mouvements->fetch_assoc()) {
    $jour = $row['jour'];
    if (isset($data_by_day[$jour])) {
        $data_by_day[$jour]['sorties_caisse'] += $row['sorties_caisse'];
        $data_by_day[$jour]['entrees_rapports'] += $row['entrees_rapports'];
        $data_by_day[$jour]['mouvements_banque'] += $row['mouvements_banque'];
    }
}

foreach ($data_by_day as $day => $data) {
    $labels[] = date('d M', strtotime($day));
    $sorties_caisse[] = $data['sorties_caisse'];
    $entrees_rapports[] = $data['entrees_rapports'];
    $mouvements_banque[] = $data['mouvements_banque'];
}
?>

const data4 = {
    labels: <?php echo json_encode($labels); ?>,
    datasets: [
        {
            label: 'Sorties caisse',
            data: <?php echo json_encode($sorties_caisse); ?>,
            backgroundColor: 'rgba(255, 99, 132, 0.5)',
        },
        {
            label: 'Entrées (rapports agents)',
            data: <?php echo json_encode($entrees_rapports); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
        },
        {
            label: 'Mouvements bancaires',
            data: <?php echo json_encode($mouvements_banque); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.5)',
        }
    ]
};

new Chart(graphique4, {
    type: 'bar',
    data: data4,
    options: {
        responsive: true,
        scales: {
            x: {
                stacked: true,
            },
            y: {
                stacked: true
            }
        },
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Mouvements financiers sur les 30 derniers jours'
            }
        }
    }
});
    </script>
</body>
</html>
