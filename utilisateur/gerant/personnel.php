<?php
require_once '../../dbconnexion.php';
require_once '../../dbconnexion.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['agent_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: ../../login.php");
    exit();
}
$roles_autorises = ['admin','gerant'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles_autorises)) {
    // Rediriger vers une page d'erreur ou la page d'accueil si l'utilisateur n'a pas le bon rôle
    header("Location: ../../acces_refuse.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du Personnel</title>
    <link rel="stylesheet" href="assets/css/style_utilisateur.css">
    <style>
        /* Styles Consolidés */
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
        }
        th, td {
            border: 1px solid #ddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
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
</head>
<body>
    <header>
        <nav>
            <a class="logo" href="#home">
                <img src="assets/img/logo_2.png" alt="Logo" style="width: 100px; height: 100px; border-radius: 20px; margin: 10px;">
            </a>
            <ul class="nav-links" style="display: flex; justify-content: space-between; align-items: center; padding: 0; margin: 0;">
                <li style="list-style-type: none; margin: 10px;">
                    <a href="create_agent.php" style="text-decoration: none; color: #333; font-weight: bold;">Ajouter un agent</a>
                </li>
                <li style="list-style-type: none; margin: 10px;">
                    <a href="#fiche_des_agants" style="text-decoration: none; color: #333; font-weight: bold;">Fiche des Agents</a>
                </li>
                <li style="list-style-type: none; margin: 10px;">
                    <a href="fiche_des_paye.html" style="text-decoration: none; color: #333; font-weight: bold;">Fiche des Payes</a>
                </li>
            </ul>
        </nav>
    </header>

    <div style="background-color: #f0f0f0; text-align: center;">
        <h1>Ressources Humaines</h1>
    </div>

    <!-- Formulaire de Création d'un Agent -->
    <form action="create_agent.php" method="post">
        <h2>Création d'un Agent</h2>
        <label for="nom">Nom:</label>
        <input type="text" id="nom" name="nom" required><br>

        <label for="prenom">Prénom:</label>
        <input type="text" id="prenom" name="prenom" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="telephone">Téléphone:</label>
        <input type="tel" id="telephone" name="telephone" required><br>

        <label for="adresse">Adresse:</label>
        <textarea id="adresse" name="adresse" rows="4" cols="50" required></textarea><br>

        <label for="departement">Département:</label>
        <select id="departement" name="departement" required>
            <option value="lundry">Lundry</option>
            <option value="stock">Stock</option>
            <option value="reception">Reception</option>
            <option value="caisse">Caisse</option>
            <option value="bar">Bar</option>
        </select><br>
      
        <label for="role">Rôle:</label>
        <select id="role" name="role" required>
            <option value="agent">Agent</option>
            <option value="dg">Directeur genral</option>
            <option value="admin">Administrateur</option>
            <option value="stock">Stock</option>
            <option value="caisse">Caisse</option>
            <option value="comptabilite">Comptabilite</option>
            <option value="gerant">Gerant</option>
        </select><br>
      
        <label for="date_embauche">Date d'embauche:</label>
        <input type="date" id="date_embauche" name="date_embauche" required><br>
      
        <button type="submit">Créer l'agent</button>
    </form>

    <!-- Liste des Agents -->
    <div style="background-color: #f0f0f0; text-align: center;">
        <h2>Liste des Agents</h2>
    </div>
    
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Adresse</th>
                <th>Département</th>
                <th>Rôle</th>
                <th>Date d'embauche</th>
                <th>action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Requête SQL pour récupérer la liste des agents
        $sql = "SELECT id, nom, prenom, email, telephone, adresse, departement, role, date_embauche FROM agents";
        $result = $conn->query($sql);

        if ($result === false) {
            echo "<tr><td colspan='8'>Erreur SQL : " . htmlspecialchars($conn->error) . "</td></tr>";
        } elseif ($result->num_rows > 0) {
            // Afficher chaque agent dans le tableau
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                echo "<td>" . htmlspecialchars($row['prenom']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['telephone']) . "</td>";
                echo "<td>" . htmlspecialchars($row['adresse']) . "</td>";
                echo "<td>" . htmlspecialchars($row['departement']) . "</td>";
                echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                echo "<td>" . htmlspecialchars($row['date_embauche']) . "</td>";
                echo "<td>
                    <button onclick='modifierAgent(" . htmlspecialchars($row['id']) . ")' style='background-color: red;cursor: pointer;'>Modifier</button>
    <button onclick='confirmerSuppression(" . htmlspecialchars($row['id']) . ")'>Supprimer</button>
</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>Aucun agent enregistré pour le moment.</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <!-- Formulaire de Paiement de l'Agent -->
    <form action="submit_agent_payment.php" method="post">
        <h2>Paiement de l'Agent</h2>
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required><br>

        <label for="agent_name">Nom de l'agent:</label>
        <input type="text" id="agent_name" name="agent_name" required autocomplete="off"><br>

        <ul id="agent_list" style="list-style-type: none; padding: 0; margin: 0;"></ul>

        <label for="category">Catégorie:</label>
        <select id="category" name="category" required>
            <option value="avance">Avance</option>
            <option value="payment">Paiement</option>
        </select><br>

        <label for="amount">Montant:</label>
        <input type="number" id="amount" name="amount" step="0.01" required><br>

        <button type="submit">Enregistrer le paiement</button>
    </form>

    <!-- Liste des Paiements -->
    <div style="background-color: #f0f0f0; text-align: center;">
        <h2>Liste des Paiements</h2>
    </div>
    
    <table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Date de Paiement</th>
            <th>Nom de l'Agent</th>
            <th>Catégorie</th>
            <th>Montant ($)</th>
            <th>Date de Création</th>
        </tr>
    </thead>
    <tbody>
    <?php
    // Requête SQL pour récupérer les paiements avec les informations des agents
    $sql = "SELECT id, date_paiement, nom_agent, categorie, montant, created_at FROM paiements";
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['date_paiement']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nom_agent']) . "</td>";
                echo "<td>" . htmlspecialchars($row['categorie']) . "</td>";
                echo "<td>" . htmlspecialchars(number_format($row['montant'], 2, ',', ' ')) . " $</td>";
                echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6' class='error'>Aucun paiement trouvé.</td></tr>";
        }
    } else {
        echo "<tr><td colspan='6' class='error'>Erreur lors de l'exécution de la requête: " . htmlspecialchars($conn->error) . "</td></tr>";
    }

    // Libérer les résultats
    if ($result) {
        $result->free();
    }

    // Fermer la connexion
    $conn->close();
    ?>
    </tbody>
</table>



    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <img src="assets/img/logo_2.png" alt="Company Logo" class="logo">
            <p>&copy; 2024 Chife Hotel. Tous droits réservés.</p>
            <div class="social-links">
                <a href="https://www.facebook.com/" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.twitter.com/" target="_blank"><i class="fab fa-twitter"></i></a>
                <a href="https://www.instagram.com/" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="https://www.linkedin.com/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </footer>

    <!-- Script pour l'Auto-Complétion des Noms d'Agents -->
    <script>
        function modifierAgent(id) {
    // Rediriger vers une page de modification avec l'ID de l'agent
    window.location.href = 'modifier_agent.php?id=' + id;
}

function confirmerSuppression(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet agent ?')) {
        // Si l'utilisateur confirme, rediriger vers un script de suppression
        window.location.href = 'supprimer_agent.php?id=' + id;
    }
}
    document.getElementById('agent_name').addEventListener('input', function() {
        let query = this.value;

        if (query.length > 2) {
            fetch('get_agents.php?query=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    let list = document.getElementById('agent_list');
                    list.innerHTML = '';

                    data.forEach(agent => {
                        let listItem = document.createElement('li');
                        listItem.textContent = agent;
                        listItem.style.cursor = 'pointer';
                        listItem.addEventListener('click', function() {
                            document.getElementById('agent_name').value = this.textContent;
                            list.innerHTML = '';
                        });
                        list.appendChild(listItem);
                    });
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des agents:', error);
                });
        } else {
            document.getElementById('agent_list').innerHTML = '';
        }
    });
    </script>
</body>
</html>
