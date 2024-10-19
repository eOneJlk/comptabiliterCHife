<?php
require('../../script/fpdf/fpdf.php');
include '../../dbconnexion.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID du rapport non spécifié.");
}

$rapport_id = intval($_GET['id']);

// Récupérez les détails du rapport
$sql = "SELECT * FROM rapports WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $rapport_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Rapport non trouvé.");
}

$rapport = $result->fetch_assoc();

class PDF extends FPDF
{
    function Header()
    {
        // Logo
        $this->Image('../../assets/img/logo_2.png', 0, 0, 210);
        // Police Arial gras 24
        $this->SetFont('Arial','B',24);
        // Titre
        $this->Cell(0,40,utf8_decode('Rapport Détaillé'),0,1,'C');
        // Saut de ligne
        $this->Ln(20);
    }
    
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

// Informations du rapport
$pdf->Cell(0,10,'Date: '.$rapport['date'],0,1);

// Récupérer le nom de l'agent
$agent_column = null;
foreach ($rapport as $key => $value) {
    if (strpos(strtolower($key), 'agent') !== false) {
        $agent_column = $key;
        break;
    }
}
if ($agent_column !== null && !empty($rapport[$agent_column])) {
    $agent_id = $rapport[$agent_column];
    $sql_agent = "SELECT nom, prenom FROM agents WHERE id = ?";
    $stmt = $conn->prepare($sql_agent);
    $stmt->bind_param("i", $agent_id);
    $stmt->execute();
    $result_agent = $stmt->get_result();
    if ($result_agent->num_rows > 0) {
        $agent = $result_agent->fetch_assoc();
        $pdf->Cell(0,10,'Agent: '.$agent['nom'].' '.$agent['prenom'],0,1);
    } else {
        $pdf->Cell(0,10,'Agent: Inconnu',0,1);
    }
} else {
    $pdf->Cell(0,10,'Agent: Information non disponible',0,1);
}

$pdf->Cell(0,10,'Nombre de Check Out: '.$rapport['nombre_check_out'],0,1);
$pdf->Cell(0,10,'Nombre de Check In: '.$rapport['nombre_check_in'],0,1);
$pdf->Cell(0,10,'Chambres Disponibles: '.$rapport['chambre_disponible'],0,1);
$pdf->Cell(0,10,'Entrée Cash: '.number_format($rapport['entree_cash'], 2, ',', ' ').' $',0,1);
$pdf->Cell(0,10,'Crédit: '.number_format($rapport['credit'], 2, ',', ' ').' $',0,1);
$pdf->Cell(0,10,'Entrée Airtel Money: '.number_format($rapport['entree_airtel_money'], 2, ',', ' ').' $',0,1);
$pdf->Cell(0,10,'Entrée Carte POS: '.number_format($rapport['entree_carte_pos'], 2, ',', ' ').' $',0,1);

$total_entrees = $rapport['entree_cash'] + $rapport['credit'] + $rapport['entree_airtel_money'] + $rapport['entree_carte_pos'];
$pdf->Cell(0,10,'Total Entrées: '.number_format($total_entrees, 2, ',', ' ').' $',0,1);

$pdf->Output('D', 'Rapport_'.$rapport_id.'.pdf');
