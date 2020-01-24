<section id="blockModeratUsers" class="container">
	<article id="moderatUsers">
		<?php
		if (isset($data['users'])) {
			if ($_SESSION['school'] === ALL_SCHOOL) {
				if (!empty($data['schools'])) {
					//display all schools for webmaster
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
									<h1>
										<?=$school->getName()?>
									</h1>
								</div>
							</div>

							<div>
								<?php
								if ($data['isActive'][$school->getName()]['active']) {
									//display active account
									?>
									<table>
										<caption>Compte(s) actif(s)</caption>
										<tr>
											<th>
												Identifiant
											</th>

											<th>
												Passer en modérateur
											</th>

											<th>
												Désactiver le compte
											</th>

											<th>
												Supprimer le compte
											</th>
										</tr>

										<?php
										foreach ($data['users'][$school->getName()]['active'] as $user) {
											?>
											<tr>
												<td>
													<?=$user->getName()?>
												</td>

												<td>
													<i class="fas fa-user-shield toModerator" schoolname="<?=$school->getName()?>" ></i>
												</td>

												<td>
													<i class="fas fa-user-times toInactive" schoolname="<?=$school->getName()?>" ></i>
												</td>

												<td>
													<i class="fas fa-times toDelete" schoolname="<?=$school->getName()?>" ></i>
												</td>
											</tr>
											<?php
										}
										?>
									</table>
									<?php
								} else {
									// there is no active account to display
									?>
									<div class="blockStyleOne fullWidth">
										<p class="msg orang textCenter">
											Il n'y a aucun compte actif à afficher
										</p>
									</div>
									<?php
								}

								if ($data['isActive'][$school->getName()]['inactive']) {
									//display inactive account
									?>
									<table>
										<caption>Compte(s) inactif(s)</caption>
										<tr>
											<th>
												Identifiant
											</th>

											<th>
												Activer le compte
											</th>

											<th>
												Supprimer le compte
											</th>
										</tr>

										<?php
										foreach ($data['users'][$school->getName()]['inactive'] as $user) {
											?>
											<tr>
												<td>
													<?=$user->getName()?>
												</td>

												<td>
													<i class="fas fa-user-plus toActive" schoolname="<?=$school->getName()?>" ></i>
												</td>

												<td>
													<i class="fas fa-times toDelete" schoolname="<?=$school->getName()?>" ></i>
												</td>
											</tr>
											<?php
										}
										?>
									</table>
									<?php
								} else {
									// there is no inactive account to display
									?>
									<div class="blockStyleOne fullWidth">
										<p class="msg orang textCenter">
											Il n'y a aucun compte inactif à afficher
										</p>
									</div>
									<?php
								}
								?>
							</div>
						</div>
					<?php
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
			} elseif ($_SESSION['grade'] === ADMIN || $_SESSION['grade'] === MODERATOR) {
				//display user's school
				$school = $data['schools'];
				if ($school->getName() === $_SESSION['school']) {
					?>
					<div class="blockSchool">
						<div>
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
							<?php
							if ($data['isActive']['active']) {
								//display active account
								?>
								<table>
									<caption>Compte(s) actif(s)</caption>
									<tr>
										<th>
											Identifiant
										</th>

										<th>
											Passer en modérateur
										</th>

										<th>
											Désactiver le compte
										</th>

										<th>
											Supprimer le compte
										</th>
									</tr>

									<?php
									foreach ($data['users'] as $user) {
										if ($user->getIsActive()) {
											?>
											<tr>
												<td>
													<?=$user->getName()?>
												</td>

												<td>
													<i class="fas fa-user-shield toModerator" schoolname="<?=$school->getName()?>" ></i>
												</td>

												<td>
													<i class="fas fa-user-times toInactive" schoolname="<?=$school->getName()?>" ></i>
												</td>

												<td>
													<i class="fas fa-times toDelete" schoolname="<?=$school->getName()?>" ></i>
												</td>
											</tr>
											<?php
										}
									}
									?>
								</table>
								<?php
							} else {
								// there is no active account to display
								?>
								<div class="blockStyleOne fullWidth">
									<p class="msg orang textCenter">
										Il n'y a aucun compte actif à afficher
									</p>
								</div>
								<?php
							}

							if ($data['isActive']['inactive']) {
								//display inactive account
								?>
								<table>
									<caption>Compte(s) inactif(s)</caption>
									<tr>
										<th>
											Identifiant
										</th>

										<th>
											Activer le compte
										</th>

										<th>
											Supprimer le compte
										</th>
									</tr>

									<?php
									foreach ($data['users'] as $user) {
										if (!$user->getIsActive()) {
											?>
											<tr>
												<td>
													<?=$user->getName()?>
												</td>

												<td>
													<i class="fas fa-user-plus toActive" schoolname="<?=$school->getName()?>" ></i>
												</td>

												<td>
													<i class="fas fa-times toDelete" schoolname="<?=$school->getName()?>" ></i>
												</td>
											</tr>
											<?php
										}
									}
									?>
								</table>
								<?php
							} else {
								// there is no inactive account to display
								?>
								<div class="blockStyleOne fullWidth">
									<p class="msg orang textCenter">
										Il n'y a aucun compte inactif à afficher
									</p>
								</div>
								<?php
							}
							?>
						</div>
					</div>
				<?php
				}
			}
		}
		?>
		<div id="modal">
			<div>
				<p></p>
				<div>
					<a href="#">
						<input type="button" name="ok" value="Valider">
					</a>
					<input type="button" name="cancel" value="Annuler">
				</div>
			</div>
		</div>
	</article>
</section>
