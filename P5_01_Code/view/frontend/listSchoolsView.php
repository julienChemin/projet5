<section id="listSchoolsView" class="container">
	<?php
	if (!empty($data['schools'])) {
		echo '<h1>Liste des établissements scolaires présent sur le site</h1>';
		echo '<p>Cliquez sur un établissement pour avoir plus d\'informations</p>';
		echo '<div id="blockSchools">';
		foreach ($data['schools'] as $school) {
			if ($school->getName() !== NO_SCHOOL) {
				!$school->getIsActive() ? $classIsActive = 'inactiveSchool' : $classIsActive = '';
				!$school->getIsActive() ? $title = ' Cet établissement est inactif sur le site' : $title = '';
				?>
				<div class="blockSchool" title="<?=$title?>">
					<div class="<?=$classIsActive?>">
						<div>
							<figure>
								<img src="<?=$school->getLogo()?>">
							</figure>
						</div>
						<div>
							<h2><?=$school->getName()?></h2>
						</div>
					</div>
				</div>
				<?php
			}
		}
		echo '</div>';
		?>
		<div id="schoolInformation">
			<p id="linkSchoolProfile">
				<a href="#">Voir le profil de : <span></span></a>
			</p>
			<div>
				<h2>Administrateurs</h2>
				<div id="adminSection" class="blockResultUser fullWidth">
					
				</div>
				<h2>Modérateurs</h2>
				<div id="moderatorSection" class="blockResultUser fullWidth">
					
				</div>
				<h2>Étudiants</h2>
				<div id="studentSection" class="blockResultUser fullWidth">
					
				</div>
			</div>
		</div>
		<?php
	} else {
		echo '<p class="blockStyleOne">Il n\'y a aucun établissement présent sur le site pour le moment</p>';
	}
	?>
</section>