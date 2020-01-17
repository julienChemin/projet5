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
					<li>
						<a href="index.php?action=signIn">Se connecter</a>
					</li>

					<li>
						<a href="index.php?action=signUp">S'inscrire</a>
					</li>
					<?php
				} else {
					?>
					<li>
						<?=$_SESSION['pseudo']?>
					</li>

					<hr class="hrNavbar">

					<li>
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
