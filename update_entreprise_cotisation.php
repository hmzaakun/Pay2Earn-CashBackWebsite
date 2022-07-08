<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

  include('includes/db.php');
  session_start();

if(isset($_POST['admis']) && !empty($_POST['admis']) && isset($_SESSION['email']) && !empty($_SESSION['email']))
{
    $admisemail = $_POST['admis'];
    $updateadmis = $db->prepare("UPDATE entreprise SET admis = 1 WHERE email = ?");
    $updateadmis->execute([$admisemail]);

}else{
    header('Location:index.php');
}
?>
