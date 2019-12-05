<section id="blockModeratSchool" class="container">
	<?php
	if (isset($_GET['option']) && $_GET['option'] === 'add') {
		//add school
		?>
		<article id="addSchool">
			<form id="formAddSchool" method="POST" action="indexAdmin.php?action=addSchool&amp;option=add">
				<div>
					<hr>
					<h2>Information sur l'établissement</h2>

					<div>
						<p>
							<label for="schoolName">Nom de l'établissement</label>
							<input type="text" name="schoolName" required="">
						</p>

						<p>
							<label for="schoolCode">Code d'affiliation</label>
							<input type="text" name="schoolCode" required="">
						</p>

						<p>
							<label for="schoolNbEleve">Nombre d'élève</label>
							<input type="text" name="schoolNbEleve" required="">
						</p>

						<p>
							<label for="schoolDuration">Durée de l'abonnement</label>
							<select name="schoolDuration" id="schoolDuration" required="">
								<option value="3">3 mois</option>
								<option value="6">6 mois</option>
								<option value="12">12 mois</option>
							</select>
						</p>

						<p>
							<input type="button" id="addSchoolBtnNext" name="next" value="Suivant">
						</p>
					</div>

					<hr>
				</div>
					
				<div>
					<hr>
					<h2>Information sur l'administrateur de l'établissement</h2>

					<div>
						<p>
							<label for="adminName">Identifiant</label>
							<input type="text" name="adminName" required="">
						</p>

						<p>
							<label for="adminPassword">Mot de passe</label>
							<input type="password" name="adminPassword" required="">
						</p>

						<p>
							<label for="adminConfirmPassword">Confirmez le mot de passe</label>
							<input type="password" name="adminConfirmPassword" required="">
						</p>

						<p>
							<label for="adminMail">Adresse email</label>
							<input type="email" name="adminMail" required="">
						</p>
					</div>

					<div>
						<p>
							<input type="button" id="addSchoolBtnPrevious" name="previous" value="Précédent">
							<input type="submit" name="submit" value="Ajouter">
						</p>
					</div>

					<hr>
				</div>

				<?php
				if (isset($data['message'])) {	
					?>
					<div>
						<p class="msg orang"><?=$data['message']?></p>
						<input type="button" id="addSchoolBtnOk" name="addSchoolBtnOk" value="Ok">
					</div>
					<?php
				}
				?>
			</form>
		</article>
		<?php
	} else {
		//choose add or edit school
		?>
		<article id="homeModeratSchool">
			<a href="indexAdmin.php?action=addSchool&amp;option=add">
				<div class="itemHomeAdmin">
					<i class="far fa-plus-square"></i>
					<span>Ajouter une Art School</span>
				</div>
			</a>

			<a href="indexAdmin.php?action=editSchool">
				<div class="itemHomeAdmin">
					<i class="far fa-edit"></i>
					<span>Editer une Art School</span>
				</div>
			</a>
		</article>
		<?php
	}
	?>
</section>
