<?php
include('includes/db.php');
$updateadmiss = $db->prepare("UPDATE client SET soldejanvier = soldepoint ");
$updateadmiss->execute();
?>