<!DOCTYPE html>
<html lang="fr" dir="ltr">

  <head>

    <?php include('includes/head.php'); ?>
    <script type="module" src="/coinflip.js"></script>

  </head>

  <body>
    <?php
    ini_set("display_errors", 1);

 include('includes/header.php');?>
    <main>
    <link href="pricing.css" rel="stylesheet">
  </head>
  <body>

<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
  <h1 class="display-4">Pay to Earn</h1>
  <p class="lead">Pay to earn est un site basé sur le principe des loyalty card qui permet à l'acheteur de faire des économies sur ses achats.<br>
     Chez nous, plus vous achetez et plus vous êtes récompensées.<br>
     Avec une multitude d'offres, de produits et d'entreprises partenaires, vous serez obligés de trouver votre bonheur. </p>
</div>
<?php
if (isset($_SESSION["email"]) || isset($_SESSION["email_user"] )) {
}else{

?>
<div class="container">
  <div class="card-deck mb-3 text-center">

    <div class="card mb-4 shadow-sm">
      <div class="card-header">
        <h4 class="my-0 font-weight-normal">Client ?</h4>
      </div>
      <div class="card-body">
        <h1 class="card-title pricing-card-title">0€</h1>
        <ul class="list-unstyled mt-3 mb-4">
          <li>Un catalogue riche</li>
          <li>De bons avantages</li>
          <li>Une expérience vu comme nul part ailleurs</li>
        </ul>
        <h3><a href="inscription_client.php">s'inscrire</a></h3>
        <a href="connexion_client.php">se connecter</a>
      </div>
    </div>

    <div class="card mb-4 shadow-sm">
      <div class="card-header">
        <h4 class="my-0 font-weight-normal">Enterprise ?</h4>
      </div>
      <div class="card-body">
        <h1 class="card-title pricing-card-title">0€</h1>
        <ul class="list-unstyled mt-3 mb-4">
          <li>Communautée d'acheteurs très actifs</li>
          <li>Facilité d'utilisation</li>
          <li>Ajout de produit et dashboard complet</li>
        </ul>
        <h3><a href="inscription_entreprise.php">s'inscrire</a></h3>
        <a href="connexion_entreprise.php">se connecter</a>
      </div>
    </div>
  </div>
</div>

<?php }
?>
   <div style="text-align: center;"><canvas id="app"></canvas></div>


    </main>

    <?php include('includes/footer.php');?>
  </body>
</html>
