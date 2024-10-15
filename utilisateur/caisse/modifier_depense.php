<?php
session_start();
require_once '../../dbconnexion.php';

// Vérification de l'autorisation
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'caissier')) {
    header("Location: ../../acces_refuse.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['depense_id'])) {
    $depense_id = $_POST['depense_id'];
    
    // Récupérer les détails de la dépense
    $query = "SELECT * FROM depenses WHERE id = ? AND etat = 'pending'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $depense_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $depense = $result->fetch_assoc();
        // Afficher un formulaire pour modifier la dépense
        ?>
        <h2>Modifier la dépense</h2>
        <form action="traiter_modification_depense.php" method="POST">
            <input type="hidden" name="depense_id" value="<?php echo $depense['id']; ?>">
            
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" value="<?php echo $depense['date']; ?>" required><br><br>
            
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($depense['nom']); ?>" required><br><br>
            
            <label for="description">Description:</label>
            <textarea id="description" name="description"><?php echo htmlspecialchars($depense['description']); ?></textarea><br><br>
            
            <label for="amount">Montant:</label>
            <input type="number" id="amount" name="amount" step="0.01" value="<?php echo $depense['amount']; ?>" required><br><br>
            
            <button type="submit">Enregistrer les modifications</button>
        </form>
        <?php
    } else {
        echo "Dépense non trouvée ou déjà approuvée.";
    }
    
    $stmt->close();
} else {
    echo "ID de dépense non fourni.";
}

$conn->close();
?>
