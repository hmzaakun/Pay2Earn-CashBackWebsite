<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

include('includes/db.php');

$idachat = htmlspecialchars($_GET['idachat']);

$q = "SELECT * FROM achat WHERE idachat = ?";
$q = $db->prepare($q);
$q->execute([
      $idachat
]);

$q = $q->fetch();


$x = "SELECT a.idarticle,a.nom,a.prix,b.quantite FROM article AS a INNER JOIN `article-achat`AS b ON b.idarticle = a.idarticle AND b.idachat = ?";
$x = $db->prepare($x);
$x->execute([
      $idachat
]);

$x = $x->fetchAll();

$client = "SELECT a.prenom,a.nom,a.email,a.ncarte FROM client AS a INNER JOIN achat AS b ON b.idclient = a.idclient AND b.idachat = ?";
$client = $db->prepare($client);
$client->execute([
      $idachat
]);

$client = $client->fetch();


$save = 'facture/' . $q['idclient'] . '_' . $idachat . '.pdf';

if (!file_exists($save)) {

      require('invoice.php');

      $pdf = new PDF_Invoice( 'P', 'mm', 'A4' );
      $pdf->AddPage();
      $pdf->addSociete( "Pay2earn",
                        "242 Rue du Faubourg Saint-Antoine\n" .
                        "75012 Paris\n".
                        "R.C.S. PARIS B 000 000 007\n" .
                        "Capital : 2 " . EURO );
      $pdf->fact_dev( "Facture ", $q['idachat'] );
      $pdf->temporaire( "facture officielle" );
      $pdf->addDate( date('d/m/Y'));
      $pdf->addClient($q['idclient']);
      $pdf->addPageNumber("1");
      $pdf->addClientAdresse($client['nom'] . "\n" . $client['prenom'] . "\n" . $client['email'] . "\nNo de carte : " . $client['ncarte']);
      $pdf->addReglement("Paiement internet");
      $pdf->addNumTVA("FR888777666");
      $pdf->addReference('Facture '.$q['idachat'].' du '. date('d/m/Y'));
      $cols=array( "REFERENCE"    => 23,
                   "DESIGNATION"  => 78,
                   "QUANTITE"     => 22,
                   "P.U. HT"      => 26,
                   "MONTANT H.T." => 30,
                   "TVA"          => 11 );
      $pdf->addCols( $cols);
      $cols=array( "REFERENCE"    => "L",
                   "DESIGNATION"  => "L",
                   "QUANTITE"     => "C",
                   "P.U. HT"      => "R",
                   "MONTANT H.T." => "R",
                   "TVA"          => "C" );
      $pdf->addLineFormat( $cols);
      $pdf->addLineFormat($cols);

      $y    = 109;

      $prix = 0;
      $quantite = 0;
      foreach ($x as $p) {

        $line = array("REFERENCE"    => 'id :' . $p['idarticle'],
                      "DESIGNATION"  => $p['nom'],
                      "QUANTITE"     => $p['quantite'],
                      "P.U. HT"      => $p['prix'],
                      "MONTANT H.T." => $p['prix']*$p['quantite'],
                      "TVA"          => $p['quantite'] );
                      $prix+=$p['prix']*$p['quantite'];
                      $quantite+=$p['quantite'];
          $size = $pdf->addLine( $y, $line );
          $y   += $size + 2;
      }

      $pdf->addCadreTVAs();


      $tot_prods = array( array ( "px_unit" => $prix, "qte" => 1, "tva" => 1 ));
      $tab_tva = array( "1"       => 0);
      $params  = array( "RemiseGlobale" => 0,
                            "remise_tva"     => 1,       // {la remise s'applique sur ce code TVA}
                            "remise"         => 0,       // {montant de la remise}
                            "remise_percent" => 0,      // {pourcentage de remise sur ce montant de TVA}
                        "FraisPort"     => 1,
                            "portTTC"        => 0,      // montant des frais de ports TTC
                                                         // par defaut la TVA = 19.6 %
                            "portHT"         => 0,       // montant des frais de ports HT
                            "portTVA"        => 0,    // valeur de la TVA a appliquer sur le montant HT
                        "AccompteExige" => 1,
                            "accompte"         => 0,     // montant de l'acompte (TTC)
                            "accompte_percent" => 0,    // pourcentage d'acompte (TTC)
                        "Remarque" => "Paye." );

      $pdf->addTVAs( $params, $tab_tva, $tot_prods);
      $pdf->addCadreEurosFrancs();
      $save = 'facture/' . $q['idclient'] . '_' . $idachat . '.pdf';

      $q = "UPDATE achat SET facture = ? WHERE idachat = ?";
      $q = $db->prepare($q);
      $q->execute([
            $save,
            $idachat
      ]);

      $pdf->Output($save,'F');
}


header("location:".$save);
?>
