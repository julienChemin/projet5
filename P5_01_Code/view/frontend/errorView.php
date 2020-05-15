<?php
if (empty($data['error_msg'])) {
	$message = 'Une erreur inconnu est survenue, merci de réessayer'; 
} else {
	$message = $data['error_msg'];
}
$arrMessage = explode(".", $message);
?>
<section id="msg" class="container">
	<div class="blockStyleOne">
		<h1>Une erreur est survenue : </h1>
		<?php
		foreach ($arrMessage as $msg) {
			echo '<p>' . $msg . '</p>' ;
		}
		?>
		<p><a href="index.php">Retourner sur la page d'accueil</a></p>
	</div>	
</section>
