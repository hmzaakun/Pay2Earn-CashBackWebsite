<!DOCTYPE html>
<html lang="fr" dir="ltr">

  <head>
    <?php include('includes/head.php'); ?>
  </head>
  <?php include('includes/header.php');?>

          <body>
<div class="inscrip">


            <form action="verification_inscription_entreprise.php" method="post" enctype="multipart/form-data">
                  <h2>INSCRIPTION ENTREPRISE</h2>
                     <?php
                    if(isset($_GET['message']) && !empty($_GET['message'])){?>
                      <p class="error"><?php
                      echo ($_GET['message']);
                    }
                    ?></p>


              <label for="email" >E-mail :</label>
               <input type="email" name="email" placeholder="Votre email" value="<?= isset($_COOKIE['email']) ? $_COOKIE['email'] : '' ?>" required="required">

                  <label for="password" >Mot de passe : </label>
                  <input type="password" name="password"  pattern=".{5,}"
                                      title="Doit contenir au moins 5 caractères"
                                      placeholder="Mot de passe" required>



                  <label for="password" >Confirmation Mot de passe : </label>
                    <input type="password" name="password2" placeholder="Confirmation Mot de passe" required="required">




                <label for="text" >Nom :</label>
                <input type="text" name="nom" placeholder="Nom de l'entreprise" required="required">
                <label for="text" >Téléphone :</label>
                <input type="text" name="telephone" placeholder="Numero de téléphone" required="required">
                <label for="text" >Parrain :</label>
                <input type="text" name="parrain" placeholder="Parrain">

                  <label for="text" >Captcha :</label>
                  <br>
                  <img src="captcha.php" >
                  <input type="text" name="captcha" required="required" >



                 <button type="submit">S'INSCRIRE</button>
                 <a href="connexion.php" class="ca">Tu as déjà un compte?</a>

             </form>
</div>
<?php include('includes/footer.php');?>
        </body>


</html>
