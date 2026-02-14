<?php
include '../db/config.php';
require('fpdf.php');

class PDF extends FPDF{

function Header(){
    $this->SetFont('Arial','B',16);
    $this->Cell(0,10,'My Diagnostic Lab - Bookings Report',0,1,'C');
    $this->SetFont('Arial','',10);
    $this->Cell(0,6,'Report Date: '.date("d-m-Y"),0,1,'C');
    $this->Ln(5);

    $this->SetFont('Arial','B',10);
    $this->Cell(10,8,'ID',1);
    $this->Cell(40,8,'Name',1);
    $this->Cell(40,8,'Mobile',1);
    $this->Cell(50,8,'Test',1);
    $this->Cell(30,8,'Status',1);
    $this->Ln();
}

function Footer(){
    $this->SetY(-15);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
}
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);

$q = mysqli_query($conn,"
SELECT * FROM bookings 
WHERE name!='' 
AND mobile!='' 
AND test!=''
ORDER BY id DESC
");

while($row=mysqli_fetch_assoc($q)){

$pdf->Cell(10,8,$row['id'],1);
$pdf->Cell(40,8,$row['name'],1);
$pdf->Cell(40,8,$row['mobile'],1);
$pdf->Cell(50,8,$row['test'],1);

$statusColor = ($row['status']=="Completed") ? [0,150,0] : [255,140,0];
$pdf->SetTextColor($statusColor[0],$statusColor[1],$statusColor[2]);
$pdf->Cell(30,8,$row['status'],1);
$pdf->SetTextColor(0,0,0);

$pdf->Ln();
}

$pdf->Output('D','bookings_report.pdf');
exit;

?>
