<?php
session_start();


include("includes/db.php");


if(isset($_GET['article'])){
  $article= $_GET['article'];

}




$recherche="SELECT * FROM article WHERE nom LIKE  '%$article%'";
$req = $db->prepare($recherche);
$req->execute();
$articles= $req->fetchAll();


foreach($articles as $art){

  echo '<img src=" publicationimage/' . $art['image'] . ' " id=avatar width="50" height="50" alt="Profil">' ;
 echo '<a href="monproduit.php?id=' .$art['idarticle'].' "> '. $art['nom'] .' </a>';





}
