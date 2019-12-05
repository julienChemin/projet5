<?php

if (empty($data['error_msg'])) {
		$message = 'Une erreur inconnu est survenue, merci de réessayer.'; 
	} else {
		$message = $data['error_msg'];
	}

?>

<section id="msg" class="container">
	<h1>Une erreur est survenue : </h1>

	<br>
	
	<span><?=$message?></span>	
</section>

<?php
