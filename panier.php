<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


session_start();
include('includes/db.php');

if(isset($_SESSION['email_user'])){
    $q = "SELECT * FROM client WHERE email = ?";
    $req = $db->prepare($q);
    $req->execute([
        $_SESSION['email_user']
    ]);
    $user = $req->fetch();

}else{
    header("location: index.php");
}

    /* SELECT P.n_point,P.dateemi FROM `point` AS P
INNER JOIN achat AS A
ON A.idachat = P.idachat
WHERE A.idclient  = 9
    */


//fonction zebitox
function sendMessage($message,$email){
    $content = array(
        "en" => $message
    );

    $fields = array(
        'app_id' => "3e374b55-473b-404a-adc5-f630bc9bf216",
        'include_external_user_ids' => array($email),
        'channel_for_external_user_ids' => 'push',
        'data' => array("foo" => "bar"),
        'contents' => $content
    );

    $fields = json_encode($fields);
    print("\nJSON sent:\n");
    print($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
        'Authorization: Basic NWYxM2M1ZTUtMzQ0ZS00Njk0LWFkNDYtNjFlOGE4NTA0NmQz'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function pointixou($pointix,$db){
    global $db;
    $q = "SELECT * FROM client WHERE email = ?";
    $req = $db->prepare($q);
    $req->execute([
        $_SESSION['email_user']
    ]);
    $user = $req->fetch();

    $zz = "SELECT P.n_point,P.dateemi,P.idpoint FROM `point` AS P INNER JOIN achat AS A ON A.idachat = P.idachat WHERE A.idclient  = ? ";
    $reqmz = $db->prepare($zz);
    $reqmz->execute([
        $user['idclient']
    ]);
    $listpoint = $reqmz->fetchAll(PDO::FETCH_ASSOC);
    $dette = 1;
// 100 point

    for($b=0;$b<count($listpoint);$b++){
        if ($pointix <= $listpoint[$b]["n_point"] && $dette == 1) {
            $q = "UPDATE point SET n_point = n_point - ? WHERE idpoint = ?";
            $req = $db->prepare($q); // Préparation de la requête
            $reponse = $req->execute([$listpoint[$b]["n_point"], $listpoint[$b]["idpoint"]]);
            $dette = 0;
        }

        if( $pointix > $listpoint[$b]['n_point'] && $dette == 1){
            $q = "UPDATE point SET n_point = 0 WHERE idpoint = ?";
            $req = $db->prepare($q); // Préparation de la requête
            $reponse = $req->execute([ $listpoint[$b]["idpoint"] ]);
            $pointix = $pointix-$listpoint[$b]["n_point"];

        }



    }




}


// SELECT P.n_point,P.dateemi FROM `point` AS P INNER JOIN achat AS A ON A.idachat = P.idachat WHERE A.idclient  = ?

$a = "SELECT n_point,dateemi,idpoint FROM  point P INNER JOIN  achat A ON A.idachat = P.idachat WHERE A.idclient = ? ";
$ad = $db->prepare($a);
$ad->execute([$user['idclient']]);
$totalpoint = $ad->fetchAll(PDO::FETCH_ASSOC);
$totalpointfinal  = 0;
for($b=0;$b<count($totalpoint);$b++){
$totalpointfinal=$totalpointfinal + $totalpoint[$b]['n_point'];
}





$m = "SELECT pourcentage FROM `code_promo` WHERE ? = idcarte_entreprise";
$reqm = $db->prepare($m);
$reqm->execute([
    $user['ncarteentreprise']
]);

$codepromo = $reqm->fetch();
$reduc=0;
if($codepromo[0]!=0){
    $remise = (100-$codepromo[0])/100;
}else{
    $remise = 1;
}





if (isset($_POST['produit_id'], $_POST['quantite']) && is_numeric($_POST['produit_id']) && is_numeric($_POST['quantite'])) {

    $produit_id = $_POST['produit_id'];
    $quantite = $_POST['quantite'];

    $stmt = $db->prepare('SELECT * FROM article WHERE idarticle = ?');
    $stmt->execute([$_POST['produit_id']]);

    $produit = $stmt->fetch();

    if ($produit && $quantite > 0) {

        if (isset($_SESSION['panier']) && is_array($_SESSION['panier'])) {
            if (array_key_exists($produit_id, $_SESSION['panier'])) {

                $_SESSION['panier'][$produit_id] += $quantite;
            } else {

                $_SESSION['panier'][$produit_id] = $quantite;

            }
        }else {

                $_SESSION['panier'] = array($produit_id => $quantite);
            }
        }

        header('location: panier.php');
        exit;
    }



if (isset($_GET['remove']) && is_numeric($_GET['remove']) && isset($_SESSION['panier']) && isset($_SESSION['panier'][$_GET['remove']])) {

    unset($_SESSION['panier'][$_GET['remove']]);
}


if (isset($_POST['update']) && isset($_SESSION['panier']) || isset($_GET['o'])){

    foreach ($_POST as $k => $v) {
        if (strpos($k, 'quantite') !== false && is_numeric($v)) {
            $idarticle = str_replace('quantite-', '', $k);
            $quantite = (int)$v;

            if (is_numeric($idarticle) && isset($_SESSION['panier'][$idarticle]) && $quantite > 0) {

                $_SESSION['panier'][$idarticle] = $quantite;
            }
        }
    }

   header('location: panier.php');
    exit;
}


$produits_in_panier = isset($_SESSION['panier']) ? $_SESSION['panier'] : array();
$produits = array();
$total = 0.00;

if ($produits_in_panier) {

    $array_to_question_marks = implode(',', array_fill(0, count($produits_in_panier), '?'));
    $stmt = $db->prepare('SELECT * FROM article WHERE idarticle IN (' . $array_to_question_marks . ')');

    $stmt->execute(array_keys($produits_in_panier));

    $produits = $stmt->fetchAll();

    foreach ($produits as $produit) {
        $total += (float)$produit['prix'] * (int)$produits_in_panier[$produit['idarticle']];

    }



}







?>
<!DOCTYPE html>
<html>


<?php include('includes/head.php'); ?>

<script>
    function getCookie(cookieName) {
        let cookie = {};
        document.cookie.split(';').forEach(function(el) {
            let [key,value] = el.split('=');
            cookie[key.trim()] = value;
        })
        return cookie[cookieName];
    }

    let externalUserId = decodeURIComponent(getCookie("email"));

    OneSignal.push(function() {
        OneSignal.setExternalUserId(externalUserId);
    });

    OneSignal.push(function() {
        OneSignal.getExternalUserId().then(function(externalUserId){
            console.log("externalUserId: ", externalUserId);
        });
    });
</script>

<body style="display: block;width: 100%;" >

  <?php include('includes/header.php'); ?>
<main>


    <div class="inscrip content-wrapper">
<?php if(isset($_POST['pointix']) && $_POST['pointix'] <= $totalpointfinal ){
    $message = "Vous pouvez pas mettre plus que".$totalpointfinal;
    $pointix = $_POST['pointix']<0?0:$_POST['pointix'];

    setcookie( "pointix", $pointix, time()+3600 );

    }else{
        $pointix = 0;
        }

?>

echo
        <form action="panier.php" method="post">

            <?php echo "Vous avez ".$totalpointfinal." point(s) <br>";?>
            <label for="pointix" >point :</label>
            <input type="number" name="pointix" id="pointix"  min="0"  max = "<?=isset($total) ? $total/0.2-1 : $totalpointfinal?>" max="<?=$totalpointfinal?>" step ="1" placeholder="pointi" value="<?= isset($_POST['pointix']) ? $_POST['pointix'] : 0 ?>" required="required">
            <input type="submit" class="btn btn-primary">

            <h1>Panier d'achat</h1>
            <table class="table">


                <tr>
                    <th colspan="2$">id</th>
                    <th colspan="2$">produit</th>
                    <th>prix</th>
                    <th>quantité</th>
                    <th>Total</th>
                    <th></th>
                </tr>


                <?php


                $total=$total * $remise;
               /* if($total<0){
                    $total = 1;
                    $message = "Pour des raisons économique la facture est ajusté à 1€";
                }
               */

if(isset($_POST['success'])) {

    $pointix1 = $_COOKIE['pointix'];
    pointixou($pointix1,$db);

    $totalui=$total-$pointix1*0.2;
    $q = "INSERT INTO achat SET prix = ?, idclient = ? , code_promo= ?";
    $req = $db->prepare($q); // Préparation de la requête
    $reponse = $req->execute([$totalui, $user['idclient'],$codepromo[0] ]); //$codepromo[0]

    $qb= "SELECT idachat FROM achat WHERE idclient = ? ORDER BY idachat DESC";
    $reqb = $db->prepare($qb);
    $reqb->execute([
    $user['idclient']
    ]);
    $idachat = $reqb->fetch();


    // idpoint	n_point	idachat	dateemi
    //fonction point calc
    function Arrondir($oui){
    if ($oui-(int)$oui>=0.5){
    return (int)$oui+1;
    }
    return (int)$oui;
    }

    function CalculPoint($la)
    {
    $bonus = 0;
    if ($la > 100) {
    $bonus = $la / 100;
    }
    $la = $la * 0.3 + $bonus;
    $la = Arrondir($la);
    return $la;
    }

    $pointi=CalculPoint($totalui);
    //  INSERT INTO entreprise (email, password, nom,cle,confirme,telephone,codeparrain,ncarte) VALUES (:email, :password,:nom,:cle , :confirme,:telephone,:codeparrain,:ncarte)
    // INSERT INTO `article-achat` SET idachat = $idachat[0] , idarticle = ? , quantite = ?
    $q = "INSERT INTO point SET n_point = ? ,  idachat = ?";
    $req = $db->prepare($q); // Préparation de la requête
    $reponse = $req->execute([$pointi,$idachat[0] ]);
    foreach ($produits as $produit){
    $q = "INSERT INTO `article-achat` SET idachat = $idachat[0] , idarticle = ? , quantite = ?";
    $req = $db->prepare($q); // Préparation de la requête
    $reponse = $req->execute([ $produit['idarticle'],$produits_in_panier[$produit['idarticle']] ]);

    $q = "UPDATE article SET quantite = quantite-? where idarticle = ?";
    $req = $db->prepare($q); // Préparation de la requête
    $reponse = $req->execute([ $produits_in_panier[$produit['idarticle']],$produit['idarticle']]);

    }
    $az = "SELECT n_point,dateemi,idpoint FROM  point P INNER JOIN  achat A ON A.idachat = P.idachat WHERE A.idclient = ? ";
    $adz = $db->prepare($az);
    $adz->execute([$user['idclient']]);
    $totalpoint1 = $adz->fetchAll(PDO::FETCH_ASSOC);
    $totalpointfinal1  = 0;
    for($b=0;$b<count($totalpoint);$b++){
        $totalpointfinal1=$totalpointfinal1 + $totalpoint1[$b]['n_point'];
    }
    $tolis=totalpointfinal1+$pointi;
    $response = sendMessage("Merci pour votre achat vous avez gagné".$pointi."Vous avez maintenant ".$tolis."points","rootos@boutos.fr");
    $return["allresponses"] = $response;
    $return = json_encode( $return);

    print("\n\nJSON received:\n");
    print($return);
    print("\n");




    setcookie( "pointix", $pointix, time()-10000000 );
    unset($_SESSION['panier']);

    header('location: profil_client.php');
    exit;
}

                if (empty($produits)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">Vous n'avez aucun produit ajouté dans votre panier
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($produits as $produit):
// fonction calcul prix
                        ?>
                        <tr>
                            <td class="img">

                            </td>

                            <td id="idarticle" class="prix"><?=$produit['idarticle']?> </td>
                            <td><a href="catalogue.php?id=<?= $produit['idarticle'] ?>"><?= $produit['nom'] ?>
                                    <?php echo '<img src="publicationimage/' . $produit['image'] . '"  width ="50" height="50">'; ?>
                                </a>
                                <br>

                            <td id="prout" class="prix">€<?= $produit['prix'] ?></td>
                            <td class="quantite">
                                <input id="quantite" class="qoq" type="number" name="quantite-<?= $produit['idarticle'] ?>"
                                       value="<?= $produits_in_panier[$produit['idarticle']] ?>" min="1"
                                       max="<?= $produit['quantite'] ?>" placeholder="quantite" required>
                            </td>
                            <td class="prix">€<?= $produit['prix'] * $produits_in_panier[$produit['idarticle']] ?></td>
                            <td><a href="panier.php?remove=<?= $produit['idarticle'] ?>" class="remove">Supprimer </a>
                            </td>



                            <?php


                            ?>
                        </tr>

                    <?php endforeach;


                    ?>

                <?php

                endif;

                ?>

            </table>


            <div class="subal">
            <?php
            if($codepromo[0]){
            echo 'Grace a votre carte vous avez '.$codepromo[0].'% de reduction sur toutes la boutique<br>';
            }



            ?>
            </div>

            <span class="text">Total</span>
            <option id="prixtotal" class="prix" value="<?= ($total-$pointix*0.2)?>"><?= ($total-$pointix*0.2)?>€</option>

            <div class="buttons">
            <input type="submit" class="btn btn-primary"  value="Mettre à jour" name="update">
            <br>
            </div>


            </form>
            <form action="catalogue.php" >
            <button type="submit" class="btn btn-primary">Catalogue</button>
            </form>
            <?php

            ?>






    </div>
    <div class="inscrip content-wrapper">
        <?php

        if ($total!=0 ){

            include('checkout1.php');
        } ?>
        </div>

</main>
<?php include('includes/footer.php'); ?>
</body>
</html>
