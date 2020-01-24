<section id="homeAdmin" class="container">
	<?php
	if (isset($_SESSION['grade']) && ($_SESSION['grade'] === ADMIN  || $_SESSION['grade'] === MODERATOR)) {
		// is connected
		?>
		<article id="menuHome">
			<?php
			if ($_SESSION['school'] === ALL_SCHOOL) {
				?>
				<a href="indexAdmin.php?action=addSchool">
					<div class="itemHomeAdmin">
						<i class="fas fa-school"></i>
						<span>Modération des Art Schools</span>
					</div>
				</a>
				<?php
			} elseif ($_SESSION['grade'] === ADMIN) {
				?>
				<a href="indexAdmin.php?action=moderatSchool">
					<div class="itemHomeAdmin">
						<i class="fas fa-school"></i>
						<span>Modérer mon Art School</span>
					</div>
				</a>
				<?php
			}

			if ($_SESSION['grade'] === ADMIN) {
				?>
				<a href="indexAdmin.php?action=moderatAdmin">
					<div class="itemHomeAdmin">
						<i class="fas fa-user-cog"></i>
						<span>Modération des comptes administrateurs / modérateurs</span>
					</div>
				</a>
				<?php
			}
			?>
		
			<a href="indexAdmin.php?action=moderatUsers">
				<div class="itemHomeAdmin">
					<i class="fas fa-list-ul"></i>
					<span>Consulter la liste des élèves</span>
				</div>
			</a>
		</article>

		<aside id="slowSlide" class="sliderPosts">
			<div class="container">
				<h2>Récemment posté par des élèves de votre établissement</h2>
				<a href="#">Tout voir</a>
			</div>

			<hr>

			<div class="container posts">
				<div class="arrow arrowLeft">
					<i class="fas fa-chevron-circle-left"></i>
				</div>

				<div class="arrow arrowRight">
					<i class="fas fa-chevron-circle-left"></i>
				</div>

				<div class="slide">
					<div class="items">
						<figure>
							<img src="public/images/test.png">
						</figure>
					</div>

					<div class="items">
						<figure>
							<img src="public/images/test2.jpg">
						</figure>
					</div>

					<div class="items">
						<figure>
							<img src="public/images/test.png">
						</figure>
					</div>

					<div class="items">
						<figure>
							<img src="public/images/test2.jpg">
						</figure>
					</div>

					<div class="items">
						<figure>
							<img src="public/images/test2.jpg">
						</figure>
					</div>
				</div>

				<div class="slide">
					<div class="items">
						<figure>
							<img src="public/images/test.png">
						</figure>
					</div>

					<div class="items">
						<figure>
							<img src="public/images/test2.jpg">
						</figure>
					</div>

					<div class="items">
						<figure>
							<img src="public/images/test.png">
						</figure>
					</div>

					<div class="items">
						<figure>
							<img src="public/images/test2.jpg">
						</figure>
					</div>

					<div class="items">
						<figure>
							<img src="public/images/test2.jpg">
						</figure>
					</div>
				</div>
			</div>
		</aside>
		<?php
	} else {
		// try to connect
		?>
		<form id="formConnect" method="POST" action="indexAdmin.php">
			<div>
				<p>
					<label for="ConnectPseudoAdmin">Identifiant</label>
					<input type="text" name="ConnectPseudoAdmin" required="">
				</p>

				<p>
					<label for="ConnectPasswordAdmin">Mot de passe</label>
					<input type="password" name="ConnectPasswordAdmin" required="">
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
		<form id="formForgetPassword" method="POST" action="indexAdmin.php">
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
	<?php
	}
	?>
</section>
