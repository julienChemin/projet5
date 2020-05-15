<section id="signIn" class="container">
	<form id="formConnect" method="POST" action="index.php?action=signIn">
		<div>
			<p>
				<label for="ConnectPseudo">Identifiant</label>
				<input type="text" name="ConnectPseudo" required="">
			</p>
			<p>
				<label for="ConnectPassword">Mot de passe</label>
				<input type="password" name="ConnectPassword" required="">
			</p>
			<p>
				<label for="stayConnect">Rester connecté</label>
				<input type="checkbox" name="stayConnect" id="stayConnect">
			</p>
			<p>
				<input type="submit" name="submit" value="Connection">
				<span id="buttonForgetPassword">Mot de passe oublié</span>
			</p>
		</div>
		<?php
		if (isset($data['message'])) {
			echo '<p class="msg orang">' . $data['message'] . '</p>';
		}
		?>
	</form>
	<!--display form for forget password-->
	<form id="formForgetPassword" method="POST" action="index.php?action=signIn">
		<p>
			Saisissez l'adresse mail lié à votre compte, 
		</p>
		<p>
			un email vous sera envoyé à cette adresse pour réinitialiser votre mot de passe.
		</p>
		<div>
			<p>
				<label for="postMail">Adresse email</label>
				<input type="email" name="postMail" required="">
			</p>
			<p>
				<input type="submit" name="submit" value="Envoyer">
				<span id="buttonCancel">Annuler</span>
			</p>
		</div>
	</form>
	<!--modal for rules of cookies-->
	<div id="modal">
		<div>
			<p>En cochant "rester connecté", vous acceptez l'utilisation des cookies</p>
			<p>Les cookies sont de petites quantités d’informations stockées dans des fichiers au sein même du navigateur de votre ordinateur.</p>
			<p>Sur ArtSchool, les cookies servent uniquement à garder votre identifiant ainsi qu'une version encodé de votre mot de passe. Cela vous permet de rester connecté</p>
			<div>
				<button id="btnAcceptCookie">Accepter</button>
				<button id="btnCancelCookie">Refuser</button>
			</div>
		</div>
	</div>
</section>
