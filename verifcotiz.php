<?php
include('includes/db.php');
$updateadmiss = $db->prepare("SELECT identreprise FROM entreprise WHERE  DATEDIFF(NOW(),timeca) >= 365");
$updateadmiss->execute();
$updateadmiss = $updateadmiss->fetchAll();
if($updateadmiss) {
    foreach ($updateadmiss as $upd) {
        $updateadmiss = $db->prepare("UPDATE entreprise SET admis = 0 WHERE identreprise = ?");
        $updateadmiss->execute([$upd['identreprise']]);

    }
}
?>