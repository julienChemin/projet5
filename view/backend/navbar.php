<header id="navbar" class="fullWidth">
	<div class="container">
		<div>
			<span>
				<a href="indexAdmin.php">LOGO</a>
			</span>

			<nav>
				<ul>
					<li title="Modérer les utilisateurs">
						<a href="indexAdmin.php?action=moderatUsers">
							<i class="fas fa-list-ul"></i>
						</a>
					</li>

					<hr class="hrNavbar">

					<?php
					if ($_SESSION['grade'] === ADMIN) {
						?>
						<li title="Modérer l'administration">
							<a href="indexAdmin.php?action=moderatAdmin">
								<i class="fas fa-user-cog"></i>
							</a>
						</li>

						<hr class="hrNavbar">
						<?php
					}

					if ($_SESSION['school'] === ALL_SCHOOL) {
						?>
						<li title="Ajout / édition des établissements">
							<a href="indexAdmin.php?action=addSchool">
								<i class="fas fa-school"></i>
							</a>
						</li>

						<hr class="hrNavbar">
						<?php
					} elseif ($_SESSION['grade'] === ADMIN) {
						?>
						<li title="Modérer mon établissement">
							<a href="indexAdmin.php?action=moderatSchool">
								<i class="fas fa-school"></i>
							</a>
						</li>

						<hr class="hrNavbar">
						<?php
					}
					?>

					<li id="pseudo">
						<?=$_SESSION['pseudo']?>
						<i class="fas fa-sort-down"></i>
					</li>

					<hr class="hrNavbar">

					<li title="Vers ArtSchool">
						<a href="index.php"><i class="fas fa-external-link-alt"></i></a>
					</li>

					<hr class="hrNavbar">

					<li title="Se Déconnecter">
						<a href="indexAdmin.php?action=disconnect">
							<i class="fas fa-power-off"></i>
						</a>
					</li>
				</ul>
			</nav>
		</div>

		<div id="menuNavbar">
			<div>
				<ul>
					<li>
						<a href="indexAdmin.php">
							Page d'accueil<i class="fas fa-home"></i>
						</a>
					</li>

					<hr>

					<li>
						<a href="indexAdmin.php?action=moderatUsers">
							Modérer les utilisateurs<i class="fas fa-list-ul"></i>
						</a>
					</li>

					<?php
					if ($_SESSION['grade'] === ADMIN) {
						?>
						<li>
							<a href="indexAdmin.php?action=moderatAdmin">
								Modérer l'administration<i class="fas fa-user-cog"></i>
							</a>
						</li>
						<?php
					}

					if ($_SESSION['school'] === ALL_SCHOOL) {
						?>
						<li>
							<a href="indexAdmin.php?action=addSchool">
								Ajout / édition des établissements<i class="fas fa-school"></i>
							</a>
						</li>
						<?php
					} elseif ($_SESSION['grade'] === ADMIN) {
						?>
						<li>
							<a href="indexAdmin.php?action=moderatSchool">
								Modérer mon établissement<i class="fas fa-school"></i>
							</a>
						</li>
						<?php
					}
					?>

					<hr>

					<li>
						<a href="#">
							Profil de l'établissement<i class="far fa-address-card"></i>
						</a>
					</li>

					<li>
						<a href="indexAdmin.php?action=schoolHistory">
							Historique<i class="fas fa-history"></i>
						</a>
					</li>

					<hr>

					<li>
						<a href="index.php">
							Vers le site ArtSchool<i class="fas fa-external-link-alt"></i>
						</a>
					</li>

					<hr>

					<li>
						<a href="indexAdmin.php?action=disconnect">
							Se déconnecter<i class="fas fa-power-off"></i>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</header>