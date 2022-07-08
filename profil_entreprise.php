<!DOCTYPE html>
<?php
error_reporting(-1);
      ini_set('display_errors', 'On');
include('includes/db.php');


    ?>


<html>

        <?php include('includes/head.php'); ?>
        <head>


        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">

        function getCookie(cookieName) {
            let cookie = {};
            document.cookie.split(';').forEach(function(el) {
                let [key,value] = el.split('=');
                cookie[key.trim()] = value;
            })
            return cookie[cookieName];
        }

          google.charts.load('current', {'packages':['corechart']});
          google.charts.setOnLoadCallback(drawChart);

          function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['date', 'ventes'],
              ['', 0],
              ['Avant\-hier',  getCookie("ahier")],
              ['Hier',  getCookie("hier")],
              ['Aujourd\'hui',  getCookie("today")]
            ]);

            var options = {
              title: 'Ventes sur les derniers jours',
              curveType: 'function',
              legend: { position: 'bottom' }
            };

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

            chart.draw(data, options);
          }
        </script>
        </head>


    <body>
    <?php include('includes/header.php'); ?>

        <main>
          <?php
          if (isset($_SESSION['email'])){
                $q = "SELECT * FROM entreprise WHERE email = ?";
                $req = $db->prepare($q);
                $req->execute([
                    $_SESSION['email']
                ]);
              }
                $user = $req->fetch();


          $today = "SELECT COUNT(*) FROM `article-achat` AS a INNER JOIN article AS b WHERE a.datetimeuh>NOW() - INTERVAL 1 DAY AND a.datetimeuh<NOW() - INTERVAL 0 DAY AND b.identreprise=?";
          $today = $db->prepare($today);
          $today->execute([
          $user['identreprise']
           ]);
           $today = $today->fetch();
           setcookie('today', $today[0], time() + 3600 );


           $hier = "SELECT COUNT(*) FROM `article-achat` AS a INNER JOIN article AS b WHERE a.datetimeuh>NOW() - INTERVAL 2 DAY AND a.datetimeuh<NOW() - INTERVAL 1 DAY AND b.identreprise=?";
           $hier = $db->prepare($hier);
           $hier->execute([
           $user['identreprise']
            ]);
            $hier = $hier->fetch();
            setcookie('hier', $hier[0], time() + 3600 );

            $ahier = "SELECT COUNT(*) FROM `article-achat` AS a INNER JOIN article AS b WHERE a.datetimeuh>NOW() - INTERVAL 3 DAY AND a.datetimeuh<NOW() - INTERVAL 2 DAY AND b.identreprise=?";
            $ahier = $db->prepare($ahier);
            $ahier->execute([
            $user['identreprise']
             ]);
             $ahier = $ahier->fetch();
             setcookie('ahier', $ahier[0], time() + 3600 );

             $avant = "SELECT COUNT(*) FROM `article-achat` AS a INNER JOIN article AS b WHERE a.datetimeuh>NOW() - INTERVAL 4 DAY AND a.datetimeuh<NOW() - INTERVAL 3 DAY AND b.identreprise=?";
             $avant = $db->prepare($avant);
             $avant->execute([
             $user['identreprise']
              ]);
              $avant = $avant->fetch();
              setcookie('avant', $avant[0], time() + 3600 );
           ?>

                  <?php if($user) { ?>
                     <div class="media profil">
                         <div class="media-body">
                           <h5 class="mt-0">Ma carte entreprise :</h5>

                           <p>
                             <?php echo ' Nom : ' . $user['nom'] . ''; ?>
                           </p>
                           <p>
                             <?php echo ' Mail : ' . $user['email'] . ''; ?>
                           </p>
                           <p>
                             <?php echo ' Numéro de téléphone : ' . $user['telephone'] . ''; ?>
                           </p>
                           <p>
                             <?php echo ' Chiffre d\'affaire :' . $user['ca'] . ''; ?>€
                           </p>

                           <p>
                             <?php echo ' Votre code de parrainage :' . $user['codeparrain'] . ''; ?>
                           </p>

                       <?php
                       if ($user['admis']== 1) {
                         echo '<p>'.' Numero de loyalty card : ' . $user['ncarte'] . '</p> ';

                        ?>



                          <?php
                          if(isset($user['ncarte'])) {
                             $string = trim($user['ncarte']);
                             if($string != '') {
                              echo "<img alt='testing' src='barcode.php?codetype=code39&size=100&text=".$string."&print=true'/><br>";
                              echo '<a href="pdf_entreprise.php?ncarte=' . $string . '"> Acceder à ma carte entreprise en format PDF</a>';
                             }
                          }else {
                            echo "<p>Vous n'avez pas de loyalty card !</p>";
                          }


                      }else {
                        echo '<a href="test.php">payez votre cotisation pour avoir acces à votre carte</a>';
                      }






                           $toutarticle = "SELECT * FROM article WHERE identreprise = ?";
                           $reqos = $db->prepare($toutarticle);
                           $reqos->execute([
                           $user['identreprise']
                            ]);
                            $allarticle = $reqos->fetchAll();





                           if (isset($_GET['supprimer']) AND !empty($_GET['supprimer'])) {
                             $verifos = "SELECT identreprise FROM article WHERE identreprise = ?";
                             $verifos = $db->prepare($verifos);
                             $verifos->execute([
                             $user['identreprise']
                              ]);
                              $verifos = $verifos->fetch();

                              if ($user['identreprise']==$verifos[0]) {


                               $postsupprime=$_GET['supprimer'];
                               $supprime = "DELETE FROM article WHERE idarticle = ? AND identreprise = ? ";
                               $requete = $db->prepare($supprime);
                               $requete->execute([
                                 $postsupprime,
                                 $user['identreprise']
                               ]);
                               header("Location:profil_entreprise.php");
                             }
                           }

                       ?>

                         </div>
                     </div>


                     <div class="profil2">
                       <h2 style="color:white">Mes offres :</h2>
                       <div class="" style="width : 100%;">

                       </div>

                             <?php
                             foreach ($allarticle as $p) {?>
                               <div class="profil3">
                               <?php echo '<img src="publicationimage/' . $p['image']. ' "  width="200" height="200" alt="publication">' ; ?>
                             <p>
                                <?php echo $p['description']; ?>
                             </p>
                             <p>
                                <?php echo $p['prix'] . '€'; ?>
                             </p>
                             <p>
                                <?php echo   strftime("%A %d %B %G %H:%M", strtotime($p['dateajout']));  ?>
                              <p>
                                  <a href="monproduit.php?id=<?=$p['idarticle']; ?>">Plus d'infos</a>
                              </p>
                             </p>
                             <a href="profil_entreprise.php?supprimer=<?php echo $p['idarticle'];?>">Supprimer</a>
                             </div>
                             <?php }


                             $requs = $db->prepare("SELECT * FROM article AS a INNER JOIN `article-achat` AS b WHERE a.identreprise=? ORDER BY b.datetimeuh DESC");
                             $requs->execute([
                               $user['identreprise']
                             ]);
                             $article = $requs->fetchAll();
                             ?>


                             <h2 style="color:white">Mes dernières ventes :</h2>
                             <div class="" style="width : 100%;"></div>
                             <div class="adminedit3" style="overflow-y:scroll; height:400px;">

                                   <table class="table">
                             <thead>
                               <tr>
                                 <th scope="col">id achat</th>
                                 <th scope="col">nom</th>
                                 <th scope="col">prix</th>
                                 <th scope="col">quantite</th>
                                 <th scope="col">date de vente</th>
                               </tr>
                             </thead>
                               <tbody>
                             <?php foreach ($article as $a){
                               ?>
                           <tr>
                             <td><?php echo $a['idachat']; ?> </td>
                             <td><?php echo $a['nom']; ?> </td>
                             <td><?php echo $a['prix']; ?> </td>
                             <td><?php echo $a['quantite']; ?> </td>
                             <td><?php echo $a['datetimeuh']; ?> </td>
                           </tr>
                         <?php }?>
                           </tbody>
                               </table>
                           </div>

                           <h2 style="color:white">Ces 3 derniers jours :</h2>
                           <div class="" style="width : 100%;"></div>
                           <div id="curve_chart" style="width: 900px; height: 500px"></div>
                     </div>
                     <?php } else {
                         echo '<center><h2>Utilisateur introuvable !</h2></center>';
                     }?>


        </main>

             <?php include('includes/footer.php');



              ?>

    </body>

</html>
