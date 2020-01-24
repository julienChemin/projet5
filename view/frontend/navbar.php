<header id="navbar" class="fullWidth">
	<div class="container">
		<a href="index.php">
			<span>LOGO</span>
		</a>
		<h1>Art School</h1>
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
					<li>
						<?=$_SESSION['pseudo']?>
					</li>

					<?php
					if ($_SESSION['grade'] === ADMIN || $_SESSION['grade'] === MODERATOR) {
						?>
						<li title="vers l'interface d'administration">
							<a href="indexAdmin.php"><i class="fas fa-external-link-square-alt"></i></a>
						</li>
						<?php
					}
					?>

					<hr class="hrNavbar">

					<li title="Deconnection">
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
</header>
