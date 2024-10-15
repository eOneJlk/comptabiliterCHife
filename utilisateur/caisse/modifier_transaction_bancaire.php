<?php
session_start();
require_once '../../dbconnexion.php';

// Vérification de l'autorisation
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'caissier')) {
    header("Location: ../../acces_refuse.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['transaction_id'])) {
    $transaction_id = $_POST['transaction_id'];
    
    // Récupérer les détails de la transaction
    $query = "SELECT * FROM transactions_bancaires WHERE id = ? AND etat = 'pending'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $transaction = $result->fetch_assoc();
        // Afficher un formulaire pour modifier la transaction
        ?>
        <h2>Modifier la transaction bancaire</h2>
        <form action="traiter_modification_bancaire.php" method="POST">
            <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
            
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" value="<?php echo $transaction['date']; ?>" required><br><br>
            
            <label for="transaction_type">Type de transaction:</label>
            <input type="text" id="transaction_type" name="transaction_type" value="<?php echo $transaction['transaction_type']; ?>" required><br><br>
            
            <label for="amount">Montant:</label>
            <input type="number" id="amount" name="amount" step="0.01" value="<?php echo $transaction['amount']; ?>" required><br><br>
            
            <label for="invoice_number">Numéro de facture:</label>
            <input type="text" id="invoice_number" name="invoice_number" value="<?php echo $transaction['invoice_number']; ?>"><br><br>
            
            <label for="slip_number">Numéro de bordereau:</label>
            <input type="text" id="slip_number" name="slip_number" value="<?php echo $transaction['slip_number']; ?>"><br><br>
            
            <button type="submit">Enregistrer les modifications</button>
        </form>
        <?php
    } else {
        echo "Transaction non trouvée ou déjà approuvée.";
    }
    
    $stmt->close();
} else {
    echo "ID de transaction non fourni.";
}

$conn->close();
?>
