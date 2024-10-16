<?php
require_once '../../dbconnexion.php';
require_once '../../script/fpdf/fpdf.php'; 
// Vérifier si une date a été passée
if (!isset($_GET['date'])) {
    die("Aucune date spécifiée.");
}

$date = $_GET['date'];

// Récupérer les données du rapport pour la date spécifiée
$sql = "SELECT * FROM rapports WHERE date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Aucun rapport trouvé pour cette date.");
}

$rapport = $result->fetch_assoc();

// Créer un nouveau PDF
$pdf = new FPDF();
$pdf->AddPage();

// En-tête
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Soubassement du ' . $date, 0, 1, 'C');
$pdf->Ln(10);

// Contenu du rapport
$pdf->SetFont('Arial', '', 12);

$fields = [
    'Nom' => $rapport['nom'],
    'Nombre de Check Out' => $rapport['nombre_check_out'],
    'Nombre de Check In' => $rapport['nombre_check_in'],
    'Chambres Disponibles' => $rapport['chambre_disponible'],
    'Entrée Cash' => number_format($rapport['entree_cash'], 2, ',', ' ') . ' $',
    'Crédit' => number_format($rapport['credit'], 2, ',', ' ') . ' $',
    'Entrée Airtel Money' => number_format($rapport['entree_airtel_money'], 2, ',', ' ') . ' $',
    'Entrée Carte POS' => number_format($rapport['entree_carte_pos'], 2, ',', ' ') . ' $'
];

foreach ($fields as $label => $value) {
    $pdf->Cell(0, 10, utf8_decode($label . ': ' . $value), 0, 1);
}

$pdf->Ln(10);
$pdf->MultiCell(0, 10, utf8_decode('Contenu: ' . $rapport['contenu']));

// Générer le PDF
$pdf->Output('D', 'Soubassement_' . $date . '.pdf');

$stmt->close();
$conn->close();
