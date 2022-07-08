<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

include('includes/db.php');

$ncarte = htmlspecialchars($_GET['ncarte']);

$req = "SELECT * FROM client WHERE ncarte = ?";
$req = $db->prepare($req);
$req->execute([
    $ncarte
]);
$req = $req->fetch();

require('fpdf.php');
//require('code39.php');

class PDF extends FPDF
{
// En-tête
function Header()
{
    // Logo
    $this->Image('images/logo.png',10,6,30);
    // Police Arial gras 15
    $this->SetFont('Arial','B',15);
    // Décalage à droite
    $this->Cell(80);
    // Titre
    $this->Cell(30,10,'Pay2earn',1,0,'C');
    // Décalage à droite
    $this->Cell(20);
    // Police Arial gras 15
    $this->SetFont('Arial','',15);
    // détail
    $this->Cell(30,10,'pay2earn.p2e@gmail.com');
    // Saut de ligne
    $this->Ln(20);
    $this->Ln(20);
}

// Pied de page
function Footer()
{
    // Positionnement à 1,5 cm du bas
    $this->SetY(-15);
    // Police Arial italique 8
    $this->SetFont('Arial','I',8);
    $this->Cell(30,10,'pay2earn');
    // Numéro de page
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}

// Instanciation de la classe dérivée
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

$pdf->Cell(0,10,'Ma carte client : ',0,1);
$pdf->Cell(0,10,"Nom :  " . $req['nom'] . "\n" ,0,1);
$pdf->Cell(0,10,"Prenom :  " . $req['prenom'] . "\n" ,0,1);
$pdf->Cell(0,10,'Mail : ' . $req['email'] . "\n",0,1);
$pdf->Cell(0,10,'Numero de carte : ' . $req['ncarte'] . "\n",0,1);
$pdf->Code39(80,40,$req['ncarte'],1,10);

$pdf->Output();
?>
