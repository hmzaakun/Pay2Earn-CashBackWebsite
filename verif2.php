<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
session_start();
include('includes/db.php');
if (!empty($_GET['id']) AND !empty($_GET['cle']) AND isset($_GET['id']) AND isset($_GET['cle']) ){
  $getid = $_GET['id'];
  $getcle = $_GET['cle'];
  $preparevalidation = $db->prepare("SELECT * FROM client WHERE idclient = ? AND cle = ?");
  $preparevalidation->execute(array($getid,$getcle));

  if($preparevalidation->rowCount()>0){
    $Userinfo=$preparevalidation->fetch();
    if ($Userinfo['confirme']!=1){
      $update = $db->prepare("UPDATE client SET confirme = ? WHERE idclient = ?");
      $update->execute(array(1,$getid));
      $_SESSION['id'] = $getcle;
      header('Location:monprofil.php');
    }else{
      $_SESSION['id']= $getcle;
      header('Location:monprofil.php');
    }
    }else{
      echo "Clé incorrect";
    }


  }else{
      header('Location:index.php');
}

?>
<html>
<?php include('includes/head.php'); ?>
<body>
  <?php include('includes/header.php'); ?>
  <?php include('includes/footer.php'); ?>
</body>
</html>
