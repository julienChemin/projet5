<!DOCTYPE html>
<html lang="fr">
	<head>
		<title>Art-school</title>

		<!--meta-->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Art-school est un site qui répertorie des artistes en cours d'études">

		<meta name="twitter:title" content="Art-school">
		<meta name="twitter:description" content="'École d'Art' est un site qui répertorie des artistes en cours d'études">
		<meta name="twitter:image" content="images/a.jpeg">

		<meta property="og:title" content="École d'Art" />
		<meta property="og:type" content="website" />
		<meta property="og:url" content="julienchemin.fr/projet5/art-school.html" />
		<meta property="og:image" content="images/a.jpeg" />
		<meta property="og:description" content="Art-school est un site qui répertorie des artistes en cours d'études" /> 
		<meta property="og:site_name" content="Art-school" />

		<!--css-->
		<link rel="stylesheet" type="text/css" href="public/css/style.css">

		<!--font awesome-->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

		<!--Google font-->
		<link href="https://fonts.googleapis.com/css?family=Asap|Slabo+27px&display=swap" rel="stylesheet"> 
		
	</head>
	<body>
		<?php 
		if (isset(Chemin\ArtSchool\Model\Backend::$isConnected) && Chemin\ArtSchool\Model\Backend::$isConnected) {
			require('backend/navbar.php');
		} else {
			require('frontend/navbar.php');
		}
		?>

		<main>
			<?=$content?>
		</main>

		<?php
		if (isset($data['option'])) {
			foreach ($data['option'] as $option) {
				switch ($option) {
					case 'slide' :
						?>
						<script src="public/js/slide.js"></script>
						<?php
						break;
					case 'forgetPassword' :
						?>
						<script src="public/js/forgetPassword.js"></script>
						<?php
						break;
					case 'addSchool' :
						?>
						<script src="public/js/addSchool.js"></script>
						<?php
						break;
					case 'editSchool' :
						?>
						<script src="public/js/editSchool.js"></script>
						<?php
						break;
					default :
						throw new Exception('L\'option indiqué n\'existe pas.');
						break;
				}
			}
		}
		?>
	</body>
</html>
