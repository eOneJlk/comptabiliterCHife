<?php
session_start();
 
                 // Assurez-vous d'avoir une connexion à la base de données établie
                 include '../../dbconnexion.php';
            
                 $roles_autorises = ['admin','gerant']; // Ajoutez les rôles autorisés à accéder à la gerant
                if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles_autorises)) {
                    // Rediriger vers une page d'erreur ou la page d'accueil si l'utilisateur n'a pas le bon rôle
                    echo "<script>window.location.href = '../../acces_refuse.php';</script>";
                    exit();
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
                <li style="list-style-type: none; margin: 10px;"><a href="index.php" style="text-decoration: none; color: #333; font-weight: bold;">Dashboard</a></li>
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
        
        const data1 = {
            labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin'],
            datasets: [{
                label: 'Chambres Occupées',
                data: [50, 60, 70, 80, 90, 100],
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        };
        
        const data2 = {
            labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin'],
            datasets: [{
                label: 'Réservations',
                data: [100, 120, 140, 160, 180, 200],
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

        // Graphique 3 : Répartition des revenus par type de chambre
        const graphique3 = document.getElementById('graphique-3').getContext('2d');
        const data3 = {
            labels: ['Chambre Standard', 'Chambre Deluxe', 'Suite Junior', 'Suite Exécutive'],
            datasets: [{
                data: [30, 25, 20, 25],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
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
                        text: 'Répartition des revenus par type de chambre'
                    }
                }
            }
        });

        // Graphique 4 : Occupation des chambres par catégorie sur les derniers mois
        const graphique4 = document.getElementById('graphique-4').getContext('2d');
        const data4 = {
            labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin'],
            datasets: [
                {
                    label: 'Chambre Standard',
                    data: [65, 70, 80, 75, 85, 90],
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                },
                {
                    label: 'Chambre Deluxe',
                    data: [55, 60, 65, 70, 75, 80],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                },
                {
                    label: 'Suite Junior',
                    data: [40, 45, 50, 55, 60, 65],
                    backgroundColor: 'rgba(255, 206, 86, 0.5)',
                },
                {
                    label: 'Suite Exécutive',
                    data: [30, 35, 40, 45, 50, 55],
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
                        text: 'Occupation des chambres par catégorie'
                    }
                }
            }
        });
    </script>
</body>
</html>
