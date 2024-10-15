<?php
require_once '../../dbconnexion.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['agent_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Vérifier si l'ID de l'agent à modifier est fourni
if (!isset($_GET['id'])) {
    header("Location: personnel.php");
    exit();
}

$agent_id = $_GET['id'];

// Récupérer les informations de l'agent
$sql = "SELECT * FROM agents WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$result = $stmt->get_result();
$agent = $result->fetch_assoc();

if (!$agent) {
    echo "Agent non trouvé.";
    exit();
}

// Traitement du formulaire de modification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($conn->real_escape_string($_POST['nom']));
    $prenom = htmlspecialchars($conn->real_escape_string($_POST['prenom']));
    $email = htmlspecialchars($conn->real_escape_string($_POST['email']));
    $telephone = htmlspecialchars($conn->real_escape_string($_POST['telephone']));
    $adresse = htmlspecialchars($conn->real_escape_string($_POST['adresse']));
    $departement = htmlspecialchars($conn->real_escape_string($_POST['departement']));
    $role = htmlspecialchars($conn->real_escape_string($_POST['role']));
    $date_embauche = htmlspecialchars($conn->real_escape_string($_POST['date_embauche']));

    $update_sql = "UPDATE agents SET nom = ?, prenom = ?, email = ?, telephone = ?, adresse = ?, departement = ?, role = ?, date_embauche = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssssssi", $nom, $prenom, $email, $telephone, $adresse, $departement, $role, $date_embauche, $agent_id);

    if ($update_stmt->execute()) {
        echo "Agent modifié avec succès.";
        header("Location: personnel.php");
        exit();
    } else {
        echo "Erreur lors de la modification de l'agent : " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Agent</title>
</head>
<body>
    <h1>Modifier un Agent</h1>
    <form action="" method="post">
        <label for="nom">Nom:</label>
        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($agent['nom']); ?>" required><br>

        <label for="prenom">Prénom:</label>
        <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($agent['prenom']); ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($agent['email']); ?>" required><br>

        <label for="telephone">Téléphone:</label>
        <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($agent['telephone']); ?>" required><br>

        <label for="adresse">Adresse:</label>
        <textarea id="adresse" name="adresse" required><?php echo htmlspecialchars($agent['adresse']); ?></textarea><br>

        <label for="departement">Département:</label>
        <select id="departement" name="departement" required>
            <option value="lundry" <?php echo $agent['departement'] == 'lundry' ? 'selected' : ''; ?>>Lundry</option>
            <option value="stock" <?php echo $agent['departement'] == 'stock' ? 'selected' : ''; ?>>Stock</option>
            <option value="reception" <?php echo $agent['departement'] == 'reception' ? 'selected' : ''; ?>>Reception</option>
            <option value="caisse" <?php echo $agent['departement'] == 'caisse' ? 'selected' : ''; ?>>Caisse</option>
            <option value="bar" <?php echo $agent['departement'] == 'bar' ? 'selected' : ''; ?>>Bar</option>
        </select><br>

        <label for="role">Rôle:</label>
        <select id="role" name="role" required>
            <option value="agent" <?php echo $agent['role'] == 'agent' ? 'selected' : ''; ?>>Agent</option>
            <option value="dg" <?php echo $agent['role'] == 'dg' ? 'selected' : ''; ?>>Directeur général</option>
            <option value="admin" <?php echo $agent['role'] == 'admin' ? 'selected' : ''; ?>>Administrateur</option>
            <option value="stock" <?php echo $agent['role'] == 'stock' ? 'selected' : ''; ?>>Stock</option>
            <option value="caisse" <?php echo $agent['role'] == 'caisse' ? 'selected' : ''; ?>>Caisse</option>
            <option value="comptabilite" <?php echo $agent['role'] == 'comptabilite' ? 'selected' : ''; ?>>Comptabilité</option>
            <option value="gerant" <?php echo $agent['role'] == 'gerant' ? 'selected' : ''; ?>>Gérant</option>
        </select><br>

        <label for="date_embauche">Date d'embauche:</label>
        <input type="date" id="date_embauche" name="date_embauche" value="<?php echo htmlspecialchars($agent['date_embauche']); ?>" required><br>

        <button type="submit">Modifier l'agent</button>
    </form>
</body>
</html>