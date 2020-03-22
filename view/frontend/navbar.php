<header id="navbar" class="fullWidth">
	<div class="container">
		<div>
			<span>
				<a href="index.php">LOGO</a>
			</span>

			<nav>
				<ul>
					<?php
					if (!isset($_SESSION['grade'])) {
						?>
						<li title="Se connecter">
							<a href="index.php?action=signIn">Se connecter</a>
						</li>

						<li title="S'inscrire">
							<a href="index.php?action=signUp">S'inscrire</a>
						</li>
						<?php
					} else {
						?>
						<li id="pseudo">
							<?=$_SESSION['pseudo']?>
							<i class="fas fa-sort-down"></i>
						</li>

						<?php
						if ($_SESSION['grade'] === ADMIN || $_SESSION['grade'] === MODERATOR) {
							?>
							<hr class="hrNavbar">
							
							<li title="vers l'interface d'administration">
								<a href="indexAdmin.php">
									<i class="fas fa-external-link-alt"></i>
								</a>
							</li>
							<?php
						}
						?>

						<hr class="hrNavbar">

						<li title="Se Déconnecter">
							<a href="index.php?action=disconnect">
								<i class="fas fa-power-off"></i>
							</a>
						</li>
						<?php
					}
					?>
				</ul>
			</nav>
		</div>

		<?php
		if (isset($_SESSION['grade'])) {
			?>
			<div id="menuNavbar">
				<div>
					<ul>
						<li>
							<a href="index.php?action=userProfile&userId=<?=$_SESSION['id']?>">
							Profil<i class="far fa-address-card"></i>
							</a>
						</li>
						
						<hr>

						<?php
						if ($_SESSION['grade'] === ADMIN || $_SESSION['grade'] === MODERATOR) {
							?>
							<li title="Vers l'interface d'administration">
								<a href="indexAdmin.php">
									Vers l'interface d'administration<i class="fas fa-external-link-alt"></i>
								</a>
							</li>

							<hr>
							<?php
						}

						?>
						<li title="Se Déconnecter">
							<a href="index.php?action=disconnect">
								Se Déconnecter<i class="fas fa-power-off"></i>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<?php
		}
		?>
	</div>
</header>
