< !DOCTYPE html >
<html lang="fr" dir="ltr">

<head>
    <?php include('includes/head.php'); ?>
</head>
<?php include('includes/header.php');?>

<body>
<div class="inscrip">


    <form action="verification_inscription_client.php" method="post" enctype="multipart/form-data">
        <h2>INSCRIPTION CLIENT</h2>
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
        <input type="text" name="nom" placeholder="Nom de famille" required="required">
        <label for="text" >Prenom :</label>
        <input type="text" name="prenom" placeholder="Prenom" required="required">
        <label for="text" >Numero de carte de l'entreprise:</label>
        <input type="text" name="ncarteentreprise" placeholder="numero de carte" required="required">


        <label for="text" >Captcha :</label>
        <br>
        <img src="captcha.php" >
        <input type="text" name="captcha" required="required" >



        <button type="submit">S'INSCRIRE</button>
        <a href="connexion_client.php" class="ca">Tu as déjà un compte?</a>

    </form>
</div>
<?php include('includes/footer.php');?>
</body>


</html>
