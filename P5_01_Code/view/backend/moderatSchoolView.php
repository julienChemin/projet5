<section id="blockModeratSchool" class="container">
	<article id="moderatSchool">
		<?php
		if (!empty($data['schools'])) {
			if ($_SESSION['school'] === ALL_SCHOOL) {
				//consulting all school
				foreach ($data['schools'] as $school) {
					if ($school->getIsActive()) {
						$classIsActive = "";
					} else {
						$classIsActive = "inactiveSchool";
					}
					?>
					
					<div class="blockSchool">
						<div class="<?=$classIsActive?>">
							<div>
								<figure>
									<img src='<?=$school->getLogo()?>'>
								</figure>
							</div>

							<div>
								<h1>
									<?=$school->getName()?>
								</h1>
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
										<form method="POST" action="indexAdmin.php?action=editSchool&amp;elem=name">
											<input type="hidden" name="schoolName" value="<?=$school->getName()?>">
											<input type="submit" name="submit" value="Modifier">
										</form>
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
										<form method="POST" action="indexAdmin.php?action=editSchool&amp;elem=admin">
											<input type="hidden" name="schoolName" value="<?=$school->getName()?>">
											<input type="submit" name="submit" value="Modifier">
										</form>
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
										<form method="POST" action="indexAdmin.php?action=editSchool&amp;elem=code">
											<input type="hidden" name="schoolName" value="<?=$school->getName()?>">
											<input type="submit" name="submit" value="Modifier">
										</form>
									</td>
								</tr>

								<tr>
									<td>
										Nombre de comptes
									</td>

									<td>
										Disponible - <?=$school->getNbEleve()?> | Actif - <?=$school->getNbActiveAccount()?>
									</td>

									<td>
										<form method="POST" action="indexAdmin.php?action=editSchool&amp;elem=nbEleve">
											<input type="hidden" name="schoolName" value="<?=$school->getName()?>">
											<input type="submit" name="submit" value="Modifier">
										</form>
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
										<form method="POST" action="indexAdmin.php?action=editSchool&amp;elem=logo">
											<input type="hidden" name="schoolName" value="<?=$school->getName()?>">
											<input type="submit" name="submit" value="Modifier">
										</form>
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
										<form method="POST" action="indexAdmin.php?action=editSchool&amp;elem=dateDeadline">
											<input type="hidden" name="schoolName" value="<?=$school->getName()?>">
											<input type="submit" name="submit" value="Modifier">
										</form>
									</td>
								</tr>

								<tr>
									<td>
										Établissement
									</td>

									<td>
										<?php
										if ($school->getIsActive()) {
											echo 'Actif';
										} else {
											echo 'Inactif';
										}
										?>
									</td>

									<td>
										<?php
										if ($school->getIsActive()) {
											$value = "Désactiver";
											$elem = "toInactive";
										} else {
											$value = "Activer";
											$elem = "toActive";
										}
										?>

										<form method="POST" action="indexAdmin.php?action=editSchool&amp;elem=<?=$elem?>">
											<input type="hidden" name="schoolName" value="<?=$school->getName()?>">
											<input type="submit" name="submit" value="<?=$value?>">
										</form>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<?php
				}
			} elseif ($_SESSION['grade'] === ADMIN) {
				//consulting user school
				$school = $data['schools'];
				if ($school->getName() === $_SESSION['school']) {
					if ($school->getIsActive()) {
						$classIsActive = "";
					} else {
						$classIsActive = "inactiveSchool";
					}
					?>

					<div class="blockSchool">
						<div class="<?=$classIsActive?>">
							<div>
								<figure>
									<img src='<?=$school->getLogo()?>'>
								</figure>
							</div>

							<div>
								<h1>
									<?=$school->getName()?>
								</h1>
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
										<form method="POST" action="indexAdmin.php?action=editSchool&amp;elem=name">
											<input type="hidden" name="schoolName" value="<?=$school->getName()?>">
											<input type="submit" name="submit" value="Modifier">
										</form>
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
										<form method="POST" action="indexAdmin.php?action=editSchool&amp;elem=admin">
											<input type="hidden" name="schoolName" value="<?=$school->getName()?>">
											<input type="submit" name="submit" value="Modifier">
										</form>
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
										<form method="POST" action="indexAdmin.php?action=editSchool&amp;elem=code">
											<input type="hidden" name="schoolName" value="<?=$school->getName()?>">
											<input type="submit" name="submit" value="Modifier">
										</form>
									</td>
								</tr>

								<tr>
									<td>
										Nombre de comptes
									</td>

									<td>
										Disponible - <?=$school->getNbEleve()?> | Actif - <?=$school->getNbActiveAccount()?>
									</td>

									<td>
										<form method="POST" action="indexAdmin.php?action=moderatUsers">
											<input type="hidden" name="schoolName" value="<?=$school->getName()?>">
											<input type="submit" name="submit" value="Consulter">
										</form>
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
										<form method="POST" action="indexAdmin.php?action=editSchool&amp;elem=logo">
											<input type="hidden" name="schoolName" value="<?=$school->getName()?>">
											<input type="submit" name="submit" value="Modifier">
										</form>
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
										<form method="POST" action="indexAdmin.php?action=schoolHistory">
											<input type="hidden" name="schoolName" value="<?=$school->getName()?>">
											<input type="submit" name="submit" value="Consulter">
										</form>
									</td>
								</tr>

								<tr>
									<td>
										Établissement
									</td>

									<td>
										<?php
										if ($school->getIsActive()) {
											echo 'Actif';
										} else {
											echo 'Inactif';
										}
										?>
									</td>

									<td>
										-
									</td>
								</tr>
							</table>
						</div>
					</div>
					<?php
				}
			}
		} else {
			//no school to display
			?>
			<div class="blockStyleOne">
				<p class="msg orang textCenter">
					Il n'y a pas d'école à afficher
				</p>
			</div>
			<?php
		}
		?>
	</article>
</section>