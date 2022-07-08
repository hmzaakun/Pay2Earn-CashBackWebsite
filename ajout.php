<!DOCTYPE html>
<?php
error_reporting(-1);
      ini_set('display_errors', 'On');

include('includes/db.php');
    ?>


<html>

        <?php include('includes/head.php'); ?>


    <body>
    <?php include('includes/header.php'); ?>
    <main>
<div class="inscrip">


      <form method="post" enctype="multipart/form-data">
        <h3>Nouveau produit :</h3>
      <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Nom du produit</label>
        <input type="name" class="form-control" name="nom" required >
      </div>
      <label for="text" >Image :</label>
                    <br><br>
                    <input type="file" name="image" accept="image/jpeg,image/gif,image/png,image/jpg">
      <div class="mb-3">
        <label for="exampleInputPassword1" class="form-label">Prix</label>
        <input type="number" class="form-control" name="prix" required>
      </div>
      <div class="mb-3">
        <label for="exampleInputPassword1" class="form-label">Quantité</label>
        <input type="number" class="form-control" name="quantite" required>
      </div>
      <div class="mb-3">
        <label for="exampleInputPassword1" class="form-label">Description</label>
        <textarea class="form-control" name="description" required></textarea>

      </div>


      <button type="submit" name="valider"  class="btn btn-primary">Ajouter nouveau produit</button>
      
        <br>
        <a href="../index.php" class="ca">Acceuil</a>



    <?php


      if(isset($_POST['nom']) AND isset($_FILES['image']) AND isset($_POST['prix']) AND isset($_POST['description']) AND isset($_POST['quantite']))
    {
      $acceptable = [
           'image/jpeg',
           'image/png',
           'image/gif'
         ];


         if(!in_array($_FILES['image']['type'], $acceptable)){
           // Redirection vers poster.php
           header('location: ajout.php?message=Format de fichier incorrect.&type=danger');
           exit;
         }

         // Vérifier le poids du fichier

         $maxSize = 10 * 1024 * 1024; // 2Mo

         if($_FILES['image']['size'] > $maxSize){
           // Redirection vers poster.php
           header('location: ajout.php?message=Ce fichier est trop lourd !&type=danger');
           exit;
         }

         $path = 'publicationimage';
         if(!file_exists($path)){
           mkdir($path, 0777);
         }

         $filename = $_FILES['image']['name'];

         $array = explode('.', $filename); // convertir en tableau découper par les points
         $ext = end($array);
         $filename = 'image-' . time() . '.' . $ext;

         $destination = $path . '/' . $filename;
         $tempName = $_FILES['image']['tmp_name'];
         move_uploaded_file($tempName, $destination);

    $nom = htmlspecialchars($_POST['nom']);
    $prix = htmlspecialchars($_POST['prix']);
    $description = htmlspecialchars($_POST['description']);
    $quantite = htmlspecialchars($_POST['quantite']);

    $q = "SELECT * FROM entreprise WHERE email = ?";
    $req = $db->prepare($q);
    $req->execute([
        $_SESSION['email']
    ]);

    $user = $req->fetch();

    $identreprise = $user['identreprise'];
    $identrepot = 0;


    $req = $db->prepare('INSERT INTO article (image, nom , prix , description, quantite,identreprise,identrepot) VALUES (?,?,?,?,?,?,?)');
    $req->execute(array($filename,$nom,$prix,$description,$quantite,$identreprise,$identrepot));

    echo '<h3>l\'article est ajouté !</h3>';

    }else{


    }  ?>

    </form>

</div>
</main>
  <?php include('includes/footer.php'); ?>
</body>
</html>
