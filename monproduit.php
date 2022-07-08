<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');


include('includes/db.php');



 if (isset($_GET['id'])) {
$id=$_GET['id'];
$req = $db->prepare('SELECT * FROM article WHERE idarticle=? ');
$req->execute([$id]);
$produit = $req->fetch();
}



        ?>

        <!DOCTYPE html>
        <html>
        <?php include('includes/head.php'); ?>

        <?php include('includes/header.php'); ?>
        <body>





	<div class="publi">
    <div class="publi1">
	    <?php echo  '<img src="publicationimage/'. $produit['image']. '"  width ="150" height="200">'; ?>

	        <h1 class="name"><?=$produit['nom']?></h1>
	        <span class="price"> €<?=$produit['prix']?></span>
          <p>
            <?=$produit['description']?>
          </p>
          <p>
            <?php echo 'quantite :' . $produit['quantite'];?>
          </p>
	         <form action="panier.php" method="post">
	             <input type="number" name="quantite" value="1" min="1" max="<?=$produit['quantite']?>" placeholder="Quantité" required>
	             <input type="hidden" name="produit_id" value="<?=$produit['idarticle']?>">
	             <input type="submit" value="Ajouter au panier">
	         </form>

	     </div></div>

	     <?php include('includes/footer.php');?>
       </body>
	     </html>
