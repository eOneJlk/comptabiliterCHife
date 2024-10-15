<?php
include '../../dbconnexion.php';

// Calcul des entrées et sorties de la journée
$date_aujourdhui = date("Y-m-d");

// Transactions de caisse
$sql_entrees_caisse = "SELECT SUM(montant) as total_entrees FROM transactions_caisse WHERE type='entree' AND DATE(date) = '$date_aujourdhui'";
$sql_sorties_caisse = "SELECT SUM(montant) as total_sorties FROM transactions_caisse WHERE type='sortie' AND DATE(date) = '$date_aujourdhui'";

$result_entrees_caisse = $conn->query($sql_entrees_caisse);
$result_sorties_caisse = $conn->query($sql_sorties_caisse);

$entrees_caisse = $result_entrees_caisse->fetch_assoc()['total_entrees'] ?? 0;
$sorties_caisse = $result_sorties_caisse->fetch_assoc()['total_sorties'] ?? 0;

// Transactions bancaires
$sql_depots_banque = "SELECT SUM(amount) as total_depots FROM transactions_bancaires WHERE transaction_type='deposit' AND DATE(date) = '$date_aujourdhui'";
$sql_retraits_banque = "SELECT SUM(amount) as total_retraits FROM transactions_bancaires WHERE transaction_type='withdrawal' AND DATE(date) = '$date_aujourdhui'";

$result_depots_banque = $conn->query($sql_depots_banque);
$result_retraits_banque = $conn->query($sql_retraits_banque);

$depots_banque = $result_depots_banque->fetch_assoc()['total_depots'] ?? 0;
$retraits_banque = $result_retraits_banque->fetch_assoc()['total_retraits'] ?? 0;

// Dépenses
$sql_depenses = "SELECT SUM(amount) as total_depenses FROM depenses WHERE DATE(date) = '$date_aujourdhui'";
$result_depenses = $conn->query($sql_depenses);
$depenses = $result_depenses->fetch_assoc()['total_depenses'] ?? 0;

// Calculs finaux
$entrees_totales = $entrees_caisse + $depots_banque;
$sorties_totales = $sorties_caisse + $retraits_banque + $depenses;
$reste_total = $entrees_totales - $sorties_totales;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caissier</title>
    <link rel="stylesheet" href="assets/css/style_utilisateur.css">
    <link rel="stylesheet" href="assets/css/style_modal.css">
</head>
<body>
    <header>
        <nav>
            <a class="logo" href="#home">
                <img src="assets/img/logo_2.png" alt="Logo" style="width: 100px; height: 100px; border-radius: 20px; margin: 10px;">
            </a>
            <ul class="nav-links" style="display: flex; justify-content: space-between; align-items: center; padding: 0; margin: 0;">
                <li style="text-align: center; font-family: Arial, sans-serif; color: #333; font-size: 36px; margin-top: 50px;">
                    Formulaire de Caisse
                </li>
            </ul>
        </nav>
    </header>

    <main>
       <section style="background-color: #ffffff; border-radius: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 30px; max-width: 800px; margin: 0 auto;">
           <div style="background-color: #4a90e2; color: #ffffff; padding: 20px; margin-bottom: 30px; border-radius: 10px; text-align: center; font-weight: bold; font-size: 28px; text-transform: uppercase;">Tableau de bord</div>
           <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
               <div style="background-color: #e6ffe6; padding: 20px; width: calc(33.33% - 20px); margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                   <h3 style="color: #2ecc71; margin-bottom: 10px;">Entrées totales</h3>
                   <p style="font-size: 18px; font-weight: bold;"><?php echo number_format($entrees_totales, 2, ',', ' '); ?> €</p>
               </div>
               <div style="background-color: #ffe6e6; padding: 20px; width: calc(33.33% - 20px); margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                   <h3 style="color: #e74c3c; margin-bottom: 10px;">Sorties totales</h3>
                   <p style="font-size: 18px; font-weight: bold;"><?php echo number_format($sorties_totales, 2, ',', ' '); ?> €</p>
               </div>
               <div style="background-color: #e6f3ff; padding: 20px; width: calc(33.33% - 20px); margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                   <h3 style="color: #3498db; margin-bottom: 10px;">Reste total</h3>
                   <p style="font-size: 18px; font-weight: bold;"><?php echo number_format($reste_total, 2, ',', ' '); ?> €</p>
               </div>
           </div>
       </section>
        <section style="display: flex; justify-content: space-around; align-items: center;">
            <div>
                <h2>Mouvement De Caisse Par Compte</h2>
                <button id="openCaisseBtn">Ajouter Mouvement Caisse</button>
            </div>

            <div>
                <h2>Enregistrer une transaction</h2>
                <button id="openTransactionBtn">Enregistrer Transaction</button>
            </div>

            <div>
                <h2>Enregistrer une dépense</h2>
                <button id="openDepenseBtn">Enregistrer Dépense</button>
            </div>
        </section>

        <!-- Modals pour les formulaires -->
        <div id="caisseModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeCaisseModal">&times;</span>
                <form action="submit_caisse_transaction.php" method="post">
                    <h2>Mouvement De Caisse Par Compte</h2>
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required><br>

                    <label for="type">Type de transaction:</label>
                    <select id="type" name="type" required>
                        <option value="entree">Entrée</option>
                        <option value="sortie">Sortie</option>
                    </select><br>

                    <label for="montant">Montant:</label>
                    <input type="number" id="montant" name="montant" step="0.01" required><br>

                    <label for="details">Détails:</label>
                    <textarea id="details" name="details" rows="4" cols="50" required></textarea><br>

                    <label for="compte">Compte:</label>
                    <select id="compte" name="compte" required>
                        <option value="DG">DG</option>
                        <option value="PDG">PDG</option>
                        <option value="Gerant">Gerant</option>
                        <option value="autre">Autre</option>
                    </select><br>

                    <button type="submit">Soumettre</button>
                </form>
            </div>
        </div>

        <div id="transactionModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeTransactionModal">&times;</span>
                <form action="submit_Bak_Movement.php" method="POST">
                    <h2>Ajouter une Transaction Bancaire</h2>
                    <label for="transaction_type">Type de Transaction:</label>
                    <select id="transaction_type" name="transaction_type" required>
                        <option value="deposit">Dépôt</option>
                        <option value="withdrawal">Retrait</option>
                    </select><br><br>

                    <label for="amount">Montant:</label>
                    <input type="number" id="amount" name="amount" step="0.01" required><br><br>

                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required><br><br>

                    <label for="invoice_number">Numéro de Facture:</label>
                    <input type="text" id="invoice_number" name="invoice_number" required><br><br>

                    <label for="slip_number">Numéro de Bordereau:</label>
                    <input type="text" id="slip_number" name="slip_number" required><br><br>

                    <input type="submit" value="Ajouter la Transaction">
                </form>
            </div>
        </div>

        <div id="depenseModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeDepenseModal">&times;</span>
                <form action="submit_expense.php" method="POST">
                    <h2>Ajouter une Dépense</h2>
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required><br><br>

                    <label for="nom">Nom:</label>
                    <input type="text" id="nom" name="nom" required><br><br>

                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea><br><br>

                    <label for="amount">Montant:</label>
                    <input type="number" id="amount" name="amount" step="0.01" required><br><br>

                    <input type="submit" value="Ajouter la Dépense">
                </form>
            </div>
        </div>

        <!-- Tableaux -->
        <section>
            <h2>Tableaux De Caisse par Compte</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type de transaction</th>
                        <th>Montant</th>
                        <th>Détails</th>
                        <th>Compte</th>
                        <th>Action</th>
                        <th>Etat</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT date, type, montant, details, compte FROM transactions_caisse ORDER BY date DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["date"] . "</td>";
                        echo "<td>" . $row["type"] . "</td>";
                        echo "<td>" . $row["montant"] . "</td>";
                        echo "<td>" . $row["details"] . "</td>";
                        echo "<td>" . $row["compte"] . "</td>";
                        echo "<td><button style='background-color: #4CAF50; color: white; padding: 5px 10px; border: none; border-radius: 3px; margin-right: 5px;'>Modifier</button><button style='background-color: #f44336; color: white; padding: 5px 10px; border: none; border-radius: 3px;'>Supprimer</button></td>";
                            echo "<td><span style='background-color: #FFD700; color: black; padding: 5px 10px; border-radius: 3px;'>À approuver</span></td>";
                            echo "</tr>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Aucune transaction trouvée</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Liste des Transactions Bancaires</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type de Transaction</th>
                        <th>Montant</th>
                        <th>Date</th>
                        <th>Numéro de Facture</th>
                        <th>Numéro de Bordereau</th>
                        <th>Date de Création</th>
                        <th>Action</th>
                        <th>Etat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM transactions_bancaires ORDER BY date DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["transaction_type"] . "</td>";
                            echo "<td>" . $row["amount"] . "</td>";
                            echo "<td>" . $row["date"] . "</td>";
                            echo "<td>" . $row["invoice_number"] . "</td>";
                            echo "<td>" . $row["slip_number"] . "</td>";
                            echo "<td>" . $row["created_at"] . "</td>";
                            echo "<td><button style='background-color: #4CAF50; color: white; padding: 5px 10px; border: none; border-radius: 3px; margin-right: 5px;'>Modifier</button><button style='background-color: #f44336; color: white; padding: 5px 10px; border: none; border-radius: 3px;'>Supprimer</button></td>";
                            echo "<td><span style='background-color: #FFD700; color: black; padding: 5px 10px; border-radius: 3px;'>À approuver</span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>Aucune transaction bancaire trouvée</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Liste des Dépenses</h2>
            <?php
            $sql = "SELECT * FROM depenses";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<table border='1'>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Montant</th>
                            <th>Date de Création</th>
                            <th>Action</th>
                            <th>Etat</th>
                        </tr>";
                
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["id"] . "</td>
                            <td>" . $row["date"] . "</td>
                            <td>" . $row["nom"] . "</td>
                            <td>" . $row["description"] . "</td>
                            <td>" . $row["amount"] . "</td>
                            <td>" . $row["created_at"] . "</td>";
                            echo "<td><button style='background-color: #4CAF50; color: white; padding: 5px 10px; border: none; border-radius: 3px; margin-right: 5px;'>Modifier</button><button style='background-color: #f44336; color: white; padding: 5px 10px; border: none; border-radius: 3px;'>Supprimer</button></td>";
                            echo "<td><span style='background-color: #FFD700; color: black; padding: 5px 10px; border-radius: 3px;'>À approuver</span></td>";
                            echo "</tr>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "Aucune dépense trouvée.";
            }
            ?>
        </section>

    </main>
  <script>
       // Get the modals
       var caisseModal = document.getElementById("caisseModal");
    var transactionModal = document.getElementById("transactionModal");
    var depenseModal = document.getElementById("depenseModal");

    // Get the buttons that open the modals
    var openCaisseBtn = document.getElementById("openCaisseBtn");
    var openTransactionBtn = document.getElementById("openTransactionBtn");
    var openDepenseBtn = document.getElementById("openDepenseBtn");

    // Get the <span> elements that close the modals
    var closeCaisseModal = document.getElementById("closeCaisseModal");
    var closeTransactionModal = document.getElementById("closeTransactionModal");
    var closeDepenseModal = document.getElementById("closeDepenseModal");

    // When the user clicks the buttons, open the corresponding modal
    openCaisseBtn.onclick = function() {
        caisseModal.style.display = "block";
    }

    openTransactionBtn.onclick = function() {
        transactionModal.style.display = "block";
    }

    openDepenseBtn.onclick = function() {
        depenseModal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    closeCaisseModal.onclick = function() {
        caisseModal.style.display = "none";
    }

    closeTransactionModal.onclick = function() {
        transactionModal.style.display = "none";
    }

    closeDepenseModal.onclick = function() {
        depenseModal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == caisseModal) {
            caisseModal.style.display = "none";
        } else if (event.target == transactionModal) {
            transactionModal.style.display = "none";
        } else if (event.target == depenseModal) {
            depenseModal.style.display = "none";
        }
    }
    // Récupérer les éléments du DOM
    const openFormBtn = document.getElementById('open-form-btn');
    const popupForm = document.getElementById('popup-form');
    const popupOverlay = document.getElementById('popup-overlay');
    const closeFormBtn = document.getElementById('close-form-btn');

    // Fonction pour ouvrir le popup
    openFormBtn.addEventListener('click', function() {
      popupForm.style.display = 'block';
      popupOverlay.style.display = 'block';
    });

    // Fonction pour fermer le popup
    closeFormBtn.addEventListener('click', function() {
      popupForm.style.display = 'none';
      popupOverlay.style.display = 'none';
    });

    // Fermer le popup en cliquant en dehors du formulaire
    popupOverlay.addEventListener('click', function(event) {
      if (event.target === popupOverlay) {
        popupForm.style.display = 'none';
        popupOverlay.style.display = 'none';
      }
    });
  </script>
</body>
</html>