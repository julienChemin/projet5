<section id="blockUserProfile">
	<?php
	if ($data['user']->getId() === $_SESSION['id']) {
		//editing menus
		?>
		<div id="blockMenuEditingTop">
			<div id="contentMenuEditBanner" class="contentMenuEdit">
				<form method="POST" action="index.php?action=upload&elem=banner" enctype="multipart/form-data">
					<input type="hidden" name="userId" value="<?=$data['user']->getId()?>">
					<p>
						<label for="bannerPath">Adresse de l'image</label>
						<input type="text" name="bannerPath" id="bannerPath">
					</p>

					<p>- ou -</p>

					<p>
						<label for="dlBanner">Télécharger une image : </label>
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

			<div id="contentMenuEditProfilePicture" class="contentMenuEdit">
				<form>
					<p>
						<label for="picturePath">Adresse de l'image</label>
						<input type="text" name="picturePath" id="picturePath">
					</p>

					<p>- ou -</p>

					<p>
						<label for="dlPicture">Télécharger une image : </label>
						<input type="file" name="dlPicture" id="dlPicture">
					</p>

					<hr class="hrNavbar">

					<p>
						<span>
							<input type="radio" name="pictureOrientation" id="widePicture">
							<label for="widePicture">Image large</label>
						</span>

						<span>
							<input type="radio" name="pictureOrientation" id="highPicture">
							<label for="highPicture">Image haute</label>
						</span>
					</p>

					<hr class="hrNavbar">

					<p>
						<span>Taille de l'image</span>

						<span>
							<input type="radio" name="pictureSize" id="smallPicture">
							<label for="smallPicture">Petite</label>
							<input type="radio" name="pictureSize" id="mediumPicture">
							<label for="mediumPicture">Moyenne</label>
							<input type="radio" name="pictureSize" id="bigPicture">
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

			<div id="contentMenuEditPseudo" class="contentMenuEdit">
				<form>
					<div>
						<input type="radio" name="blockPseudoPosition" id="blockPseudoTop">
						<label for="blockPseudoTop">
							<img src="public/images/blockPseudoTop.jpg">
						</label>

						<input type="radio" name="blockPseudoPosition" id="blockPseudoCenter">
						<label for="blockPseudoCenter">
							<img src="public/images/blockPseudoCenter.jpg">
						</label>
					
						<input type="radio" name="blockPseudoPosition" id="blockPseudoBottom">
						<label for="blockPseudoBottom">
							<img src="public/images/blockPseudoBottom.jpg">
						</label>
					</div>

					<hr class="hrNavbar">

					<div>
						<input type="radio" name="pseudoPosition" id="pseudoLeft">
						<label for="pseudoLeft">
							<img src="public/images/pseudoLeft.jpg">
						</label>

						<input type="radio" name="pseudoPosition" id="pseudoCenter">
						<label for="pseudoCenter">
							<img src="public/images/pseudoCenter.jpg">
						</label>
					
						<input type="radio" name="pseudoPosition" id="pseudoRight">
						<label for="pseudoRight">
							<img src="public/images/pseudoRight.jpg">
						</label>
					</div>

					<hr class="hrNavbar">

					<div>
						<input type="radio" name="schoolPosition" id="schoolLeft">
						<label for="schoolLeft">
							<img src="public/images/schoolLeft.jpg">
						</label>

						<input type="radio" name="schoolPosition" id="schoolCenter">
						<label for="schoolCenter">
							<img src="public/images/schoolCenter.jpg">
						</label>
					
						<input type="radio" name="schoolPosition" id="schoolRight">
						<label for="schoolRight">
							<img src="public/images/schoolRight.jpg">
						</label>
					</div>

					<hr class="hrNavbar">

					<div>
						<button name="savePseudo" id="savePseudo">
							<i class="fas fa-check"></i>
						</button>
					</div>
				</form>
			</div>

			<div id="contentMenuEditProfile" class="contentMenuEdit">
				profil
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
			echo '<i class="fas fa-pencil-alt iconeEdit"></i>';
		}
		?>
	</div>
	<div id="colorFade"></div>

	<article id="userProfile" class="container">
		<header>
			<div class="mediumSquarePicture editable">
				<img src="<?=$data['user']->getProfilePicture()?>" alt="profile picture" class="widePicture">
				<?php
				if ($data['user']->getId() === $_SESSION['id']) {
					echo '<i class="fas fa-pencil-alt iconeEdit"></i>';
				}
				?>
			</div>

			<div class="elemCenter editable">
				<span><?=$data['user']->getName()?></span>
				<span><?=$data['user']->getSchool()?> - groupe</span>
				<?php
				if ($data['user']->getId() === $_SESSION['id']) {
					echo '<i class="fas fa-pencil-alt iconeEdit"></i>';
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
					du contenu profil
					<?php
					if ($data['user']->getId() === $_SESSION['id']) {
						echo '<i class="fas fa-pencil-alt iconeEdit"></i>';
					}
					?>
				</div>

				<div id="tabPublication" class="editable">
					du contenu publier
					<?php
					if ($data['user']->getId() === $_SESSION['id']) {
						echo '<i class="fas fa-pencil-alt iconeEdit"></i>';
					}
					?>
				</div>

				<div id="tabAbout" class="editable">
					du contenu a propos
					<?php
					if ($data['user']->getId() === $_SESSION['id']) {
						echo '<i class="fas fa-pencil-alt iconeEdit"></i>';
					}
					?>
				</div>
			</div>
		</div>
	</article>
</section>