<header id="navbar" class="fullWidth">
	<div class="container">
		<span>
			<a href="indexAdmin.php">LOGO</a>
		</span>

		<nav>
			<ul>
				<?php
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
				?>
				
				<li title="Modérer les utilisateurs">
					<a href="indexAdmin.php?action=moderatUsers">
						<i class="fas fa-list-ul"></i>
					</a>
				</li>

				<hr class="hrNavbar">

				<li title="Vers ArtSchool">
					<a href="index.php"><i class="fas fa-external-link-square-alt"></i></a>
				</li>

				<hr class="hrNavbar">

				<li>
					<span>
						<?=$_SESSION['pseudo']?>
					</span>
				</li>

				<hr class="hrNavbar">

				<li title="Deconnection">
					<a href="indexAdmin.php?action=disconnect">
						<i class="fas fa-power-off"></i>
					</a>
				</li>
			</ul>
		</nav>
	</div>
</header>