

<!DOCTYPE html>
<html>
	<head>
		<?php include('includes/head.php'); ?>

	</head>
	<body>
<?php include('includes/header.php'); ?>
<?php session_destroy();?>

<div class="taille">
	<main>



<div class="connex">

				<form class="" action="verification_client.php" method="post">
				  <div class="form-group" action="verification_client.php" method="post">
						<h2>CONNEXION CLIENT</h2>
						<?php
		      if(isset($_GET['message']) && !empty($_GET['message'])){?>
		        <p class="error"><?php
		        echo $_GET['message'];
						?></p><?php
		      }
		      ?>
				    <label for="exampleInputEmail1"></label>
				    <input type="email" class="form-control" name="email" placeholder="Votre email" value="<?= isset($_COOKIE['email']) ? $_COOKIE['email'] : '' ?>" required="required">
				    <small id="emailHelp" class="form-text text-muted">Shhhhhhhht... On donnera votre email à personne ;)</small>
				  </div>
				  <div class="form-group">
				    <label for="exampleInputPassword1"></label>
				    <input type="password" class="form-control" name="password" placeholder="Votre mot de passe">
				  </div>
				  <button type="submit" class="btn btn-primary">Se connecter</button>
					<a href="inscription_entreprise.php" class="ca">Tu n'as pas de compte?</a>
				</form>
</div>


		</main>
	</div>

		<?php include('includes/footer.php'); ?>

	</body>
	</div>
</html>
