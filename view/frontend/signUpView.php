<section id="signUp" class="container">
	<div class="textCenter">
		<p>- Pour le moment, la création de compte est réservé au élèves faisant parti d'un établissement scolaire associé au site Art-School.fr</p>
		<p>- Peut etre plus tard.</p>	
	</div>

	<form id="formSignUp" method="POST" action="index.php?action=signUp">
		<div>
			<div>
				<p>
					<label for="pseudo">Identifiant</label>
					<input type="text" name="pseudo" required="">
				</p>

				<p>
					<label for="mail">Adresse e-mail</label>
					<input type="email" name="mail" required="">
				</p>
			</div>
			
			<hr>

			<div>
				<p>
					<label for="password">Mot de passe</label>
					<input type="password" name="password" required="">
				</p>

				<p>
					<label for="confirmPassword">Confirmez le mot de passe</label>
					<input type="password" name="confirmPassword" required="">
				</p>
			</div>

			<hr>

			<p id="postAffiliationCode">
				<label for="affiliationCode">Code d'affiliation</label>
				<input type="text" name="affiliationCode" required="">
			</p>

			<p>
				<input type="submit" name="submit" value="Valider">
			</p>
		</div>

		<?php
		if (isset($data['message'])) {
			echo '<p class="msg orang">' . $data['message'] . '</p>';
		}
		?>
	</form>
</section>