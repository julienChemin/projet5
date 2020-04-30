<section id="blockProfile">
	<div id="banner">
		<?php
		if ($data['school']->getNoBanner()) {
			echo '<img class="hide" src="' . $data['school']->getProfileBanner() . '" alt="banner picture">';
		} else {
			echo '<img src="' . $data['school']->getProfileBanner() . '" alt="banner picture">';
		}
		?>
	</div>
	<div id="colorFade"></div>

	<article id="profile" class="container">
		<header>
			<div class="<?=$data['school']->getProfilePictureSize()?>">
				<?php
				if ($data['school']->getProfilePicture() === 'public/images/question-mark.png') {
					echo '<img src="' . $data["school"]->getLogo() . '" alt="profile picture" class="' . $data["school"]->getProfilePictureOrientation() . '">';
				} else {
					echo '<img src="' . $data["school"]->getProfilePicture() . '" alt="profile picture" class="' . $data["school"]->getProfilePictureOrientation() . '">';
				}
				?>
			</div>

			<div class="<?=$data['school']->getProfileTextBlock()?>">
				<span class="<?=$data['school']->getProfileTextSchool()?>"><?=$data['school']->getName()?></span>
			</div>
		</header>

		<div>
			<nav>
				<ul id="blockTabs" class="tabsStyleOne">
					<li class="buttonIsFocus">Profil</li>
					<li>Publication</li>
					<?php
					if ($data['school']->getName() === $_SESSION['school']) {
						echo '<li>Publication privée</li>';
					}
					?>
					<li>À propos</li>
				</ul>
			</nav>

			<div id="slideTab">
				<div id="tabProfile">
					<div>
						<?php
						if (!empty($data['profileContent'])) {
							foreach ($data['profileContent'] as $profileContent) {
								if ($profileContent->getTab() === 'profile') {
									if (!empty($profileContent->getAlign())) {
										echo '<div class="contentAloneInRow" style="order:' . $profileContent->getContentOrder() . '">';
									}
									?>

									<div class="blockContentProfile <?=$profileContent->getSize()?> <?=$profileContent->getAlign()?>" style="order:<?=$profileContent->getContentOrder()?>">
										<?=$profileContent->getContent()?>
									</div>

									<?php
									if (!empty($profileContent->getAlign())) {
										echo '</div>';
									}
								}
							}
						}
						?>
					</div>
				</div>

				<div id="tabPublication">
					<div></div>
				</div>

				<?php
				if ($data['school']->getName() === $_SESSION['school']) {
					?>
					<div id="tabPrivatePublication">
						<div></div>
					</div>
					<?php
				}
				?>

				<div id="tabAbout">
					<div>
						<?php
						if (!empty($data['profileContent'])) {
							foreach ($data['profileContent'] as $profileContent) {
								if ($profileContent->getTab() === 'about') {
									if (!empty($profileContent->getAlign())) {
										echo '<div class="contentAloneInRow" style="order:' . $profileContent->getContentOrder() . '">';
									}
									?>

									<div class="blockContentAbout <?=$profileContent->getSize()?> <?=$profileContent->getAlign()?>" style="order:<?=$profileContent->getContentOrder()?>">
										<?=$profileContent->getContent()?>
									</div>

									<?php
									if (!empty($profileContent->getAlign())) {
										echo '</div>';
									}
								}
							}
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</article>
</section>
