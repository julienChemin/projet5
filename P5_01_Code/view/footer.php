<footer>
	<section class="container">
		<div>
			<article id="linksList">
				<h3>Établissement inscrit sur ArtSchools</h3>
				<div>
					<ul></ul>
				</div>
			</article>
			<hr class="hrFooter">
			<article>
				<h3>En savoir plus</h3>
				<p>
					Vous pouvez consulter la <a href="index.php?action=faq">F.A.Q</a> , ou rejoindre le <a href="https://discord.gg/uDfwPHH">discord</a> ArtSchools
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
			</article>
			<hr class="hrFooter">
			<article id="contact">
				<h3>Contact</h3>
				<ul>
					<li><a href="mailto:artschoolsfr@gmail.com"><i class="fas fa-envelope-square"></i></a></li>
					<li><a href="https://discord.gg/uDfwPHH"><i class="fab fa-discord"></i></a></li>
				</ul>
			</article>
		</div>
	</section>
</footer>
