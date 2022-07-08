
<?php

include('includes/db.php');

 if(isset($_SESSION['email'])){
    }else{
    header('location: catalogue.php?');}


 if (isset($_SESSION['email'])){
            $q = "SELECT * FROM entreprise WHERE email = ?";
            $req = $db->prepare($q);
            $req->execute([
            $_SESSION['email']
            ]);
            $usersession = $req->fetch();
        }

$stmt = $db->prepare('SELECT * FROM produits ORDER BY id DESC ');
$stmt->execute();
$produit = $stmt->fetchAll(PDO::FETCH_ASSOC);

$reqas= $db->prepare('SELECT * FROM entreprise ORDER BY id DESC');
$reqas->execute();
$info=$reqas->fetchAll();

	?>
	<!DOCTYPE html>
	<html>
	<head>
		<title></title>

   <?php include('includes/head.php'); ?>


       <link href="pricing.css" rel="stylesheet">

	</head>
	<header>
  </header>
	<body>

	 <div class="album py-5 bg-light">
    <div class="container">

      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

	        <?php foreach ($produit as $prod): ?>
	        <td> <?php echo  '<img src="admins/publicationimage/'. $prod['image']. '"  width ="150" height="200">'; ?><br>
	            <h3><?=$prod['nom']?></h3>

              <br>
	            <span class="price">
	                €<?=$prod['prix']?>

	                	 <form action="catalogue.php?page=panier" method="post">
	             <input type="number" name="quantité" value="1" min="1" max="<?=$produit['quantité']?>" placeholder="Quantité" required>
	             <input type="hidden" name="produit_id" value="<?=$prod['id']?>">            <input type="submit" value="Ajouter au panier">
	         </form>
	                </span>


	            </span>
	        </a></td>
	        <?php endforeach; ?>
	               </tr></table>
	    </div></div>
        <?php include('includes/footer.php');?>
	</body>
	</html>
