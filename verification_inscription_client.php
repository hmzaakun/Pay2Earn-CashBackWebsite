<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require "PHPMailer/PHPMailerAutoload.php";
include('includes/db.php');
$email = htmlspecialchars($_POST['email']);
$nom =htmlspecialchars($_POST['nom']);
$prenom =htmlspecialchars($_POST['prenom']);


session_start();
if(isset($_POST['captcha'])){
    if($_POST['captcha'] == $_SESSION['captcha']){

    }
        else{
        header('location: inscription_client.php?message=Captcha invalide ...');
        exit;
    }
}


if( !isset($_POST['email']) || empty($_POST['email']) ||
 !isset($_POST['password']) || empty($_POST['password']) ||
    !isset($_POST['nom']) || empty($_POST['nom']) ||
    !isset($_POST['prenom']) || empty($_POST['prenom']) ||
         !isset($_POST['captcha']) || empty($_POST['captcha']) ||
          !isset($_POST['password2']) || empty($_POST['password2'])  ){
	header('location: inscription_client.php?message=Vous devez  tout remplir.');

	exit;
}


/*
  if(isset($_POST['parrain']) && !empty($_POST['parrain'])) {
  $parrainoui = 0;
  $parrain = htmlspecialchars($_POST['parrain']);
  $wsh = "SELECT *  FROM entreprise";
  // Préparation de la db
  $reqouz = $db->prepare($wsh);
  // Execution de la requête
  $reqouz->execute();
  // Récupération de la toute les ligne de résultats
  $resultat=$reqouz->fetchAll();

  foreach ( $resultat as $resu ) {
    if ($parrain == $resu["codeparrain"]){
      $parrainoui = 1;
      $updatep=$db->prepare("UPDATE entreprise SET parrain = $parrain WHERE email = '$email' ");
      $updatep->execute();
      header('location: inscription_entreprise.php?message=SAMARCGH');
      exit;
    }
  }
    if ($parrainoui==0){
      header('location: inscription_entreprise.php?message=Le parrain n\'existe pas.');
      exit;
    }
  }
*/




$password=$_POST['password'];
$password2=$_POST['password2'];


if ($password==$password2) {

			}else{
				header('location: inscription_client.php?message=Vos mot de passe ne correspondent pas.&type=danger');
				exit;
			}




if( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ){

	header('location: inscription_client.php?message=Email invalide&type=danger');
	exit;
}



// ajout d'un nouvel utilisateur à la table

// Connexion à la base de données


$q = "SELECT idclient  FROM client WHERE email = :email";
// Préparation de la db
$req = $db->prepare($q);
// Execution de la requête
$req->execute([
	'email' => $email
]);
// Récupération de la première ligne de résultats
$resultat = $req->fetch(); // Renvoie un tableau représentant la première ligne de résultats ou un booléen FALSE
// Si existe => erreur, redirection
if($resultat){
	// Redirection vers inscription.php
	header('location: inscription_client.php?message=Cet email est déjà utilisé.&type=danger');
	exit;
}

//$codeparrain = random_int(1000,9999);
$cle = rand(1000000, 9000000);

//création de la carte client si la carte entreprise existe
$ncarteentreprise = htmlspecialchars($_POST['ncarteentreprise']);

$requs = "SELECT ncarte FROM entreprise WHERE ncarte = ?";
$requs = $db->prepare($requs);
$requs->execute([
    $ncarteentreprise
     ]);
 $requs = $requs->fetch();



if (isset($requs[0])) {
  if ($ncarteentreprise==$requs[0]) {
    $ncarte = rand(100000,999999);

    $verifos = "SELECT ncarte FROM client WHERE ncarte = ?";
    $verifos = $db->prepare($verifos);
    $verifos->execute([
        $ncarte
         ]);
     $verifos = $verifos->fetch();

    while ($ncarte == $verifos) {
          $ncarte = rand(100000,999999);

          $verifos = "SELECT ncarte FROM client WHERE ncarte = ?";
          $verifos = $db->prepare($verifos);
          $verifos->execute([
          $ncarte
           ]);
           $verifos = $verifos->fetch();
        }
  }
}else {
  header('location: inscription_client.php?message=cette entreprise n\'existe pas.&type=danger');
  exit;
}





$q = "INSERT INTO client (email, password, nom,prenom,cle,confirme,ncarte,ncarteentreprise) VALUES (:email, :password,:nom,:prenom,:cle , :confirme,:ncarte,:ncarteentreprise)";
$req = $db->prepare($q); // Préparation de la requête
$reponse = $req->execute([
	'email' => $email,
	'password' => hash('sha512', $password),
		'nom' => $nom,
    'prenom' => $prenom,
    'cle' => $cle,
    'confirme' => 0,
    'ncarte' => $ncarte,
    'ncarteentreprise' => $ncarteentreprise
]);

$qaa = "SELECT * FROM client WHERE email = ?";
$reqrr = $db->prepare($qaa);
$reqrr->execute([$email]);
$userinfo = $reqrr->fetch();
$_SESSION['id']=$userinfo['idclient'];
function smtpmailer($to, $from, $from_name, $subject, $body)
    {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;

        $mail->SMTPSecure = 'ssl';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;
        $mail->Username = 'pay2earn.p2e@gmail.com';
        $mail->Password = 'pay2earnoui';

   //   $path = 'reseller.pdf';
   //   $mail->AddAttachment($path);

        $mail->IsHTML(true);
        $mail->From='pay2earn.p2e@gmail.com';
        $mail->FromName=$from_name;
        $mail->Sender=$from;
        $mail->AddReplyTo($from, $from_name);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AddAddress($to);
        if(!$mail->Send())
        {
            $error ="Succés";
            return $error;
        }
        else
        {
            $error = "Erreur";
            return $error;
        }

    }


    $to   = $_POST['email'];
    $from = 'pay2earn.p2e@gmail.com';
    $name = 'Pay2Earn';
    $subj = 'Email de confirmation';
    $msg = '<a href="https://www.pay2earn.store/verif2.php?id='.$_SESSION['id'].'&cle='.$cle.'">Confirmer son compte';
    $error=smtpmailer($to,$from, $name ,$subj, $msg);
 if($reponse){



	header('location: index.php?message=Compte créé avec succès !!&type=success');
	exit;
}else{

	header('location: inscription_client.php?message=Erreur lors de la création du compte.&type=danger');
	exit;
}
