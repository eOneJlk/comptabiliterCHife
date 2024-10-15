<?php
require('../../script/fpdf/fpdf.php');
require_once '../../dbconnexion.php';

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial','B',15);
        $this->Cell(0,10,'Rapport des produits en stock',0,1,'C');
        $this->Ln(10);
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
    $pdf->Cell(40,10,'Date d\'entrée',1);
    $pdf->Cell(50,10,'Nom du produit',1);
    $pdf->Cell(30,10,'Quantité',1);
    $pdf->Cell(70,10,'Emplacement du stock',1);
    $pdf->Ln();

    $pdf->SetFont('Arial','',12);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(40,10,$row['date_entree'],1);
        $pdf->Cell(50,10,$row['nom_produit'],1);
        $pdf->Cell(30,10,$row['quantite'],1);
        $pdf->Cell(70,10,$row['emplacement_stock'],1);
        $pdf->Ln();
    }

    $stmt->close();
    $conn->close();
} else {
    $pdf->Cell(0,10,'Aucune date spécifiée',0,1);
}

$pdf->Output('D', 'rapport_stock.pdf');