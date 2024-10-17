<?php
// Inclure la connexion à la base de données
require_once '../../dbconnexion.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['agent_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: ../../login.php");
    exit();
}
$roles_autorises = ['admin','stock'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles_autorises)) {
    // Rediriger vers une page d'erreur ou la page d'accueil si l'utilisateur n'a pas le bon rôle
    header("Location: ../../acces_refuse.php");
    exit();
}
// Afficher les messages de session s'ils existent
if (isset($_SESSION['message'])) {
    echo "<div class='" . $_SESSION['message_type'] . "'>" . $_SESSION['message'] . "</div>";
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Synchroniser les données du stock
$sql = "SELECT * FROM produits WHERE date_modification > DATE_SUB(NOW(), INTERVAL 1 DAY)";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Mettre à jour les données locales avec les données de la base de données
        // Ceci est un exemple, vous devrez adapter cette partie selon votre structure de données locale
        $_SESSION['stock'][$row['id']] = $row;
    }
}
// Vérifier le rôle de l'utilisateur
$roles_autorises = ['admin']; // Ajoutez ou retirez des rôles selon vos besoins
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles_autorises)) {
    // Rediriger vers une page d'erreur ou la page d'accueil si l'utilisateur n'a pas le bon rôle
    header("Location: ../../acces_refuse.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caissier</title>
    <link rel="stylesheet" href="assets/css/style_utilisateur.css">
    <link rel="stylesheet" href="assets/css/style_stock.css">
</head>
<body>
    <header></header>
    <nav>
      <a class="logo" href="#home"><img src="assets/img/logo_2.png" alt="Logo" style="width: 100px; height: 100px; border-radius: 20px; margin: 10px;"></a>
        <ul class="nav-links" style="display: flex; justify-content: space-between; align-items: center; padding: 0; margin: 0;">
          <li style="list-style-type: none; margin: 10px;"><a href="#home" style="text-decoration: none; color: #333; font-weight: bold;">Accueil</a></li>
          <li style="list-style-type: none; margin: 10px;"><a href="#dashboard.html" style="text-decoration: none; color: #333; font-weight: bold;">Dashboard</a></li>
          <li style="list-style-type: none; margin: 10px;"><a href="#table_de_stock" style="text-decoration: none; color: #333; font-weight: bold;">Produits en stock</a></li>
        </ul>
  </nav>
    </header>
</head>
<body>

<header>
    <!-- Navigation, etc. -->
</header>

<div class="search-bar" style="margin: 20px 0; text-align: center;">
    <form id="searchForm" style="display: inline-block;">
        <label for="date_debut">Date de début:</label>
        <input type="date" id="date_debut" name="date_debut" required style="margin-right: 10px;">
        <label for="date_fin">Date de fin:</label>
        <input type="date" id="date_fin" name="date_fin" required style="margin-right: 10px;">
        <button type="submit" style="padding: 5px 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">Rechercher</button>
    </form>
</div>

<!-- Popup pour afficher les résultats -->
<div id="resultPopup" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Résultats de la recherche</h2>
        <div id="searchResults"></div>
    </div>
</div>

<h1 style="text-align: center;">Formulaire de Stock</h1>

<!-- Boutons pour afficher les formulaires -->
<div class="button-container" style="display: flex; justify-content: center; gap: 10px; margin-bottom: 20px;">
    <button id="openReportForm" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;">Ouvrir Formulaire de Rapport</button>
    <button id="openProductForm" style="padding: 10px 20px; background-color: #008CBA; color: white; border: none; border-radius: 5px; cursor: pointer;">Ouvrir Formulaire d'Enregistrement de Produit</button>
    <button id="openRemoveProductForm" style="padding: 10px 20px; background-color: #f44336; color: white; border: none; border-radius: 5px; cursor: pointer;">Ouvrir Formulaire de Retrait de Produit</button>
</div>

<!-- Modale du formulaire de rapport -->
<div id="reportModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeReportModal">&times;</span>
        <form action="generate_report.php" method="post">
            <h2>Générer un Rapport</h2>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required><br>

            <label for="departement">Département:</label>
            <select id="departement" name="departement" required>
                <option value="lundry">Lundry</option>
                <option value="stock">Stock</option>
                <option value="reception">Reception</option>
                <option value="caisse">Caisse</option>
                <option value="bar">Bar</option>
            </select><br>

            <label for="type">Type de rapport:</label>
            <select id="type" name="type" required>
                <option value="chambre">Chambre</option>
                <option value="stock">Stock</option>
                <option value="vente_bars">Vente Bars</option>
                <option value="caisse">Caisse</option>
            </select><br>

            <label for="produit">Produit (optionnel):</label>
            <input type="text" id="produit" name="produit"><br>

            <button type="submit" name="action" value="generate_report">Générer le rapport</button>
        </form>
    </div>
</div>

<!--Table d'enregistrement de produit-->

<h2>TABLE DES PRODUITS EN STOCK</h2>
<table>
    <thead>
        <tr>
            <th>Date d'entrée</th>
            <th>Nom du produit</th>
            <th>Quantité</th>
            <th>Emplacement du stock</th>
            <th>Action</th>
            <th>Dernière modification</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Inclure la connexion à la base de données
        include('../../dbconnexion.php');

        // Requête pour récupérer les produits en stock
        $sql = "SELECT p.*, CONCAT(a.nom, ' ', a.prenom) AS nom_modificateur 
                FROM produits p 
                LEFT JOIN agents a ON p.id_modificateur = a.id 
                WHERE p.quantite > 0 AND p.date_suppression IS NULL";

        // Ajouter la condition de date si les paramètres sont présents
        if (isset($_GET['date_debut']) && isset($_GET['date_fin'])) {
            $date_debut = $_GET['date_debut'];
            $date_fin = $_GET['date_fin'];
            $sql .= " AND p.date_entree BETWEEN '$date_debut' AND '$date_fin'";
        }

        $result = $conn->query($sql);

        // Vérifier s'il y a des produits
        if ($result->num_rows > 0) {
            // Boucle pour afficher chaque produit
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['date_entree'] . "</td>";
                echo "<td>" . $row['nom_produit'] . "</td>";
                echo "<td>" . $row['quantite'] . "</td>";
                echo "<td>" . $row['emplacement_stock'] . "</td>";
                echo "<td>
                        <button onclick='openModifyModal(" . $row['id'] . ")'>Modifier</button>
                      </td>";
                if ($row['date_modification'] === null) {
                    echo "<td><span style='background-color: yellow;'>Aucune modification</span></td>";
                } else {
                    echo "<td>" . $row['date_modification'] . " par " . $row['nom_modificateur'] . "</td>";
                }
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>Aucun produit en stock</td></tr>";
        }

        // Fermer la connexion
        $conn->close();
        ?>
    </tbody>
</table>

<script>
</script>

<!-- Modale du formulaire d'enregistrement de produit -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeProductModal">&times;</span>
        <form action="record_product.php" method="post">
            <h2>Enregistrer un Produit</h2>
            <label for="date">Date d'entrée:</label>
            <input type="date" id="date" name="date" required><br>

            <label for="product_name">Nom du produit:</label>
            <input type="text" id="product_name" name="product_name" required><br>

            <label for="quantity">Quantité:</label>
            <input type="number" id="quantity" name="quantity" required><br>

            <label for="stock_location">Emplacement du stock:</label>
            <select id="stock_location" name="stock_location" required>
                <option value="lundry">Lundry</option>
                <option value="stock">Stock</option>
                <option value="reception">Reception</option>
                <option value="caisse">Caisse</option>
                <option value="bar">Bar</option>
            </select><br>


            <button type="submit">Enregistrer l'entrée de stock</button>
        </form>
    </div>
</div>
<h2>TABLE DES RETRAIT PRODUITS EN STOCK</h2>
    <table>
        <thead>
            <tr>
                <th>Date d'entrée</th>
                <th>Nom du produit</th>
                <th>Quantité</th>
                <th>Emplacement du stock</th>
                <th>Date de sortie</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Requête pour récupérer les produits en 
            include('../../dbconnexion.php');
            $sql = "SELECT * FROM produits";
            $result = $conn->query($sql);

            // Vérifier s'il y a des produits
            if ($result->num_rows > 0) {
                // Boucle pour afficher chaque produit
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['date_entree'] . "</td>";
                    echo "<td>" . $row['nom_produit'] . "</td>";
                    echo "<td>" . $row['quantite'] . "</td>";
                    echo "<td>" . $row['emplacement_stock'] . "</td>";
                    echo "<td>" . $row['date_sortie'] . "</td>"; // Afficher la date de sortie
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Aucun produit en stock</td></tr>";
            }

            // Fermer la connexion
            $conn->close();
            ?>
        </tbody>
    </table>
<!-- Modale du formulaire de retrait de produit -->
<div id="removeProductModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeRemoveProductModal">&times;</span>
        <form action="remove_product.php" method="post">
            <h2>Retirer un Produit du Stock</h2>
            <label for="product_name">Nom du produit:</label>
            <input type="text" id="product_name" name="product_name" required autocomplete="off"><br>

            <label for="quantity">Quantité à retirer:</label>
            <input type="number" id="quantity" name="quantity" required><br>

            <label for="stock_location">Emplacement du stock:</label>
            <select id="stock_location" name="stock_location" required>
                <option value="lundry">Lundry</option>
                <option value="stock">Stock</option>
                <option value="reception">Reception</option>
                <option value="caisse">Caisse</option>
                <option value="bar">Bar</option>
            </select><br>

            <label for="date_sortie">Date de sortie:</label>
            <input type="date" id="date_sortie" name="date_sortie" required><br>

            <button type="submit">Retirer du stock</button>
        </form>
    </div>
</div>

<script>
    
function openModifyModal(productId) {
    // Créer une nouvelle modal pour la modification
    var modal = document.createElement('div');
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Modifier le produit</h2>
            <form id="modifyProductForm">
                <input type="hidden" name="product_id" value="${productId}">
                <label for="new_quantity">Nouvelle quantité :</label>
                <input type="number" id="new_quantity" name="new_quantity" required>
                <button type="submit">Enregistrer les modifications</button>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.style.display = 'block';

    // Fermer la modal
    modal.querySelector('.close').onclick = function() {
        modal.style.display = 'none';
    }

    // Gérer la soumission du formulaire
    modal.querySelector('#modifyProductForm').onsubmit = function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        
        fetch('modify_product.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Produit modifié avec succès');
                location.reload(); // Recharger la page pour afficher les changements
            } else {
                alert('Erreur lors de la modification du produit');
            }
            modal.style.display = 'none';
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
        });
    }
}

    // Get the modals
    var reportModal = document.getElementById("reportModal");
    var productModal = document.getElementById("productModal");
    var removeProductModal = document.getElementById("removeProductModal");

    // Get the buttons that open the modals
    var openReportFormBtn = document.getElementById("openReportForm");
    var openProductFormBtn = document.getElementById("openProductForm");
    var openRemoveProductFormBtn = document.getElementById("openRemoveProductForm");

    // Get the <span> elements that close the modals
    var closeReportModal = document.getElementById("closeReportModal");
    var closeProductModal = document.getElementById("closeProductModal");
    var closeRemoveProductModal = document.getElementById("closeRemoveProductModal");

    // When the user clicks the button, open the corresponding modal
    openReportFormBtn.onclick = function() {
        reportModal.style.display = "block";
    }

    openProductFormBtn.onclick = function() {
        productModal.style.display = "block";
    }

    openRemoveProductFormBtn.onclick = function() {
        removeProductModal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    closeReportModal.onclick = function() {
        reportModal.style.display = "none";
    }

    closeProductModal.onclick = function() {
        productModal.style.display = "none";
    }

    closeRemoveProductModal.onclick = function() {
        removeProductModal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == reportModal) {
            reportModal.style.display = "none";
        } else if (event.target == productModal) {
            productModal.style.display = "none";
        } else if (event.target == removeProductModal) {
            removeProductModal.style.display = "none";
        }
    }

    function printPDF(dateDebut, dateFin) {
        window.open('generate_pdf.php?date_debut=' + dateDebut + '&date_fin=' + dateFin, '_blank');
    }

    document.getElementById('searchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        var dateDebut = document.getElementById('date_debut').value;
        var dateFin = document.getElementById('date_fin').value;
        
        // Effectuer la requête AJAX
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'search_products.php?date_debut=' + dateDebut + '&date_fin=' + dateFin, true);
        
        xhr.onload = function() {
            if (this.status == 200) {
                document.getElementById('searchResults').innerHTML = this.responseText;
                document.getElementById('resultPopup').style.display = 'block';
            }
        };
        
        xhr.send();
    });

    // Fermer le popup
    document.querySelector('.close').addEventListener('click', function() {
        document.getElementById('resultPopup').style.display = 'none';
    });

    // Fermer le popup si on clique en dehors
    window.addEventListener('click', function(event) {
        if (event.target == document.getElementById('resultPopup')) {
            document.getElementById('resultPopup').style.display = 'none';
        }
    });
</script>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style>

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
      
</body>
</html>