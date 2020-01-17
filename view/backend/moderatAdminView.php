<section id="blockModeratAdmin" class="container">
	<article id="moderatAdmin">
		<?php
		if (isset($data['users'])) {
			if ($_SESSION['school'] === 'allSchool') {
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
								<table> 
									<tr>
										<th>
											Administrateur(s)
										</th>

										<th>
											Passer en modérateur
										</th>
									</tr>

									<?php
									foreach ($data['users'][$school->getName()] as $user) {
										if ($user->getIsAdmin()) {
											?>
											<tr>
												<td>
													<?=$user->getName()?>
												</td>

												<?php
												if ($user->getId() !== $school->getIdAdmin() && $user->getId() !== $_SESSION['id']) {
													?>
													<td>
														<i class="far fa-minus-square toModerator" schoolname="<?=$school->getName()?>"></i>
													</td>
													<?php
												} else {
													?>
													<td>
														<i class="far fa-minus-square inactifLink" schoolname="<?=$school->getName()?>"></i>
													</td>
													<?php
												}
												?>
											</tr>
											<?php
										}
									}
									?>
								</table>

								<?php
								if ($data['nbModerator'][$school->getName()] > 0) {
									?>
									<table> 
										<tr>
											<th>
												Modérateur(s)
											</th>

											<th>
												Passer en administrateur
											</th>

											<th>
												Enlever les droits de modérateur
											</th>
										</tr>

										<?php
										foreach ($data['users'][$school->getName()] as $user) {
											if ($user->getIsModerator()) {
												?>
												<tr>
													<td>
														<?=$user->getName()?>
													</td>

													<td>
														<i class="far fa-plus-square toAdmin" schoolname="<?=$school->getName()?>"></i>
													</td>

													<td>
														<i class="far fa-minus-square toNormalUser" schoolname="<?=$school->getName()?>"></i>
													</td>
												</tr>
												<?php
											}
										}
										?>
									</table>
								<?php
								} else {
									?>
									<div class="blockStyleOne fullWidth">
										<p class="msg orang textCenter">
											Aucun modérateur actuellement
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
			} elseif ($_SESSION['grade'] === 'admin') {
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
							<table> 
								<tr>
									<th>
										Administrateur(s)
									</th>

									<th>
										Passer en modérateur
									</th>
								</tr>

								<?php
								//display all admins
								foreach ($data['users'] as $user) {
									if ($user->getIsAdmin()) {
										?>
										<tr>
											<td>
												<?=$user->getName()?>
											</td>

											<?php
											if (($user->getId() !== $school->getIdAdmin() && $user->getId() !== $_SESSION['id'])) {
												?>
												<td>
													<i class="far fa-minus-square toModerator" schoolname="<?=$school->getName()?>"></i>
												</td>
												<?php
											} else {
												?>
												<td>
													<i class="far fa-minus-square inactifLink" schoolname="<?=$school->getName()?>"></i>
												</td>
												<?php
											}
											?>
										</tr>
										<?php
									}
								}
								?>
							</table>

							<?php
							if ($data['nbModerator'] > 0) {
								//display all moderator
								?>
								<table> 
									<tr>
										<th>
											Modérateur(s)
										</th>

										<th>
											Passer en administrateur
										</th>

										<th>
											Enlever les droits de modérateur
										</th>
									</tr>

									<?php
									foreach ($data['users'] as $user) {
										if ($user->getIsModerator()) {
											?>
											<tr>
												<td>
													<?=$user->getName()?>
												</td>

												<td>
													<i class="far fa-plus-square toAdmin" schoolname="<?=$school->getName()?>"></i>
												</td>

												<td>
													<i class="far fa-minus-square toNormalUser" schoolname="<?=$school->getName()?>"></i>
												</td>
											</tr>
											<?php
										}
									}
									?>
								</table>
								<?php
							} else {
								// there is no moderator to display
								?>
								<div class="blockStyleOne fullWidth">
									<p class="msg orang textCenter">
										Aucun modérateur actuellement
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