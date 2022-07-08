<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">

  <head>
    <?php include('includes/head.php'); ?>
  </head>
  <?php include('includes/header.php');
  include('includes/db.php');
  if (isset($_SESSION['email']))
   {
    $email = $_SESSION['email'];
  }else{
    header('Location:index.php');

  }
  $admisq = $db->prepare("SELECT admis FROM entreprise WHERE email = ?");
  $admisq->execute([$email]);
  $admisq = $admisq->fetch();

  $cacheck = $db->prepare("SELECT ca FROM entreprise WHERE email = ?");
  $cacheck->execute([$email]);
  $cacheck = $cacheck->fetch();
  function calculateCotisation(array $cacheck){
      include('includes/db.php');
    $ca=$cacheck[0];
    /*
    Moins de 200 000 €  Gratuit
  Entre 200 000 € et 800 000 € 0,8% du chiffre d’affaires annuel
  Entre 800000 € et 1500000 € 0,6 % du chiffre d’affaires annuel
  Entre 1500000 € et 3000 000 € 0,4 % du chiffre d’affaires annuel
  Au-delà de 3000000 € 0,3 % du chiffre d’affaires annuel
  */
  if($ca<200000){
    $cotisation = 0;
    $updateadmiss = $db->prepare("UPDATE entreprise SET admis = 1 WHERE email = ?");
    $updateadmiss->execute([$_SESSION['email']]);

  }
  if($ca>=200000 && $ca<800000 ){
    $cotisation = $ca*0.008;
  }
  if($ca>=800000 && $ca<1500000 ){
    $cotisation = $ca*0.006;
  }
  if($ca>=1500000 && $ca<3000000 ){
    $cotisation = $ca*0.004;
  }
  if($ca>=3000000){
    $cotisation = $ca*0.003;
  }


  //convertion  *100

      return $cotisation;
  }



  ?>

          <body>
            <?php

            ?>
            <div class="inscrip">
                <form method="POST" action="" enctype="multipart/form-data">
                  <h2>Chiffre d'affaire</h2><br>
                  <label>Chiffre d'affaires <?php echo date("Y")-1;?> :</label>
                <input type="text" name="ca" <?php echo $cacheck[0]==0?:"disabled=\"disabled\"" ; ?> placeholder="<?php echo $cacheck[0]; ?>€" value="" />
                <?php if ($cacheck[0]==0){ ?>
                <input type="submit" value="Mettre à jour le CA">
              <?php }else{
                ?> <?php echo $admisq[0]==0?calculateCotisation($cacheck).'€':''; ?>  <?php

              } ?>


              </form>
              <div class="wsh">
                                <?php
                              if($cacheck[0]!=0 && $admisq[0]==0 && calculateCotisation($cacheck)!=0){
                                   include ('checkout.php');
                                 }

                                  ?>
              </div>
            </div>

<?php

if(isset($_POST['ca']) && !empty($_POST['ca']) ){
  //
  $ca=$_POST['ca'];

  if ($cacheck[0]==0){
    $cacheck = $db->prepare("UPDATE entreprise SET ca = ? WHERE email = ?");
    $cacheck->execute([$ca,$email]);
header("Refresh:0");
  }else{
    echo "Vous avez déja remplis le chiffre d'affaire";
  }

}

?>

          </body>
