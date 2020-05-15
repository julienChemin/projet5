<section id="blockModeratAdmin" class="container">
	<article id="moderatAdmin">
		<?php
		if (isset($data['users'])) {
			if ($_SESSION['school'] === ALL_SCHOOL) {
				if (!empty($data['schools'])) {
					if (isset($data['message'])) {	
						?>
						<div class="blockStyleOne blockMsg">
							<i class="fas fa-times orang"></i>
							<p class="msg orang"><?=$data['message']?></p>
						</div>
						<?php
					}
					//display all schools for webmaster
					foreach ($data['schools'] as $school) {
						?>
						<div class="blockSchool">
							<div class="<?=$school->getIsActive() ? "" : "inactiveSchool"?>">
								<div>
									<figure>
										<img src='<?=$school->getLogo()?>'>
									</figure>
								</div>
								<div>
									<h1>
										<a href="index.php?action=schoolProfile&school=<?=$school->getName()?>"><?=$school->getName()?></a>
									</h1>
								</div>
								<div>
									<i class="far fa-caret-square-down"></i>
									<i class="far fa-caret-square-up"></i>
								</div>
							</div>
							<div class="tableAdminModerator">
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
													<a href="index.php?action=userProfile&userId=<?=$user->getId()?>"><?=$user->getName()?></a>
												</td>
												<?php
												if ($user->getId() !== $school->getIdAdmin() && $user->getId() !== $_SESSION['id']) {
													echo '<td><i class="far fa-minus-square toModerator" schoolname="' . $school->getName() . '"></i></td>';
												} else {
													echo '<td><i class="far fa-minus-square inactifLink" schoolname="' . $school->getName() . '"></i></td>';
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
											<th>
												Supprimer
											</th>
										</tr>
										<?php
										foreach ($data['users'][$school->getName()] as $user) {
											if ($user->getIsModerator()) {
												?>
												<tr>
													<td>
														<a href="index.php?action=userProfile&userId=<?=$user->getId()?>"><?=$user->getName()?></a>
													</td>
													<td>
														<i class="far fa-plus-square toAdmin" schoolname="<?=$school->getName()?>"></i>
													</td>
													<td>
														<i class="far fa-minus-square toNormalUser" schoolname="<?=$school->getName()?>"></i>
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
									?>
									<div class="blockStyleOne fullWidth">
										<p class="msg orang textCenter">
											Aucun modérateur actuellement
										</p>
									</div>
									<?php
								}
								?>
								<form class="formAddModerator" method="POST" action="indexAdmin.php?action=moderatAdmin&amp;option=addModerator">	
									<p class="orang">Ajouter un modérateur >></p>
									<div>
										<hr>
										<h2>Information sur le nouveau modérateur</h2>
										<div>
											<p>
												<label for="moderatorName">Identifiant</label>
												<input type="text" name="moderatorName" required="">
											</p>
											<p>
												<label for="moderatorPassword">Mot de passe</label>
												<input type="password" name="moderatorPassword" required="">
											</p>
											<p>
												<label for="moderatorConfirmPassword">Confirmez le mot de passe</label>
												<input type="password" name="moderatorConfirmPassword" required="">
											</p>
											<p>
												<label for="moderatorMail">Adresse email</label>
												<input type="email" name="moderatorMail" required="">
											</p>
										</div>
										<div>
											<p>
												<input type="hidden" name="schoolName" value="<?=$school->getName()?>">
												<input type="submit" name="submit" value="Valider">
												<input type="button" name="cancel" value="Annuler">
											</p>
										</div>
										<hr>
									</div>
								</form>
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
			} elseif ($_SESSION['grade'] === ADMIN) {
				//display user's school
				$school = $data['schools'];
				if ($school->getName() === $_SESSION['school']) {
					if (isset($data['message'])) {	
						?>
						<div class="blockStyleOne blockMsg">
							<i class="fas fa-times orang"></i>
							<p class="msg orang"><?=$data['message']?></p>
						</div>
						<?php
					}
					?>
					<div class="blockSchool">
						<div class="<?=$school->getIsActive() ? "" : "inactiveSchool"?>">
							<div>
								<figure>
									<img src='<?=$school->getLogo()?>'>
								</figure>
							</div>
							<div>
								<h1>
									<a href="indexAdmin.php?action=schoolProfile&school=<?=$school->getName()?>"><?=$school->getName()?></a>
								</h1>
							</div>
						</div>
						<div class="tableAdminModerator">
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
												<a href="index.php?action=userProfile&userId=<?=$user->getId()?>"><?=$user->getName()?></a>
											</td>
											<?php
											if (($user->getId() !== $school->getIdAdmin() && $user->getId() !== $_SESSION['id'])) {
												echo '<td><i class="far fa-minus-square toModerator" schoolname="' . $school->getName() . '"></i></td>';
											} else {
												echo '<td><i class="far fa-minus-square inactifLink" schoolname="' . $school->getName() . '"></i></td>';
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
										<th>
											Supprimer
										</th>
									</tr>
									<?php
									foreach ($data['users'] as $user) {
										if ($user->getIsModerator()) {
											?>
											<tr>
												<td>
													<a href="index.php?action=userProfile&userId=<?=$user->getId()?>"><?=$user->getName()?></a>
												</td>
												<td>
													<i class="far fa-plus-square toAdmin" schoolname="<?=$school->getName()?>"></i>
												</td>
												<td>
													<i class="far fa-minus-square toNormalUser" schoolname="<?=$school->getName()?>"></i>
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
							<form class="formAddModerator" method="POST" action="indexAdmin.php?action=moderatAdmin&amp;option=addModerator">
								<p class="orang">Ajouter un modérateur >></p>
								<div>
									<hr>
									<h2>Information sur le nouveau modérateur</h2>
									<div>
										<p>
											<label for="moderatorName">Identifiant</label>
											<input type="text" name="moderatorName" required="">
										</p>
										<p>
											<label for="moderatorPassword">Mot de passe</label>
											<input type="password" name="moderatorPassword" required="">
										</p>
										<p>
											<label for="moderatorConfirmPassword">Confirmez le mot de passe</label>
											<input type="password" name="moderatorConfirmPassword" required="">
										</p>
										<p>
											<label for="moderatorMail">Adresse email</label>
											<input type="email" name="moderatorMail" required="">
										</p>
									</div>
									<div>
										<p>
											<input type="hidden" name="schoolName" value="<?=$school->getName()?>">
											<input type="submit" name="submit" value="Valider">
											<input type="button" name="cancel" value="Annuler">
										</p>
									</div>
									<hr>
								</div>
							</form>
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
