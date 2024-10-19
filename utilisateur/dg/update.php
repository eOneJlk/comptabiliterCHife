<?php
session_start();

// Vérification du rôle
$roles_autorises = ['admin', 'gerant'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles_autorises)) {
    header("Location: ../../acces_refuse.php");
    exit();
}

// Vérification de l'ID de l'agent
if (!isset($_GET['id'])) {
    header("Location: erreur.php?message=ID_agent_manquant");
    exit();
}

$agent_id = $_GET['id'];

// Récupération des informations de l'agent
$query = "SELECT nom, prenom, email, telephone, adresse, departement, role FROM agents WHERE id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $agent_id);
    $stmt->execute();
    $stmt->bind_result($nom, $prenom, $email, $telephone, $adresse, $departement, $role);
    if ($stmt->fetch()) {
        // Stockage des informations dans des variables locales au lieu de la session
        $agent_nom = $nom;
        $agent_prenom = $prenom;
        $agent_email = $email;
        $agent_telephone = $telephone;
        $agent_adresse = $adresse;
        $agent_departement = $departement;
        $agent_role = $role;
    } else {
        header("Location: erreur.php?message=Agent_non_trouve");
        exit();
    }
    $stmt->close();
} else {
    header("Location: erreur.php?message=Erreur_preparation_requete");
    exit();
}
?>

<form method="POST" action="process_update.php">
    <input type="hidden" name="id" value="<?php echo $agent_id; ?>">
    
    Nom:
    <input type="text" name="nom" value="<?php echo htmlspecialchars($agent_nom); ?>" required>
    
    Prénom:
    <input type="text" name="prenom" value="<?php echo htmlspecialchars($agent_prenom); ?>" required>
    
    Email:
    <input type="email" name="email" value="<?php echo htmlspecialchars($agent_email); ?>" required>
    
    Téléphone:
    <input type="tel" name="telephone" value="<?php echo htmlspecialchars($agent_telephone); ?>" required>
    
    Adresse:
    <input type="text" name="adresse" value="<?php echo htmlspecialchars($agent_adresse); ?>" required>
    
    Département:
    <input type="text" name="departement" value="<?php echo htmlspecialchars($agent_departement); ?>" required>
    
    Rôle:
    <select name="role" required>
        <option value="admin" <?php echo $agent_role === 'admin' ? 'selected' : ''; ?>>Admin</option>
        <option value="gerant" <?php echo $agent_role === 'gerant' ? 'selected' : ''; ?>>Gérant</option>
        <option value="caissier" <?php echo $agent_role === 'caissier' ? 'selected' : ''; ?>>Caissier</option>
        <!-- Ajoutez d'autres rôles si nécessaire -->
    </select>
    
    <button type="submit">Mettre à jour</button>
</form>
