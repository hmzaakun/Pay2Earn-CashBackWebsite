<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if(isset($_POST['email']) && !empty($_POST['email'])){

	setcookie('email', htmlspecialchars($_POST['email']), time() + 3600 );
}

$ip = $_SERVER['REMOTE_ADDR'];

if( !isset($_POST['email']) || empty($_POST['email']) || !isset($_POST['password']) || empty($_POST['password']) ){

	header('location: connexion_client.php?message=Vous devez remplir les 2 champs&type=danger');
	exit;
}


$log = fopen('log.txt', 'a+');


$line = date("Y/m/d - H:i:s") . ' - Tentative de connexion de : ' . $_POST['email'] . "\n";


fputs($log, $line);


fclose($log);





if( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ){
	// Redirection vers connexion.php
	header('location: connexion_client.php?message=Email invalide&type=danger');
	exit;
}


// Vérifier que l'utilisateur existe en base de données

//Connexion à la base de données.
include('includes/db.php');

$q = "SELECT idclient FROM client WHERE email = :email AND password = :password";
$req = $db->prepare($q);
$req->execute([
	'email' => $_POST['email'],
	'password' => hash('sha512', $_POST['password']) // Même méthode de hachage qu'à la création de l'utilisateur
]);
// UPDATE IP
/*
$qb = "UPDATE users SET ip = :ip WHERE email = :email AND password = :password";
$reqm = $db->prepare($qb);
$reqm->execute([
	'ip' => $ip,
	'email' => $_POST['email'],
	'password' => hash('sha512', $_POST['password']) // Même méthode de hachage qu'à la création de l'utilisateur
]);
*/
$user = $req->fetch(); // Récupérer la première ligne de résultat // false si aucun résultat

if($user){
	// la requête a renvoyé un résultat
	// ouvrir une session utilisateur
	session_start();

	// Remplir la session
	$_SESSION['email_user'] = htmlspecialchars($_POST['email']);

	// Redirection vers la page d'accueil
	header('location: index.php');
	exit;
}else{
	// la requête n'a renvoyé aucun résultat
	// Redirection vers connexion.php
	header('location: connexion_client.php?message=Identifiants invalides&type=danger');
	exit;
}


// Vérifier que les champs ont la bonne valeur





















?>
