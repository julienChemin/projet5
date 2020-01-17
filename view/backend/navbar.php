<header id="navbar" class="fullWidth">
	<div class="container">
		<span>
			<a href="indexAdmin.php">LOGO</a>
		</span>

		<nav>
			<ul>
				<?php
				if ($_SESSION['school'] === 'allSchool') {
					?>
					<li>
						<a href="indexAdmin.php?action=addSchool">
							<i class="fas fa-school"></i>
						</a>
					</li>

					<hr class="hrNavbar">
					<?php
				} elseif ($_SESSION['grade'] === 'admin') {
					?>
					<li>
						<a href="indexAdmin.php?action=moderatSchool">
							<i class="fas fa-school"></i>
						</a>
					</li>

					<hr class="hrNavbar">
					<?php
				}

				if ($_SESSION['grade'] === 'admin') {
					?>
					<li>
						<a href="indexAdmin.php?action=moderatAdmin">
							<i class="fas fa-user-cog"></i>
						</a>
					</li>

					<hr class="hrNavbar">
					<?php
				}
				?>
				
				<li>
					<a href="indexAdmin.php?action=moderatUsers">
						<i class="fas fa-list-ul"></i>
					</a>
				</li>

				<hr class="hrNavbar">

				<li>
					<span>
						<?=$_SESSION['pseudo']?>
					</span>
				</li>

				<hr class="hrNavbar">

				<li>
					<a href="indexAdmin.php?action=disconnect">
						<i class="fas fa-power-off"></i>
					</a>
				</li>
			</ul>
		</nav>
	</div>
</header>