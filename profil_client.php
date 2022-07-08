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
          <?php if (isset($_SESSION['email_user'])){
                $q = "SELECT * FROM client WHERE email = ?";
                $req = $db->prepare($q);
                $req->execute([
                    $_SESSION['email_user']
                ]);
              $user = $req->fetch();
              }else{
              header("location: index.php");
          }
          $a = "SELECT n_point,dateemi,idpoint FROM  point P INNER JOIN  achat A ON A.idachat = P.idachat WHERE A.idclient = ? ";
          $ad = $db->prepare($a);
          $ad->execute([$user['idclient']]);
          $totalpoint = $ad->fetchAll(PDO::FETCH_ASSOC);
          $totalpointfinal  = 0.00;
          for($b=0;$b<count($totalpoint);$b++){
              $totalpointfinal=$totalpointfinal + $totalpoint[$b]['n_point'];
          }






          ?>

                  <?php if($user) { ?>
                     <div class="media profil">
                         <div class="media-body">
                           <h5 class="mt-0">Ma carte :</h5>

                           <p>
                             <?php echo ' Nom : ' . $user['nom'] . ' || '; ?>
                             <?php echo ' Prenom : ' . $user['prenom'] . ''; ?>
                           </p>
                           <p>
                             <?php echo ' Mail : ' . $user['email'] . ''; ?>
                           </p>
                           <p>
                             <?php echo ' mes points : ' . $totalpointfinal . ''; ?>
                           </p>
                           <p>
                             <?php echo ' valeur : ' . ($totalpointfinal*0.2) . '€'; ?>
                           </p>
                           <p>
                             <?php echo ' numero de loyalty card : ' . $user['ncarte'] . '</p> '; ?>
                           </p>


                           <?php
                   						if(isset($user['ncarte'])) {
                   						   $string = trim($user['ncarte']);
                   						   if($string != '') {
                   							  echo "<img alt='testing' src='barcode.php?codetype=code39&size=100&text=".$string."&print=true'/><br>";
                                  echo '<a href="pdf_client.php?ncarte=' . $string . '"> Acceder à ma carte client en format PDF</a>';
                   						   } else {
                   							   echo "Vous n'avez pas de loyalty card !";
                   						   }
                   						}
                   					?>





                         </div>
                     </div>

                     <?php
                     $q = "SELECT * FROM achat WHERE idclient = ? ORDER BY date DESC";
                     $q = $db->prepare($q);
                     $q->execute([
                           $user['idclient']
                     ]);

                     $q = $q->fetchAll(); ?>

                     <div class="media profil">
                       <h2>Mes achats :   </h2>
                          <div class="inscrip">

                            <table class="table">
                                  <thead>
                                    <tr>
                                      <th scope="col">n° achat</th>
                                      <th scope="col">date</th>
                                      <th scope="col">prix</th>
                                      <th scope="col">facture</th>
                                    </tr>
                                  </thead>
                                  <tbody>

                                    <?php foreach ($q as $u){
                                      ?>
                                    <tr>
                                    <td><?php echo $u['idachat']; ?> </td>
                                    <td> <?php echo $u['date']; ?> </td>
                                    <td><?php echo $u['prix']; ?>€ </td>
                                    <td><a href="facture.php?idachat=<?php echo $u['idachat']; ?>">Facture</a></td>
                                  </tr>
                                <?php }?>
                                  </tbody>
                                </table>

                          </div>
                     </div>


                     <?php } else {
                         echo '<center><h2>Utilisateur introuvable !</h2></center>';
                     }?>


        </main>

             <?php include('includes/footer.php'); ?>

    </body>
</html>
