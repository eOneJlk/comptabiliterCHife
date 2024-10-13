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
    <h1 style="text-align: center; font-size: 2rem; color: #333; margin-top: 50px; background-color: #f9f9f9;">Formulaire de Stock</h1>
    <form action="submit_report.php" method="post">
        <h2>From de Rapport</h2>
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required><br>

        <label for="type">Departement:</label>
        <select id="type" name="type" required>
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
      
        <label for="details">Détails:</label>
        <textarea id="details" name="details" rows="4" cols="50" required></textarea><br>
      
        <button type="submit">Soumettre</button>
    </form>

    <form action="record_product.php" method="post">
        <h2>Enregistrer un Produit</h2>
        <label for="date">Date d'entrée:</label>
        <input type="date" id="date" name="date" required><br>
        <label for="product_name">Nom du produit:</label>
        <input type="text" id="product_name" name="product_name" required><br>
        <label for="quantity">Quantité:</label>
        <input type="number" id="quantity" name="quantity" required><br>
      
        <label for="stock_location">Emplacement du stock:</label>
        <input type="text" id="stock_location" name="stock_location" required><br>
      
        <button type="submit">Enregistrer l'entrée de stock</button>
      </form class="table_de_stock">
      <h2>TABLE DES PRODUITS EN STOCK</h2>
      <table>
        <thead>
          <tr>
            <th>Date d'entrée</th>
            <th>Nom du produit</th>
            <th>Quantité</th>
            <th>Emplacement du stock</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Inclure la connexion à la base de données
          include('../../dbconnexion.php');

          // Requête pour récupérer les produits en stock
          $sql = "SELECT * FROM produits WHERE quantite > 0";
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
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='4'>Aucun produit en stock</td></tr>";
          }

          // Fermer la connexion
          $conn->close();
          ?>
        </tbody>
      </table>
      <form action="remove_product.php" method="post">
        <h2>Retirer un Produit du Stock</h2>
        <label for="product_name">Nom du produit:</label>
        <input type="text" id="product_name" name="product_name" required autocomplete="off"><br>
        <div id="product_suggestions"></div>
        <label for="quantity">Quantité à retirer:</label>
        <input type="number" id="quantity" name="quantity" required><br>
        <label for="stock_location">Emplacement du stock:</label>
        <input type="text" id="stock_location" name="stock_location" required><br>
        <label for="date_sortie">Date de sortie:</label>
        <input type="date" id="date_sortie" name="date_sortie" required><br>
        <button type="submit">Retirer du stock</button>
    </form>
    
    <h2>TABLE DES PRODUITS EN STOCK</h2>
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
            // Requête pour récupérer les produits en stock
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#product_name').keyup(function() {
        var query = $(this).val();
        if (query != '') {
            $.ajax({
                url: "get_products.php",
                method: "POST",
                data: {query:query},
                success: function(data) {
                    $('#product_suggestions').fadeIn();
                    $('#product_suggestions').html(data);
                }
            });
        } else {
            $('#product_suggestions').fadeOut();
        }
    });

    $(document).on('click', '.suggestion', function() {
        $('#product_name').val($(this).text());
        $('#product_suggestions').fadeOut();
    });
});
</script>

<style>
#product_suggestions {
    position: absolute;
    background-color: #f9f9f9;
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #ddd;
    display: none;
}
.suggestion {
    padding: 10px;
    cursor: pointer;
}
.suggestion:hover {
    background-color: #f1f1f1;
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
