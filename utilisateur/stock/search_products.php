<?php
require_once '../../dbconnexion.php';

if (isset($_GET['date_debut']) && isset($_GET['date_fin'])) {
    $date_debut = $_GET['date_debut'];
    $date_fin = $_GET['date_fin'];

    $sql = "SELECT * FROM produits WHERE date_entree BETWEEN ? AND ? AND quantite > 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $date_debut, $date_fin);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table border='1'>
                <tr>
                    <th>Date d'entrée</th>
                    <th>Nom du produit</th>
                    <th>Quantité</th>
                    <th>Emplacement du stock</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row['date_entree'] . "</td>
                    <td>" . $row['nom_produit'] . "</td>
                    <td>" . $row['quantite'] . "</td>
                    <td>" . $row['emplacement_stock'] . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "Aucun produit trouvé pour cette période.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Veuillez spécifier une date de début et une date de fin.";
}
?>
