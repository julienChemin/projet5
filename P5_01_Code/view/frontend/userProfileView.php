<section id="blockUserProfile">
	<?php
	if ($data['user']->getId() === $_SESSION['id']) {
		//editing menus
		?>
		<div id="blockMenuEditingTop">
			<div id="contentMenuEditBanner" class="contentMenuEdit menuEditHeader">
				<form method="POST" action="index.php?action=upload&elem=banner&noBanner=<?=$data['user']->getNoBanner() ? 'true' : 'false'?>" enctype="multipart/form-data">
					<input type="hidden" name="userId" value="<?=$data['user']->getId()?>">
					<p>
						<label for="bannerPath">Adresse de l'image</label>
						<input type="text" name="bannerPath" id="bannerPath">
					</p>

					<p>- ou -</p>

					<p>
						<label for="dlBanner">Télécharger une image (max : 2Mo) : </label>
						<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
						<input type="file" name="dlBanner" id="dlBanner">
					</p>

					<p>- ou -</p>

					<p>
						<span>
							<?php
							if ($data['user']->getNoBanner()) {
								echo '<input type="checkbox" name="noBanner" id="noBanner" checked>';
							} else {
								echo '<input type="checkbox" name="noBanner" id="noBanner">';
							}
							?>
							<label for="noBanner">Pas d'image de bannière</label>
						</span>
					</p>

					<hr class="hrNavbar">

					<p>
						<button name="saveBanner" id="saveBanner">
							<i class="fas fa-check"></i>
						</button>
					</p>
				</form>
			</div>

			<div id="contentMenuEditProfilePicture" class="contentMenuEdit menuEditHeader">
				<form method="POST" action="index.php?action=upload&elem=picture&orientation=<?=$data['user']->getProfilePictureOrientation()?>
				&size=<?=$data['user']->getProfilePictureSize()?>" enctype="multipart/form-data">
					<p>
						<label for="picturePath">Adresse de l'image</label>
						<input type="text" name="picturePath" id="picturePath">
					</p>

					<p>- ou -</p>

					<p>
						<label for="dlPicture">Télécharger une image (max : 2Mo): </label>
						<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
						<input type="file" name="dlPicture" id="dlPicture">
					</p>

					<hr class="hrNavbar">

					<p>
						<span>
							<input type="radio" name="pictureOrientation" id="widePicture" 
							value="widePicture" <?=$data['user']->getProfilePictureOrientation() === 'widePicture' ? 'checked' : 'unchecked'?>>
							<label for="widePicture">Image large</label>
						</span>

						<span>
							<input type="radio" name="pictureOrientation" id="highPicture" 
							value="highPicture" <?=$data['user']->getProfilePictureOrientation() === 'highPicture' ? 'checked' : 'unchecked'?>>
							<label for="highPicture">Image haute</label>
						</span>
					</p>

					<hr class="hrNavbar">

					<p>
						<span>Taille de l'image</span>

						<span>
							<input type="radio" name="pictureSize" id="smallPicture" 
							value="smallPicture" <?=$data['user']->getProfilePictureSize() === 'smallPicture' ? 'checked' : 'unchecked'?>>
							<label for="smallPicture">Petite</label>
							<input type="radio" name="pictureSize" id="mediumPicture" 
							value="mediumPicture" <?=$data['user']->getProfilePictureSize() === 'mediumPicture' ? 'checked' : 'unchecked'?>>
							<label for="mediumPicture">Moyenne</label>
							<input type="radio" name="pictureSize" id="bigPicture" 
							value="bigPicture" <?=$data['user']->getProfilePictureSize() === 'bigPicture' ? 'checked' : 'unchecked'?>>
							<label for="bigPicture">Grande</label>
						</span>
					</p>

					<hr class="hrNavbar">

					<p>
						<button name="saveProfilePicture" id="saveProfilePicture">
							<i class="fas fa-check"></i>
						</button>
					</p>
				</form>
			</div>

			<div id="contentMenuEditText" class="contentMenuEdit menuEditHeader">
				<form>
					<div>
						<input type="radio" name="blockTextPosition" id="blockTextTop"
						value="elemStart" <?=$data['user']->getProfileTextBlock() === 'elemStart' ? 'checked' : 'unchecked'?>>
						<label for="blockTextTop">
							<img src="public/images/blockTextTop.jpg">
						</label>

						<input type="radio" name="blockTextPosition" id="blockTextCenter"
						value="elemCenter" <?=$data['user']->getProfileTextBlock() === 'elemCenter' ? 'checked' : 'unchecked'?>>
						<label for="blockTextCenter">
							<img src="public/images/blockTextCenter.jpg">
						</label>
					
						<input type="radio" name="blockTextPosition" id="blockTextBottom"
						value="elemEnd" <?=$data['user']->getProfileTextBlock() === 'elemEnd' ? 'checked' : 'unchecked'?>>
						<label for="blockTextBottom">
							<img src="public/images/blockTextBottom.jpg">
						</label>
					</div>

					<hr class="hrNavbar">

					<div>
						<input type="radio" name="pseudoPosition" id="pseudoLeft"
						value="elemStart" <?=$data['user']->getProfileTextPseudo() === 'elemStart' ? 'checked' : 'unchecked'?>>
						<label for="pseudoLeft">
							<img src="public/images/pseudoLeft.jpg">
						</label>

						<input type="radio" name="pseudoPosition" id="pseudoCenter"
						value="elemCenter" <?=$data['user']->getProfileTextPseudo() === 'elemCenter' ? 'checked' : 'unchecked'?>>
						<label for="pseudoCenter">
							<img src="public/images/pseudoCenter.jpg">
						</label>
					
						<input type="radio" name="pseudoPosition" id="pseudoRight"
						value="elemEnd" <?=$data['user']->getProfileTextPseudo() === 'elemEnd' ? 'checked' : 'unchecked'?>>
						<label for="pseudoRight">
							<img src="public/images/pseudoRight.jpg">
						</label>
					</div>

					<hr class="hrNavbar">

					<div>
						<input type="radio" name="schoolPosition" id="schoolLeft"
						value="elemStart" <?=$data['user']->getProfileTextSchool() === 'elemStart' ? 'checked' : 'unchecked'?>>
						<label for="schoolLeft">
							<img src="public/images/schoolLeft.jpg">
						</label>

						<input type="radio" name="schoolPosition" id="schoolCenter"
						value="elemCenter" <?=$data['user']->getProfileTextSchool() === 'elemCenter' ? 'checked' : 'unchecked'?>>
						<label for="schoolCenter">
							<img src="public/images/schoolCenter.jpg">
						</label>
					
						<input type="radio" name="schoolPosition" id="schoolRight"
						value="elemEnd" <?=$data['user']->getProfileTextSchool() === 'elemEnd' ? 'checked' : 'unchecked'?>>
						<label for="schoolRight">
							<img src="public/images/schoolRight.jpg">
						</label>
					</div>

					<hr class="hrNavbar">

					<div>
						<button name="saveProfileText" id="saveProfileText">
							<i class="fas fa-check"></i>
						</button>
					</div>
				</form>
			</div>

			<div id="contentMenuEditBlock" class="contentMenuEdit">
				<form>
					<div>
						<p>Largeur</p>

						<div>
							<input type="radio" name="blockSize" id="blockSmall"
							value="small">
							<label for="blockSmall">Petit</label>

							<input type="radio" name="blockSize" id="blockMedium"
							value="medium">
							<label for="blockMedium">Moyen</label>
						
							<input type="radio" name="blockSize" id="blockBig"
							value="big">
							<label for="blockBig">Grand</label>
						</div>
					</div>

					<hr class="hrNavbar">

					<p id="blockProfileListOrder">
						<label for="profileContentOrder">Bloc numéro :</label>
						<select name="profileContentOrder" id="profileContentOrder">
							<?php
							if (count($data['profileContent']) !== 0) {
								$j=0;
								for ($i=0; $i<count($data['profileContent']); $i++) {
									if ($data['profileContent'][$i]->getTab() === 'profile') {
										$j+=1;
										echo '<option value = ' . $j  . '>' . $j . '</option>';
									}
								}
							}
							?>
						</select>
					</p>

					<p id="blockAboutListOrder">
						<label for="aboutContentOrder">Bloc numéro :</label>
						<select name="aboutContentOrder" id="aboutContentOrder">
							<?php
							if (count($data['profileContent']) !== 0) {
								$j=0;
								for ($i=0; $i<count($data['profileContent']); $i++) {
									if ($data['profileContent'][$i]->getTab() === 'about') {
										$j+=1;
										echo '<option value = ' . $j  . '>' . $j . '</option>';
									}
								}
							}
							?>
						</select>
					</p>

					<hr class="hrNavbar">

					<p>
						<label for="align">Bloc seul sur sa ligne</label>
						<input type="checkbox" name="align" id="align">
					</p>

					<div>
						<p>Alignement</p>

						<div>
							<input type="radio" name="alignSide" id="alignLeft"
							value="elemStart">
							<label for="alignLeft">Gauche</label>

							<input type="radio" name="alignSide" id="alignCenter"
							value="elemCenter">
							<label for="alignCenter">Centre</label>
						
							<input type="radio" name="alignSide" id="alignRight"
							value="elemEnd">
							<label for="alignRight">Droite</label>
						</div>
					</div>

					<div id="blockToDelete">
						<hr class="hrNavbar">
						<i class="fas fa-times"></i>
						<hr class="hrNavbar">
					</div>
				</form>
			</div>

			<div id="contentMenuEditPublication" class="contentMenuEdit">
				publi
			</div>

			<div id="contentMenuEditAbout" class="contentMenuEdit">
				about
			</div>
		</div>
		<?php
	}
	?>

	<div id="banner" class="editable">
		<?php
		if ($data['user']->getNoBanner()) {
			echo '<img class="hide" src="' . $data['user']->getProfileBanner() . '" alt="banner picture">';
		} else {
			echo '<img src="' . $data['user']->getProfileBanner() . '" alt="banner picture">';
		}

		if ($data['user']->getId() === $_SESSION['id']) {
			echo '<i class="fas fa-pencil-alt iconeEdit iconeEditHeader"></i>';
		}
		?>
	</div>
	<div id="colorFade"></div>

	<article id="userProfile" class="container">
		<header>
			<div class="<?=$data['user']->getProfilePictureSize()?> editable">
				<img src="<?=$data['user']->getProfilePicture()?>" alt="profile picture" class="<?=$data['user']->getProfilePictureOrientation()?>">
				<?php
				if ($data['user']->getId() === $_SESSION['id']) {
					echo '<i class="fas fa-pencil-alt iconeEdit iconeEditHeader"></i>';
				}
				?>
			</div>

			<div class="<?=$data['user']->getProfileTextBlock()?> editable">
				<span class="<?=$data['user']->getProfileTextPseudo()?>"><?=$data['user']->getName()?></span>
				<a href="index.php?action=schoolProfile&school=<?=$data['user']->getSchool()?>" class="<?=$data['user']->getProfileTextSchool()?>">
					<?=$data['user']->getSchool()?> - groupe
				</a>
				<?php
				if ($data['user']->getId() === $_SESSION['id']) {
					echo '<i class="fas fa-pencil-alt iconeEdit iconeEditHeader"></i>';
				}
				?>
			</div>
		</header>

		<div>
			<nav>
				<ul id="blockTabs" class="tabsStyleOne">
					<li class="buttonIsFocus">Profil</li>
					<li>Publication</li>
					<li>À propos</li>
				</ul>

				<?php
				if ($data['user']->getId() === $_SESSION['id']) {
					?>
					<ul id="blockTabsEditProfile">
						<li title="éditer le profil">
							<i class="fas fa-pencil-alt iconeEdit"></i>
						</li>
					</ul>
					<?php
				}
				?>
			</nav>

			<div id="slideTab">
				<div id="tabProfile" class="editable">
					<?php
					if ($data['user']->getId() === $_SESSION['id']) {
						echo '<i class="far fa-plus-square iconeEdit"></i>';
					}
					?>

					<div>
						<?php
						if (!empty($data['profileContent'])) {
							foreach ($data['profileContent'] as $profileContent) {
								if ($profileContent->getTab() === 'profile') {
									if (!empty($profileContent->getAlign())) {
										echo '<div class="contentAloneInRow" style="order:' . $profileContent->getContentOrder() . '">';
									}
									?>
									<div class="blockContentProfile editable <?=$profileContent->getSize()?> <?=$profileContent->getAlign()?>" style="order:<?=$profileContent->getContentOrder()?>">
										<?php
										if ($data['user']->getId() === $_SESSION['id']) {
											echo '<i class="fas fa-pencil-alt iconeEdit iconeEditProfile" atrsize="' . $profileContent->getSize() .  
											'" atralign="' . $profileContent->getAlign() . '"> N° ' . $profileContent->getContentOrder() . '</i>';
										}
										?>
										<?=$profileContent->getContent()?>
									</div>
									<?php
									if (!empty($profileContent->getAlign())) {
										echo '</div>';
									}
								}
							}
						} elseif ($data['user']->getId() === $_SESSION['id']) {
							?>
							<div class="blockContentProfile blockTuto big">
								<p>
									Cliquez sur  <i class="fas fa-pencil-alt orang"></i>  pour éditer votre profil. Vous pouvez aussi consulter la <a href="index.php?action=Faq">F.A.Q</a> pour plus d'informations
								</p>
							</div>
							<?php
						}
						?>
					</div>
				</div>

				<div id="tabPublication" class="editable">
					<?php
					if ($data['user']->getId() === $_SESSION['id']) {
						echo '<a href="index.php?action=addPost"><i class="far fa-plus-square iconeEdit"></i></a>';
					}
					?>

					<div>
						
					</div>
				</div>

				<div id="tabAbout" class="editable">
					<?php
					if ($data['user']->getId() === $_SESSION['id']) {
						echo '<i class="far fa-plus-square iconeEdit"></i>';
					}
					?>

					<div>
						<?php
						if (!empty($data['profileContent'])) {
							foreach ($data['profileContent'] as $profileContent) {
								if ($profileContent->getTab() === 'about') {
									if (!empty($profileContent->getAlign())) {
										echo '<div class="contentAloneInRow" style="order:' . $profileContent->getContentOrder() . '">';
									}
									?>
									<div class="blockContentAbout editable <?=$profileContent->getSize()?> <?=$profileContent->getAlign()?>" style="order:<?=$profileContent->getContentOrder()?>">
										<?php
										if ($data['user']->getId() === $_SESSION['id']) {
											echo '<i class="fas fa-pencil-alt iconeEdit iconeEditAbout" atrsize="' . $profileContent->getSize() .  
											'" atralign="' . $profileContent->getAlign() . '"> N° ' . $profileContent->getContentOrder() . '</i>';
										}
										?>
										<?=$profileContent->getContent()?>
									</div>
									<?php
									if (!empty($profileContent->getAlign())) {
										echo '</div>';
									}
								}
							}
						} elseif ($data['user']->getId() === $_SESSION['id']) {
							?>
							<div class="blockContentAbout blockTuto big">
								<p>
									Cliquez sur  <i class="fas fa-pencil-alt orang"></i>  pour éditer votre profil. Vous pouvez aussi consulter la <a href="index.php?action=Faq">F.A.Q</a> pour plus d'informations
								</p>
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</article>
</section>

<?php
if ($data['user']->getId() === $_SESSION['id']) {
	?>
	<div id="modal">
		<form class="container" method="POST" action="index.php?action=updateProfile&elem=content&userId=<?=$data['user']->getId()?>">
			<div id="warningBeforeDelete">
				<p>Êtes-vous sûr de vouloir supprimer ce bloc?</p>
			</div>

			<div>
				<textarea id="tinyMCEtextarea" name="tinyMCEtextarea"></textarea>
			</div>

			<div class="modalButtons">
				<input type="hidden" name="type" value="">
				<input type="hidden" name="blockOrderValue" value="">
				<input type="hidden" name="newOrderValue" value="">
				<input type="hidden" name="sizeValue" value="small">
				<input type="hidden" name="alignValue" value="">
				<input type="hidden" name="deleteBlock" value="">

				<input type="submit" name="submit" value="Valider">
				<input type="button" name="cancel" value="Annuler">
			</div>
		</form>
	</div>
	<?php
}
?>
