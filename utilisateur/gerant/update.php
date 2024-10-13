<?php
require_once '../../dbconnexion.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['agent_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: ../../login.php");
    exit();
}
// Récupérer l'ID de l'agent depuis la session
$agent_id = $_SESSION['agent_id'];

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($conn->real_escape_string($_POST['nom']));
    $prenom = htmlspecialchars($conn->real_escape_string($_POST['prenom']));
    $email = htmlspecialchars($conn->real_escape_string($_POST['email']));
    $telephone = htmlspecialchars($conn->real_escape_string($_POST['telephone']));
    $adresse = htmlspecialchars($conn->real_escape_string($_POST['adresse']));
    $departement = htmlspecialchars($conn->real_escape_string($_POST['departement']));
    $role = htmlspecialchars($conn->real_escape_string($_POST['role']));

    // Requête SQL pour mettre à jour les informations de l'agent
    $sql = "UPDATE agents SET nom = ?, prenom = ?, email = ?, telephone = ?, adresse = ?, departement = ?, role = ? WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssssssi", $nom, $prenom, $email, $telephone, $adresse, $departement, $role, $agent_id);

        // Exécuter la requête
        if ($stmt->execute()) {
            echo "Informations mises à jour avec succès!";
        } else {
            echo "Erreur lors de la mise à jour des informations : " . $conn->error;
        }

        $stmt->close();
    }
}

// Fermer la connexion
$conn->close();
?>

<!-- Formulaire de mise à jour des informations -->
<form action="update.php" method="post">
    <label for="nom">Nom:</label>
    <input type="text" id="nom" name="nom" value="<?php echo $_SESSION['nom']; ?>" required><br>
    
    <label for="prenom">Prénom:</label>
    <input type="text" id="prenom" name="prenom" value="<?php echo $_SESSION['prenom']; ?>" required><br>
    
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?php echo $_SESSION['email']; ?>" required><br>
    
    <label for="telephone">Téléphone:</label>
    <input type="tel" id="telephone" name="telephone" value="<?php echo $_SESSION['telephone']; ?>" required><br>
    
    <label for="adresse">Adresse:</label>
    <input type="text" id="adresse" name="adresse" value="<?php echo $_SESSION['adresse']; ?>" required><br>
    
    <label for="departement">Département:</label>
    <input type="text" id="departement" name="departement" value="<?php echo $_SESSION['departement']; ?>" required><br>
    
    <label for="role">Rôle:</label>
    <input type="text" id="role" name="role" value="<?php echo $_SESSION['role']; ?>" required><br>
    
    <button type="submit">Mettre à jour</button>
</form>

