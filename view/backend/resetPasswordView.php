<?php

if (isset($data['user']) && $data['user']->getBeingReset()) {
	if ($data['user']->getTemporaryPassword() === $_GET['key'] && $data['user']->getId() === $_GET['id']) {
		?>

		<section id="resetPassword">
			<form id="formResetPassword" method="POST" action="indexAdmin.php?action=resetPassword">
				<div>
					<p>
						<label for="newPassword">Nouveau mot de passe</label>
						<input type="password" name="newPassword" required="">
					</p>

					<p>
						<label for="confirmNewPassword">Confirmez votre nouveau mot de passe</label>
						<input type="password" name="confirmNewPassword" required="">
					</p>

					<p>
						<input type="hidden" name="id" value=<?=$_GET['id']?>>
					</p>

					<p>
						<input type="hidden" name="key" value=<?=$_GET['key']?>>
					</p>

					<p>
						<input type="submit" name="submit" value="valider">
					</p>
				</div>

				<?php
				if (isset($data['message'])) {
					echo '<p class="infoError">' . $data['message'] . '</p>';
				}
				?>
			</form>
		</section>
		<?php
	} else {
		throw new Exception("Ce lien n'est plus valide");
		
	}
} else {
	?>
	<section id="resetPassword">
		<div>
			<?php
			if (isset($data['message'])) {
				echo '<p class="infoComment">' . $data['message'] . '</p>';
			}
			?>
			<p class="infoComment"><a href="indexAdmin.php">Retourner sur le site</a></p>
		</div>
	</section>
	<?php
}
