<?php
session_start();
require_once 'dbconnexion.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['agent_id'])) {
    header("Location: login.php");
    exit();
}

// Récupérer le nom de l'agent connecté
$agent_id = $_SESSION['agent_id'];
$sql = "SELECT nom, prenom FROM agents WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$result = $stmt->get_result();
$agent = $result->fetch_assoc();
$nom_complet = $agent['nom'] . ' ' . $agent['prenom'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">x
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Dashboard</title>
    <!-- Ajout de Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Ajout de Google Fonts pour les icônes -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Dashboard</div>
            <div class="nav-links" style="display: flex; justify-content: space-between; align-items: center; padding: 20px; background-color: #f9f9f9; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
                <a href="utilisateur/dg/dg.php" class="nav-link" style="background-color: hwb(0 80% 20%); color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">DG</a>
                <a href="utilisateur/gerant/gerant.php" class="nav-link" style="background-color: hwb(0 80% 20%); color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Gerant</a>
                <a href="utilisateur/comptabilite/comptabilite.html" class="nav-link" style="background-color: hwb(0 80% 20%); color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Comptabilite</a>
                <a href="utilisateur/stock/stock.php" class="nav-link" style="background-color: hwb(0 80% 20%); color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Stok</a>
                <a href="utilisateur/caisse/caisse.php" class="nav-link" style="background-color: hwb(0 80% 20%); color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Caisse</a>
                <a href="utilisateur/agent/agent.php" class="nav-link" style="background-color: hwb(0 80% 20%); color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Agant</a>
            </div>
            <div class="user-profile">
                <img src="assets/img/logo_2.png" alt="User Avatar">
                <span><a href="profil.php"><?php echo htmlspecialchars($nom_complet); ?></a></span>
                <a href="logout.php" class="logout-link">Déconnexion</a>
            </div>
        </nav>
    </header>

    <main>
        <section id="table-des-agents">
            <h2>Table des agents créés par le gérant</h2>
            <div class="chart-container">
                <table class="table-agents">
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Role</th>
                    </tr>
                <?php
                $sql_agents = "SELECT nom, prenom, email, telephone, role FROM agents";
                $result_agents = $conn->query($sql_agents);

                if ($result_agents->num_rows > 0) {
                    while($row = $result_agents->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['prenom']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['telephone']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align: center;'>Aucun agent trouvé.</td></tr>";
                }
                ?>
                </table>
            </div>
        </section>
        

        <section id="table-des-données">
            <h2>Tables des données </h2>
            <div class="chart-container">
                <table class="table-1">
                    <tr>
                        <th>Nom</th>
                        <th>Justification</th>
                        <th>Montant</th>
                        <th>Etat</th>
                    </tr>
                </table>
               
            </div>
        </section>

        <section id="table-des-données">
            <h2>Rapport des departements</h2>
            <div class="chart-container">
                <table class="table-2">
                    <tr>
                        <th>Nom</th>
                        <th>Contenue</th>
                        <th>Date</th>
                        <th><button type="button">Lire</button></th>
                    </tr>
                </table>
            </div>
        </section>
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
    <script src="assets/script.js"></script>
</body>
</html>