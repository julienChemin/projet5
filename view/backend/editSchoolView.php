<section id="blockEditSchool" class="container">
	<article id="editSchool">
		<?php
		if (!empty($_GET['elem'])) {
			//display form to edit elements
			switch ($_GET['elem']) {
				case 'name' :
					?>
					<form method="POST" action="indexAdmin.php?action=editSchool">
						<p>
							<label for="editName">Nouveau nom de l'établissement</label>
							<input type="text" name="editName" required="">
						</p>

						<p>
							<input type="submit" name="valider" value="Valider">
						</p>

						<p>
							<input type="hidden" name="elem" value="<?=$_GET['elem']?>">
							<input type="hidden" name="schoolName" value="<?=$_POST['schoolName']?>">
						</p>
					</form>
					<?php
				break;
				case 'admin' :
					?>
					<form method="POST" action="indexAdmin.php?action=editSchool">
						<p>
							<label for="editAdmin">Nom du nouvel Administrateur</label>
							<input type="text" name="editAdmin" required="">
						</p>

						<p>
							<input type="submit" name="valider" value="Valider">
						</p>

						<p>
							<input type="hidden" name="elem" value="<?=$_GET['elem']?>">
							<input type="hidden" name="schoolName" value="<?=$_POST['schoolName']?>">
						</p>
					</form>
					<?php
				break;
				case 'code' :
					?>
					<form method="POST" action="indexAdmin.php?action=editSchool">
						<p>
							<label for="editCode">Nouveau code</label>
							<input type="text" name="editCode" required="">
						</p>

						<p>
							<input type="submit" name="valider" value="Valider">
						</p>

						<p>
							<input type="hidden" name="elem" value="<?=$_GET['elem']?>">
							<input type="hidden" name="schoolName" value="<?=$_POST['schoolName']?>">
						</p>
					</form>
					<?php
				break;
				case 'nbEleve' :
					?>
					<form method="POST" action="indexAdmin.php?action=editSchool">
						<p>
							<label for="editNbEleve">Nombre d'élèves</label>
							<input type="text" name="editNbEleve" required="">
						</p>

						<p>
							<input type="submit" name="valider" value="Valider">
						</p>

						<p>
							<input type="hidden" name="elem" value="<?=$_GET['elem']?>">
							<input type="hidden" name="schoolName" value="<?=$_POST['schoolName']?>">
						</p>
					</form>
					<?php
				break;
				case 'logo' :
					?>
					<form method="POST" action="indexAdmin.php?action=editSchool">
						<p>
							<label for="editLogo">Chemin vers le nouveau logo</label>
							<input type="text" name="editLogo" required="">
						</p>

						<p>
							<input type="submit" name="valider" value="Valider">
						</p>

						<p>
							<input type="hidden" name="elem" value="<?=$_GET['elem']?>">
							<input type="hidden" name="schoolName" value="<?=$_POST['schoolName']?>">
						</p>
					</form>
					<?php
				break;
				case 'dateDeadline' :
					?>
					<form method="POST" action="indexAdmin.php?action=editSchool">
						<p>
							<label for="editDateDeadline">Nombre de mois à ajouter à la date d'échéance</label>
							<select name="editDateDeadline" id="editDateDeadline" required="">
								<option value="3">3 mois</option>
								<option value="6">6 mois</option>
								<option value="12">12 mois</option>
							</select>
						</p>

						<p>
							<input type="submit" name="valider" value="Valider">
						</p>

						<p>
							<input type="hidden" name="elem" value="<?=$_GET['elem']?>">
							<input type="hidden" name="schoolName" value="<?=$_POST['schoolName']?>">
						</p>
					</form>
					<?php
				break;
				default :
					?>
					<div class="blockStyleOne">
						<p class="msg orang textCenter">Les informations renseignées sont incorrectes</p>
						<a href="indexAdmin.php?action=moderatSchool"><input type="button" id="editSchoolBtnOk" name="editSchoolBtnOk" value="Ok"></a>
					</div>
					<?php
			}
		}

		if (isset($data['message'])) {	
			?>
			<div class="blockStyleOne">
				<p class="msg orang textCenter"><?=$data['message']?></p>
				<a href="indexAdmin.php?action=moderatSchool"><input type="button" id="editSchoolBtnOk" name="editSchoolBtnOk" value="Ok"></a>
			</div>
			<?php
		}
	?>
	</article>
</section>
