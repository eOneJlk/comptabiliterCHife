<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal de Caisse - Rapport Quotidien</title>
    <link rel="stylesheet" href="assets/style_agent.css">
</head>
<body>
    <header>
        <nav>
            <a class="logo" href="#home"><img src="assets/img/logo_2.png" alt="Logo" style="width: 100px; height: 100px; border-radius: 20px; margin: 10px;"></a>
            <ul class="nav-links">
                <li><a href="#home">Accueil</a></li>
                <li><a href="#dashboard.html">Dashboard</a></li>
                <li><a href="#rapports-et-statistiques">Statistiques</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php
        session_start();
        require_once '../../dbconnexion.php';
        
        // Vérification de l'autorisation
        if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'agent')) {
            header("Location: ../../acces_refuse.php");
            exit();
        }
        
        // Vérifier si un rapport a déjà été soumis aujourd'hui
        $today = date("Y-m-d");
        $sql = "SELECT * FROM rapports WHERE date = '$today'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Un rapport a déjà été soumis aujourd'hui
            echo "<div class='info-message'>";
            echo "<p><strong>Information :</strong> Le rapport pour aujourd'hui a déjà été soumis. Le formulaire sera à nouveau disponible demain.</p>";
            echo "</div>";
        } else {
            // Aucun rapport n'a été soumis aujourd'hui, afficher le formulaire
        ?>
            <div class="info-message">
                <p><strong>Attention :</strong> Ce formulaire ne peut être soumis qu'une seule fois par jour. Assurez-vous que toutes les informations sont correctes avant de soumettre.</p>
            </div>

            <form action="submit_journal_entry.php" method="post" id="journalForm">
                <h2>Formulaire d'Entrée du Journal de Caisse</h2>
                
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required readonly><br>

                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" required><br>

                <label for="nombre_check_out">Nombre de Check Out:</label>
                <input type="number" id="nombre_check_out" name="nombre_check_out" required><br>

                <label for="nombre_check_in">Nombre de Check In:</label>
                <input type="number" id="nombre_check_in" name="nombre_check_in" required><br>

                <label for="chambre_disponible">Chambres Disponibles:</label>
                <input type="number" id="chambre_disponible" name="chambre_disponible" required><br>

                <label for="contenu">Contenu:</label>
                <textarea id="contenu" name="contenu" rows="4" cols="50" required></textarea><br>

                <label for="entree_cash">Entrée Cash:</label>
                <input type="number" id="entree_cash" name="entree_cash" step="0.01" required><br>

                <label for="credit">Crédit:</label>
                <input type="number" id="credit" name="credit" step="0.01" required><br>

                <label for="entree_airtel_money">Entrée Airtel Money:</label>
                <input type="number" id="entree_airtel_money" name="entree_airtel_money" step="0.01" required><br>

                <label for="entree_carte_pos">Entrée Carte POS:</label>
                <input type="number" id="entree_carte_pos" name="entree_carte_pos" step="0.01" required><br>
                
                <button type="submit">Soumettre le rapport journalier</button>
            </form>
       
        <button onclick="showStockRequestForm()">Soumettre une demande de produit au stock</button>
        <div id="stockRequestFormContainer" style="display: none;">
            <form action="submit_stock_request.php" method="post" id="stockRequestForm">
                <h2>Formulaire de Demande de Produit au Stock</h2>
                
                <label for="produit">Produit:</label>
                <input type="text" id="produit" name="produit" required><br>

                <label for="quantite">Quantité:</label>
                <input type="number" id="quantite" name="quantite" required><br>

                <label for="raison">Raison de la demande:</label>
                <textarea id="raison" name="raison" rows="4" cols="50" required></textarea><br>

                <button type="submit">Soumettre la demande de produit</button>
            </form>
        </div>
        <script>
            function showStockRequestForm() {
                var formContainer = document.getElementById("stockRequestFormContainer");
                if (formContainer.style.display === "none") {
                    formContainer.style.display = "block";
                } else {
                    formContainer.style.display = "none";
                }
            }
        </script>
        <?php
        }
        ?>
    </main>
    <section style="background-color: #ffffff; border-radius: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 30px; max-width: 800px; margin: 20px auto;">
        <h2 style="background-color: #4a90e2; color: #ffffff; padding: 15px; margin-bottom: 20px; border-radius: 10px; text-align: center; font-weight: bold; font-size: 24px; text-transform: uppercase;">Rapport Journalier</h2>
        <div class="rapport-content" style="display: flex; flex-wrap: wrap; justify-content: space-between;">
            <?php
            // Inclure la connexion à la base de données
            include '../../dbconnexion.php';

            // Récupérer le dernier rapport soumis
            $sql = "SELECT * FROM rapports ORDER BY date DESC LIMIT 1";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $fields = [
                    'Date' => $row['date'],
                    'Nom' => $row['nom'],
                    'Check Out' => $row['nombre_check_out'],
                    'Check In' => $row['nombre_check_in'],
                    'Chambres Disponibles' => $row['chambre_disponible'],
                    'Entrée Cash' => number_format($row['entree_cash'], 0, ',', ' ') . '$',
                    'Crédit' => number_format($row['credit'], 0, ',', ' ') . ' $',
                    'Entrée Airtel Money' => number_format($row['entree_airtel_money'], 0, ',', ' ') . ' $',
                    'Entrée Carte POS' => number_format($row['entree_carte_pos'], 0, ',', ' ') . ' $'
                ];

                foreach ($fields as $label => $value) {
                    echo "<div style='width: 48%; margin-bottom: 15px; background-color: #f8f9fa; padding: 10px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
                    echo "<strong style='color: #4a90e2;'>{$label}:</strong> <span style='float: right;'>{$value}</span>";
                    echo "</div>";
                }

                echo "<div style='width: 100%; margin-top: 20px; background-color: #f8f9fa; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
                echo "<strong style='color: #4a90e2;'>Contenu:</strong><br>";
                echo "<p style='margin-top: 10px;'>" . nl2br(htmlspecialchars($row['contenu'])) . "</p>";
                echo "</div>";
            } else {
                echo "<p style='width: 100%; text-align: center; color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 5px;'>Aucun rapport disponible.</p>";
            }
            ?>
        </div>
    </section>

    <section class="historique-rapports" style="background-color: #ffffff; border-radius: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 30px; max-width: 800px; margin: 20px auto;">
        <h2 style="background-color: #4a90e2; color: #ffffff; padding: 15px; margin-bottom: 20px; border-radius: 10px; text-align: center; font-weight: bold; font-size: 24px; text-transform: uppercase;">Historique des Rapports</h2>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="background-color: #f2f2f2; padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Date</th>
                    <th style="background-color: #f2f2f2; padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Nom</th>
                    <th style="background-color: #f2f2f2; padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Check Out</th>
                    <th style="background-color: #f2f2f2; padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Check In</th>
                    <th style="background-color: #f2f2f2; padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Chambres Disponibles</th>
                    <th style="background-color: #f2f2f2; padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Total Entrées</th>
                    <th style="background-color: #f2f2f2; padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Télécharger le soubassement</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT date, nom, nombre_check_out, nombre_check_in, chambre_disponible, 
                        (entree_cash + credit + entree_airtel_money + entree_carte_pos) as total_entrees 
                        FROM rapports ORDER BY date DESC LIMIT 10";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td style='padding: 12px; text-align: left; border: 1px solid #ddd;'>" . $row['date'] . "</td>";
                        echo "<td style='padding: 12px; text-align: left; border: 1px solid #ddd;'>" . $row['nom'] . "</td>";
                        echo "<td style='padding: 12px; text-align: left; border: 1px solid #ddd;'>" . $row['nombre_check_out'] . "</td>";
                        echo "<td style='padding: 12px; text-align: left; border: 1px solid #ddd;'>" . $row['nombre_check_in'] . "</td>";
                        echo "<td style='padding: 12px; text-align: left; border: 1px solid #ddd;'>" . $row['chambre_disponible'] . "</td>";
                        echo "<td style='padding: 12px; text-align: left; border: 1px solid #ddd;'>" . number_format($row['total_entrees'], 2, ',', ' ') . " $</td>";
                        echo "<td style='padding: 12px; text-align: left; border: 1px solid #ddd;'>";
                        echo "<a href='generer_soubassement.php?date=" . $row['date'] . "' target='_blank' style='text-decoration: none;'>";
                        echo "<button style='background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;'>Télécharger le soubassement</button>";
                        echo "</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='padding: 12px; text-align: center; border: 1px solid #ddd;'>Aucun historique disponible.</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
        
    </section>

    <script>
      // Fonction pour formater la date
      const formatDate = (date) => {
          const dd = String(date.getDate()).padStart(2, '0');
          const mm = String(date.getMonth() + 1).padStart(2, '0');
          const yyyy = date.getFullYear();
          return `${yyyy}-${mm}-${dd}`;
      };
  
      // Récupérer la date d'Internet
      fetch('https://worldtimeapi.org/api/ip')
          .then(response => response.json())
          .then(data => {
              const internetDate = new Date(data.datetime);
              document.getElementById('date').value = formatDate(internetDate);
          })
          .catch(error => {
              console.error('Erreur lors de la récupération de la date:', error);
              // En cas d'erreur, utiliser la date locale
              document.getElementById('date').value = formatDate(new Date());
          });
  </script>
</body>
</html>
