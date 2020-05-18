<!DOCTYPE html>
<html lang="fr">
	<head>
		<title>Art-school</title>
		<link rel="shortcut icon" type="image/png" href="public/images/favicon.png"/>
		<!--meta-->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Art-school est un site qui répertorie des artistes en cours d'études">

		<meta name="twitter:title" content="Art-school">
		<meta name="twitter:description" content="Art-school est un site qui répertorie des artistes en cours d'études">
		<meta name="twitter:image" content="images/a.jpeg">

		<meta property="og:title" content="Art-school" />
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
		if (defined('BACKEND') && isset($_SESSION['grade']) && ($_SESSION['grade'] === ADMIN  || $_SESSION['grade'] === MODERATOR)) {
			require('backend/navbar.php');
		} else {
			require('frontend/navbar.php');
		}
		?>
		<main>
			<?=$content?>
		</main>
		<?php
		require('footer.php');
		if (isset($data['option'])) {
			foreach ($data['option'] as $option) {
				switch ($option) {
					case 'homeAdmin' :
						echo '<script src="public/js/slide.js"></script>';
						echo '<script src="public/js/homeAdmin.js"></script>';
					break;
					case 'buttonToggleSchool' :
						echo '<script src="public/js/buttonToggleSchool.js"></script>';
					break;
					case 'forgetPassword' :
						echo '<script src="public/js/forgetPassword.js"></script>';
					break;
					case 'addSchool' :
						echo '<script src="public/js/addSchool.js"></script>';
					break;
					case 'moderatSchool' :
						echo '<script src="public/js/moderatSchool.js"></script>';
					break;
					case 'moderatAdmin' :
						echo '<script src="public/js/moderatAdmin.js"></script>';
					break;
					case 'moderatUsers' :
						echo '<script src="public/js/moderatUsers.js"></script>';
					break;
					case 'schoolHistory' :
						echo '<script src="public/js/schoolHistory.js"></script>';
					break;
					case 'signUp' :
						echo '<script src="public/js/signUp.js"></script>';
					break;
					case 'signIn' :
						echo '<script src="public/js/signIn.js"></script>';
					break;
					case 'userProfile' :
						echo '<script src="public/js/fillProfileWithPosts.js"></script>';
						echo '<script src="public/js/userProfile.js"></script>';
					break;
					case 'schoolProfile' :
						echo '<script src="public/js/fillProfileWithPosts.js"></script>';
						echo '<script src="public/js/schoolProfile.js"></script>';
					break;
					case 'tinyMCE' :
						require('gitignore/key.php');
						echo '<script src="https://cdn.tiny.cloud/1/' . $tinyMCEapiKey . '/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>';
						echo '<script src="public/js/tinyMCEinit.js"></script>';
					break;
					case 'addPost' :
						echo '<script src="public/js/addPost.js"></script>';
					break;
					default :
						throw new Exception('L\'option indiqué n\'existe pas.');
					break;
				}
			}
		}
		?>
		<script src="public/js/ajax.js"></script>
		<script src="public/js/footer.js"></script>
		<script src="public/js/navbar.js"></script>
	</body>
</html>
