<?php
include('includes/db.php');
$updateadmiss = $db->prepare("UPDATE client SET soldepoint = IF((soldepoint - soldejanvier) >= 0 , soldepoint - soldejanvier, soldepoint) ");
$updateadmiss->execute();
?>