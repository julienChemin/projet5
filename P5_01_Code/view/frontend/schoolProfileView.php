<section id="blockProfile">
    <?php
        if ($data['school']->getNoBanner()) {
            $backgroundImgAttribut = '';
            $classNoBanner = "noBanner";
        } else {
            $backgroundImgAttribut = "background-image: url('" . $data['school']->getProfileBanner() . "')";
            $classNoBanner = "";
        }
    ?>
    <div id="banner" class="<?=$classNoBanner?>" style="<?=$backgroundImgAttribut?>"></div>
    <div id="colorFade"></div>

    <article id="profile" class="container">
        <header>
            <?php $data['school']->getProfilePicture() === 'public/images/question-mark.png' ? $PictureSrc = $data["school"]->getLogo() : $PictureSrc = $data["school"]->getProfilePicture()?>
            <div id="profilePicture" class="<?=$data['school']->getProfilePictureSize()?>" style="background-image: url('<?=$PictureSrc?>')"></div>
            <div class="<?=$data['school']->getProfileTextBlock()?>">
                <span class="<?=$data['school']->getProfileTextSchool()?>"><?=$data['school']->getName()?></span>
            </div>
        </header>
        <?php
        if (!$data['school']->getIsActive()) {
            echo '<div class="fullWidth inactiveSchool">' . $data['contractInfo'] . '</div>';
        }
        ?>
        <div>
            <nav>
                <ul id="blockTabs" class="tabsStyleOne">
                    <li class="buttonIsFocus">Profil</li>
                    <li>Actualité</li>
                    <li>Publication</li>
                    <?php
                    if (!empty($_SESSION) && (($data['school']->getName() === $_SESSION['school'] && $data['userIsActive']) || $_SESSION['school'] === ALL_SCHOOL)) {
                        echo '<li>Publication privée</li>';
                    }
                    ?>
                    <li>À propos</li>
                </ul>
            </nav>
            <div id="slideTab">
                <div id="tabProfile" class="noHeight">
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
                <div id="tabNews" class="noHeight">
                    <div>
                        <?php
                        if (!empty($data['profileContent'])) {
                            foreach ($data['profileContent'] as $profileContent) {
                                if ($profileContent->getTab() === 'news') {
                                    if (!empty($profileContent->getAlign())) {
                                        echo '<div class="contentAloneInRow" style="order:' . $profileContent->getContentOrder() . '">';
                                    }
                                    ?>
                                    <div class="blockContentNews <?=$profileContent->getSize()?> <?=$profileContent->getAlign()?>" style="order:<?=$profileContent->getContentOrder()?>">
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
                <div id="tabPublication" class="noHeight">
                    <div></div>
                </div>
                <?php
                if (!empty($_SESSION) && (($data['school']->getName() === $_SESSION['school'] && $data['userIsActive']) || $_SESSION['school'] === ALL_SCHOOL)) {
                    ?>
                    <div id="tabPrivatePublication" class="noHeight">
                        <div></div>
                    </div>
                    <?php
                }
                ?>
                <div id="tabAbout" class="noHeight">
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
