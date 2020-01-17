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
</section>