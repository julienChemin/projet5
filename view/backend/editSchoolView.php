<section id="blockModeratSchool" class="container">
	<article id="editSchool">
		<?php
		if (isset($_GET['id']) && $_GET['id'] > 0) {
			//edit some element
			switch ($_GET['elem']) {
				case 'name' :
					?>
					<form method="POST" action="indexAdmin.php?action=editSchool&amp;elem=name&amp;id=<?=$_GET['id']?>">
						<p>
							<label for="editName">Nouveau nom de l'établissement</label>
							<input type="text" name="editName" required="">
						</p>

						<p>
							<input type="submit" name="valider" value="Valider">
						</p>

						<p>
							<input type="hidden" name="id" value="<?=$_GET['id']?>">
						</p>
					</form>
					<?php
				break;
				case 'admin' :
					?>
					<form method="POST" action="indexAdmin.php?action=editSchool&amp;elem=admin&amp;id=<?=$_GET['id']?>">
						<p>
							<label for="editAdmin">Nom du nouvel Administrateur</label>
							<input type="text" name="editAdmin" required="">
						</p>

						<p>
							<input type="submit" name="valider" value="Valider">
						</p>

						<p>
							<input type="hidden" name="id" value="<?=$_GET['id']?>">
							<input type="hidden" name="idAdmin" value="<?=$_GET['idAdmin']?>">
						</p>
					</form>
					<?php
				break;
				case 'code' :
					?>
					<form method="POST" action="indexAdmin.php?action=editSchool&amp;elem=code&amp;id=<?=$_GET['id']?>">
						<p>
							<label for="editCode">Nouveau code</label>
							<input type="text" name="editCode" required="">
						</p>

						<p>
							<input type="submit" name="valider" value="Valider">
						</p>

						<p>
							<input type="hidden" name="id" value="<?=$_GET['id']?>">
						</p>
					</form>
					<?php
				break;
				case 'nbEleve' :
					?>
					<form method="POST" action="indexAdmin.php?action=editSchool&amp;elem=nbEleve&amp;id=<?=$_GET['id']?>">
						<p>
							<label for="editNbEleve">Nombre d'élèves</label>
							<input type="text" name="editNbEleve" required="">
						</p>

						<p>
							<input type="submit" name="valider" value="Valider">
						</p>

						<p>
							<input type="hidden" name="id" value="<?=$_GET['id']?>">
						</p>
					</form>
					<?php
				break;
				case 'logo' :
					?>
					<form method="POST" action="indexAdmin.php?action=editSchool&amp;elem=logo&amp;id=<?=$_GET['id']?>">
						<p>
							<label for="editLogo">Chemin vers le nouveau logo</label>
							<input type="text" name="editLogo" required="">
						</p>

						<p>
							<input type="submit" name="valider" value="Valider">
						</p>

						<p>
							<input type="hidden" name="id" value="<?=$_GET['id']?>">
						</p>
					</form>
					<?php
				break;
				case 'dateDeadline' :
					?>
					<form method="POST" action="indexAdmin.php?action=editSchool&amp;elem=dateDeadline&amp;id=<?=$_GET['id']?>">
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
							<input type="hidden" name="id" value="<?=$_GET['id']?>">
						</p>
					</form>
					<?php
				break;
			}
			if (isset($data['message'])) {	
				?>
				<div class="blockStyleOne">
					<p class="msg orang textCenter"><?=$data['message']?></p>
					<a href="indexAdmin.php?action=editSchool"><input type="button" id="editSchoolBtnOk" name="editSchoolBtnOk" value="Ok"></a>
				</div>
				<?php
			}
		} else {
			//consulting school info
			foreach ($data['schools'] as $school) {
				?>
				<div class="blockSchool">
					<div>
						<div>
							<figure>
								<img src='<?=$school->getLogo()?>'>
							</figure>
						</div>

						<div>
							<h2>
								<?=$school->getName()?>
							</h2>
						</div>

						<div>
							<i class="far fa-caret-square-down"></i>
							<i class="far fa-caret-square-up"></i>
						</div>
					</div>

					<div>
						<table>
							<tr>
								<td>
									Nom de l'établissement
								</td>

								<td>
									<?=$school->getName()?>
								</td>

								<td>
									<a href="indexAdmin.php?action=editSchool&amp;elem=name&amp;id=<?=$school->getId()?>">
										<i class="far fa-edit"></i>
									</a>
								</td>
							</tr>

							<tr>
								<td>
									Administrateur
								</td>

								<td>
									<?=$school->getNameAdmin()?>
								</td>

								<td>
									<a href="indexAdmin.php?action=editSchool&amp;elem=admin&amp;id=<?=$school->getId()?>&amp;idAdmin=<?=$school->getIdAdmin()?>">
										<i class="far fa-edit"></i>
									</a>
								</td>
							</tr>

							<tr>
								<td>
									Code d'affiliation
								</td>

								<td>
									<?=$school->getCode()?>
								</td>

								<td>
									<a href="indexAdmin.php?action=editSchool&amp;elem=code&amp;id=<?=$school->getId()?>">
										<i class="far fa-edit"></i>
									</a>
								</td>
							</tr>

							<tr>
								<td>
									Nombre d'élèves
								</td>

								<td>
									<?=$school->getNbEleve()?>
								</td>

								<td>
									<a href="indexAdmin.php?action=editSchool&amp;elem=nbEleve&amp;id=<?=$school->getId()?>">
										<i class="far fa-edit"></i>
									</a>
								</td>
							</tr>

							<tr>
								<td>
									Logo
								</td>

								<?php
								if ($school->getLogo() === 'public/images/question-mark.png') {
									echo '<td>Default</td>';
								} else {
									echo '<td>Custom</td>';
								}
								?>

								<td>
									<a href="indexAdmin.php?action=editSchool&amp;elem=logo&amp;id=<?=$school->getId()?>">
										<i class="far fa-edit"></i>
									</a>
								</td>
							</tr>

							<tr>
								<td>
									Date de fin du contrat
								</td>

								<td>
									<?=$school->getDateDeadline()?>
								</td>

								<td>
									<a href="indexAdmin.php?action=editSchool&amp;elem=dateDeadline&amp;id=<?=$school->getId()?>">
										<i class="far fa-edit"></i>
									</a>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<?php
			}
		}
		?>
	</article>
</section>
<?php