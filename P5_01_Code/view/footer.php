<footer>
	<hr>
	<section class="container">
		<div>
			<article id="linksList">
				<h3>Établissement inscrit sur ArtSchools</h3>

				<div>
					<ul></ul>
				</div>
			</article>
			<hr class="hrFooter">

			<article id="about">
				<h3>En savoir plus</h3>

				<p>
					Vous pouvez consulter la <a href="index.php?action=faq">F.A.Q</a> , ou rejoindre le <a href="https://discord.gg/SZUF2AMbA3">discord</a> <strong>ArtSchools</strong>
				</p>

				<?php
				if (!empty($_SESSION) && isset($_SESSION['pseudo'])) {
					?>
					<p>
						<a href="index.php?action=report&elem=other">Si vous rencontrez un problème avec le site, vous pouvez le signaler en cliquant ici</a>
					</p>
					<?php
				}
				?>

				<ul>
					<li><a href="index.php?action=cgu">Conditions générales d'utilisation</a></li>
					<li><a href="index.php?action=cgu#rgpd">Mentions légales</a></li>
					<li><a href="index.php?action=faq#dmca">DMCA</a></li>
				</ul>
			</article>
			<hr class="hrFooter">

			<article id="contact">
				<h3>Contact</h3>

				<ul>
					<li><a href="mailto:artschoolsfr@gmail.com"><i class="fas fa-envelope-square"></i></a></li>
					<li><a href="https://discord.gg/SZUF2AMbA3"><i class="fab fa-discord"></i></a></li>
				</ul>
			</article>
		</div>
	</section>
</footer>
