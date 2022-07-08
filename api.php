<?php
include('includes/db.php');
$q = "SELECT nom, prenom, email, ncarte, status FROM client WHERE ncarte = ?";
$q = $db->prepare($q);
$q->execute([
    $_GET['ncarte']
]);
$q = $q->fetch(PDO::FETCH_ASSOC);
 if ($q != false) {
  echo (json_encode($q));
}

else {
  echo "{\"status\":\"1\"}";
}

 ?>
