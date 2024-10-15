<?php
session_start();
require_once '../../dbconnexion.php';

// Vérification de l'autorisation
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'caisse')) {
    header("Location: ../../acces_refuse.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['transaction_id'])) {
    $transaction_id = $_POST['transaction_id'];
    
    // Récupérer les détails de la transaction
    $query = "SELECT * FROM transactions_caisse WHERE id = ? AND etat = 'pending'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $transaction = $result->fetch_assoc();
        // Afficher un formulaire pour modifier la transaction
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Modifier la transaction</title>
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
                            Modifier la transaction
                        </li>
                    </ul>
                </nav>
            </header>

            <main>
                <section style="background-color: #ffffff; border-radius: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 30px; max-width: 800px; margin: 0 auto;">
                    <form action="traiter_modification.php" method="POST">
                        <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                        
                        <div style="margin-bottom: 20px;">
                            <label for="date" style="display: block; margin-bottom: 5px; font-weight: bold;">Date:</label>
                            <input type="date" id="date" name="date" value="<?php echo $transaction['date']; ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label for="type" style="display: block; margin-bottom: 5px; font-weight: bold;">Type:</label>
                            <select id="type" name="type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                                <option value="entree" <?php echo ($transaction['type'] == 'entree') ? 'selected' : ''; ?>>Entrée</option>
                                <option value="sortie" <?php echo ($transaction['type'] == 'sortie') ? 'selected' : ''; ?>>Sortie</option>
                            </select>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label for="montant" style="display: block; margin-bottom: 5px; font-weight: bold;">Montant:</label>
                            <input type="number" id="montant" name="montant" step="0.01" value="<?php echo $transaction['montant']; ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label for="details" style="display: block; margin-bottom: 5px; font-weight: bold;">Détails:</label>
                            <textarea id="details" name="details" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; height: 100px;"><?php echo $transaction['details']; ?></textarea>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label for="compte" style="display: block; margin-bottom: 5px; font-weight: bold;">Compte:</label>
                            <input type="text" id="compte" name="compte" value="<?php echo $transaction['compte']; ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        
                        <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">Enregistrer les modifications</button>
                    </form>
                </section>
            </main>
        </body>
        </html>
        <?php
    } else {
        echo "<p style='text-align: center; color: red; font-size: 18px;'>Transaction non trouvée ou déjà approuvée.</p>";
    }
    
    $stmt->close();
} else {
    echo "<p style='text-align: center; color: red; font-size: 18px;'>ID de transaction non fourni.</p>";
}

$conn->close();
?>
