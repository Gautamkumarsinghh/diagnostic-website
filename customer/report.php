<?php
session_start();
include '../db/config.php';
require('../admin/fpdf.php');

$email=$_SESSION['customer'];

$pdf=new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',12);

$pdf->Cell(0,10,'My Diagnostic Report',0,1,'C');

$q=mysqli_query($conn,"SELECT * FROM bookings WHERE email='$email'");

while($r=mysqli_fetch_assoc($q)){
$pdf->Cell(40,8,$r['test'],1);
$pdf->Cell(40,8,$r['status'],1);
$pdf->Ln();
}

$pdf->Output('D','MyReport.pdf');
