<?php
require_once '../../dbconnexion.php';
require('../../script/fpdf/fpdf.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $date = $_POST['date'];
    $departement = $_POST['departement'];
    $type = $_POST['type'];
    $produit = $_POST['produit'];

    if ($_POST['action'] == 'generate_report') {
        // Générer le rapport PDF
        class PDF extends FPDF
        {
            function Header()
            {
                $this->SetFont('Arial','B',15);
                $this->Cell(0,10,'Rapport journalier',0,1,'C');
                $this->Ln(10);
            }
        }

        $pdf = new PDF();
        $pdf->AddPage();

        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,10,'Date: ' . $date,0,1);
        $pdf->Cell(0,10,'Département: ' . $departement,0,1);
        $pdf->Cell(0,10,'Type de rapport: ' . $type,0,1);
        if (!empty($produit)) {
            $pdf->Cell(0,10,'Produit: ' . $produit,0,1);
        }
        $pdf->Ln(10);

        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(40,10,'Produit',1);
        $pdf->Cell(30,10,'Quantité',1);
        $pdf->Cell(40,10,'Emplacement',1);
        $pdf->Cell(40,10,'Date d\'entrée',1);
        $pdf->Ln();

        $sql = "SELECT * FROM produits WHERE date_entree = ? AND date_suppression IS NULL";
        if (!empty($produit)) {
            $sql .= " AND nom_produit = ?";
        }
        $stmt = $conn->prepare($sql);
        if (!empty($produit)) {
            $stmt->bind_param("ss", $date, $produit);
        } else {
            $stmt->bind_param("s", $date);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $pdf->SetFont('Arial','',12);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(40,10,$row['nom_produit'],1);
            $pdf->Cell(30,10,$row['quantite'],1);
            $pdf->Cell(40,10,$row['emplacement_stock'],1);
            $pdf->Cell(40,10,$row['date_entree'],1);
            $pdf->Ln();
        }

        $pdf->Output('D', 'rapport_' . $date . '.pdf');
        
        $stmt->close();
    }

    $conn->close();
} else {
    echo "Méthode non autorisée.";
}
?>