<?php
require('../../script/fpdf/fpdf.php');
require_once '../../dbconnexion.php';

class PDF extends FPDF
{
    function Header()
    {
        // Logo
        $this->Image('../../assets/img/logo_2.png', 0, 0, 210);
        // Police Arial gras 24
        $this->SetFont('Arial','B',24);
        // Titre
        $this->Cell(0,40,utf8_decode('Rapport des produits en stock'),0,1,'C');
        // Saut de ligne
        $this->Ln(20);
    }
}

$pdf = new PDF();
$pdf->AddPage();

if (isset($_GET['date_debut']) && isset($_GET['date_fin'])) {
    $date_debut = $_GET['date_debut'];
    $date_fin = $_GET['date_fin'];

    $sql = "SELECT * FROM produits WHERE date_entree BETWEEN ? AND ? AND quantite > 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $date_debut, $date_fin);
    $stmt->execute();
    $result = $stmt->get_result();

    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(40,10,utf8_decode('Date d\'entrée'),1);
    $pdf->Cell(50,10,utf8_decode('Nom du produit'),1);
    $pdf->Cell(30,10,utf8_decode('Quantité'),1);
    $pdf->Cell(70,10,utf8_decode('Emplacement du stock'),1);
    $pdf->Ln();

    $pdf->SetFont('Arial','',12);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(40,10,utf8_decode($row['date_entree']),1);
        $pdf->Cell(50,10,utf8_decode($row['nom_produit']),1);
        $pdf->Cell(30,10,$row['quantite'],1);
        $pdf->Cell(70,10,utf8_decode($row['emplacement_stock']),1);
        $pdf->Ln();
    }

    $stmt->close();
    $conn->close();
} else {
    $pdf->Cell(0,10,utf8_decode('Aucune date spécifiée'),0,1);
}

$pdf->Output('D', 'rapport_stock.pdf');
