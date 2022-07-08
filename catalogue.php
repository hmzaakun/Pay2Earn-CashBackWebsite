<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');


include('includes/db.php');

 if (isset($_SESSION['email'])){
            $q = "SELECT * FROM entreprise WHERE email = ?";
            $req = $db->prepare($q);
            $req->execute([
            $_SESSION['email']
            ]);
            $usersession = $req->fetch();
        }

$stmt = $db->prepare('SELECT * FROM article  WHERE quantite > 1 ORDER BY idarticle DESC ');
$stmt->execute();
$produit = $stmt->fetchAll();

$reqas= $db->prepare('SELECT * FROM entreprise ORDER BY identreprise DESC');
$reqas->execute();
$info=$reqas->fetchAll();



	?>
	<!DOCTYPE html>
	<html>

  <?php include('includes/head.php'); ?>





	<body>
    <?php include('includes/header.php'); ?>
    <main>


      <div class="decouvrir">
        <div style="width:100%;"><center><h3>Recherche :</h3></center></div>

    <input type="text" onkeyup="imu(this.value)" placeholder="recherche article">
                  <div class="recherche1" id="content" ></div>

                  <script type="text/javascript">
                    let content =  document.getElementById('content');

                    function imu(x){
                      if (x.length == 0){
                        content.innerHTML = 'Aucun article..'
                      }else{
                        var XML = new XMLHttpRequest();
                        XML.onreadystatechange = function(){
                          if (XML.readyState == 4 && XML.status == 200){
                            content.innerHTML = XML.responseText;
                          }
                        };
                        XML.open('GET','recherche_article.php?article='+x,true);
                        XML.send();
                      }
                    }
                  </script>
                </div>
                  <div class="decouvrir">

	    <?php foreach ($produit as $prod): ?>
        <div class="decouvrir1">
        <a href="monproduit.php?id=<?=$prod['idarticle']?>" class="produit"><td> <?php echo  '<img src="publicationimage/'. $prod['image']. '"  width ="150" height="200">'; ?><br>
             <?=$prod['nom']?>  </a>
              <br>
	            <span class="price">
	                €<?=$prod['prix']?>

	                
	                </span>


	            </span>
	        </a></td>
          </div>
	        <?php endforeach; ?>

	    </div>

    </main>
        <?php include('includes/footer.php');?>
	</body>
	</html>
