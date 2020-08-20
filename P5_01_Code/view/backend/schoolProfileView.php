<section id="blockProfile">
    <!-- editing menus -->
    <div id="blockMenuEditingTop">
        <div id="contentMenuEditBanner" class="contentMenuEdit menuEditHeader">
            <form method="POST" action="indexAdmin.php?action=upload&elem=banner&noBanner=<?=$data['school']->getNoBanner() ? 'true' : 'false'?>" enctype="multipart/form-data">
                <input type="hidden" name="school" value="<?=$data['school']->getName()?>">
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
                        <input type="checkbox" name="noBanner" id="noBanner" <?=$data['school']->getNoBanner() === true ? 'checked' : 'unchecked'?>>
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
            <form method="POST" 
            action="indexAdmin.php?action=upload&elem=picture&orientation=<?=$data['school']->getProfilePictureOrientation()?>&size=<?=$data['school']->getProfilePictureSize()?>"
            enctype="multipart/form-data">
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
                        value="widePicture" <?=$data['school']->getProfilePictureOrientation() === 'widePicture' ? 'checked' : 'unchecked'?>>
                        <label for="widePicture">Image large</label>
                    </span>
                    <span>
                        <input type="radio" name="pictureOrientation" id="highPicture" 
                        value="highPicture" <?=$data['school']->getProfilePictureOrientation() === 'highPicture' ? 'checked' : 'unchecked'?>>
                        <label for="highPicture">Image haute</label>
                    </span>
                </p>
                <hr class="hrNavbar">
                <p>
                    <span>Taille de l'image</span>
                    <span>
                        <input type="radio" name="pictureSize" id="smallPicture" 
                        value="smallPicture" <?=$data['school']->getProfilePictureSize() === 'smallPicture' ? 'checked' : 'unchecked'?>>
                        <label for="smallPicture">Petite</label>

                        <input type="radio" name="pictureSize" id="mediumPicture" 
                        value="mediumPicture" <?=$data['school']->getProfilePictureSize() === 'mediumPicture' ? 'checked' : 'unchecked'?>>
                        <label for="mediumPicture">Moyenne</label>

                        <input type="radio" name="pictureSize" id="bigPicture" 
                        value="bigPicture" <?=$data['school']->getProfilePictureSize() === 'bigPicture' ? 'checked' : 'unchecked'?>>
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
                    value="elemStart" <?=$data['school']->getProfileTextBlock() === 'elemStart' ? 'checked' : 'unchecked'?>>
                    <label for="blockTextTop">
                        <img src="public/images/blockTextTop.jpg">
                    </label>

                    <input type="radio" name="blockTextPosition" id="blockTextCenter"
                    value="elemCenter" <?=$data['school']->getProfileTextBlock() === 'elemCenter' ? 'checked' : 'unchecked'?>>
                    <label for="blockTextCenter">
                        <img src="public/images/blockTextCenter.jpg">
                    </label>
                
                    <input type="radio" name="blockTextPosition" id="blockTextBottom"
                    value="elemEnd" <?=$data['school']->getProfileTextBlock() === 'elemEnd' ? 'checked' : 'unchecked'?>>
                    <label for="blockTextBottom">
                        <img src="public/images/blockTextBottom.jpg">
                    </label>
                </div>
                <hr class="hrNavbar">
                <div>
                    <input type="radio" name="schoolPosition" id="schoolLeft"
                    value="elemStart" <?=$data['school']->getProfileTextSchool() === 'elemStart' ? 'checked' : 'unchecked'?>>
                    <label for="schoolLeft">
                        <img src="public/images/schoolLeft.jpg">
                    </label>

                    <input type="radio" name="schoolPosition" id="schoolCenter"
                    value="elemCenter" <?=$data['school']->getProfileTextSchool() === 'elemCenter' ? 'checked' : 'unchecked'?>>
                    <label for="schoolCenter">
                        <img src="public/images/schoolCenter.jpg">
                    </label>
                
                    <input type="radio" name="schoolPosition" id="schoolRight"
                    value="elemEnd" <?=$data['school']->getProfileTextSchool() === 'elemEnd' ? 'checked' : 'unchecked'?>>
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
                <p id="blockNewsListOrder">
                    <label for="newsContentOrder">Bloc numéro :</label>
                    <select name="newsContentOrder" id="newsContentOrder">
                        <?php
                        if (count($data['profileContent']) !== 0) {
                            $j=0;
                            for ($i=0; $i<count($data['profileContent']); $i++) {
                                if ($data['profileContent'][$i]->getTab() === 'news') {
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
                    <i title="Supprimer" class="fas fa-times"></i>
                    <hr class="hrNavbar">
                </div>
            </form>
        </div>
    </div>
    <div id="banner" class="editable">
        <?php
        $data['school']->getNoBanner() ? $classForBanner = 'hide' : $classForBanner = "";
        echo '<img class="' . $classForBanner . '" src="' . $data['school']->getProfileBanner() . '" alt="banner picture">';
        echo '<i class="fas fa-pencil-alt iconeEdit iconeEditHeader" title="Editer la bannière"></i>';
        ?>
    </div>
    <div id="colorFade"></div>
    <article id="profile" class="container">
        <header>
            <div class="<?=$data['school']->getProfilePictureSize()?> editable">
                <img src="<?=$data['school']->getProfilePicture()?>" alt="profile picture" class="<?=$data['school']->getProfilePictureOrientation()?>">
                <i class="fas fa-pencil-alt iconeEdit iconeEditHeader" title="Editer la photo de profil"></i>
            </div>
            <div class="<?=$data['school']->getProfileTextBlock()?> editable">
                <span class="<?=$data['school']->getProfileTextSchool()?>"><?=$data['school']->getName()?></span>
                <i class="fas fa-pencil-alt iconeEdit iconeEditHeader" title="Editer la position du texte"></i>
            </div>
        </header>
        <div>
            <nav>
                <ul id="blockTabs" class="tabsStyleOne">
                    <li class="buttonIsFocus">Profil</li>
                    <li>Actualité</li>
                    <li>Publication</li>
                    <li>Publication Privée</li>
                    <li>À propos</li>
                </ul>
                <ul id="blockTabsEditProfile">
                    <li title="éditer le profil">
                        <i class="fas fa-pencil-alt iconeEdit"></i>
                    </li>
                </ul>
            </nav>
            <div id="slideTab">
                <div id="tabProfile" class="editable">
                    <i class="far fa-plus-square iconeEdit"></i>
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
                                        <i class="fas fa-pencil-alt iconeEdit iconeEditProfile" atrsize="<?=$profileContent->getSize()?>" 
                                            atralign="<?=$profileContent->getAlign()?>"> N° <?=$profileContent->getContentOrder()?></i>
                                        <?=$profileContent->getContent()?>  
                                    </div>
                                    <span class="hide"><?=$profileContent->getId()?></span>
                                    <?php
                                    if (!empty($profileContent->getAlign())) {
                                        echo '</div>';
                                    }
                                }
                            }
                        } else {
                            ?>
                            <div class="blockContentProfile blockTuto big">
                                <p>
                                    Cliquez sur  <i class="fas fa-pencil-alt orang"></i>  pour éditer votre profil. Vous pouvez aussi consulter 
                                    la <a href="index.php?action=Faq">F.A.Q</a> pour plus d'informations
                                </p>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div id="tabNews" class="editable">
                    <i class="far fa-plus-square iconeEdit"></i>
                    <div>
                        <?php
                        if (!empty($data['profileContent'])) {
                            foreach ($data['profileContent'] as $profileContent) {
                                if ($profileContent->getTab() === 'news') {
                                    if (!empty($profileContent->getAlign())) {
                                        echo '<div class="contentAloneInRow" style="order:' . $profileContent->getContentOrder() . '">';
                                    }
                                    ?>
                                    <div class="blockContentNews editable <?=$profileContent->getSize()?> <?=$profileContent->getAlign()?>" style="order:<?=$profileContent->getContentOrder()?>">
                                        <i class="fas fa-pencil-alt iconeEdit iconeEditNews" atrsize="<?=$profileContent->getSize()?>" 
                                            atralign="<?=$profileContent->getAlign()?>"> N° <?=$profileContent->getContentOrder()?></i>
                                        <?=$profileContent->getContent()?>
                                    </div>
                                    <span class="hide"><?=$profileContent->getId()?></span>
                                    <?php
                                    if (!empty($profileContent->getAlign())) {
                                        echo '</div>';
                                    }
                                }
                            }
                        } else {
                            ?>
                            <div class="blockContentNews blockTuto big">
                                <p>
                                    Cliquez sur  <i class="fas fa-pencil-alt orang"></i>  pour éditer votre profil. Vous pouvez aussi consulter 
                                    la <a href="index.php?action=Faq">F.A.Q</a> pour plus d'informations
                                </p>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div id="tabPublication" class="editable">
                    <a href="indexAdmin.php?action=addSchoolPost"><i class="far fa-plus-square iconeEdit"></i></a>
                    <div></div>
                </div>
                <div id="tabPrivatePublication" class="editable">
                    <a href="indexAdmin.php?action=addSchoolPost"><i class="far fa-plus-square iconeEdit"></i></a>
                    <div></div>
                </div>
                <div id="tabAbout" class="editable">
                    <i class="far fa-plus-square iconeEdit"></i>
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
                                        <i class="fas fa-pencil-alt iconeEdit iconeEditAbout" atrsize="<?=$profileContent->getSize()?>" 
                                            atralign="<?=$profileContent->getAlign()?>"> N° <?=$profileContent->getContentOrder()?></i>
                                        <?=$profileContent->getContent()?>
                                    </div>
                                    <span class="hide"><?=$profileContent->getId()?></span>
                                    <?php
                                    if (!empty($profileContent->getAlign())) {
                                        echo '</div>';
                                    }
                                }
                            }
                        } else {
                            ?>
                            <div class="blockContentAbout blockTuto big">
                                <p>
                                    Cliquez sur  <i class="fas fa-pencil-alt orang"></i>  pour éditer votre profil. Vous pouvez aussi consulter 
                                    la <a href="index.php?action=Faq">F.A.Q</a> pour plus d'informations
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
<div id="modal">
    <form class="container" method="POST" action="indexAdmin.php?action=updateProfile&elem=content&school=<?=$data['school']->getName()?>">
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
            <input type="hidden" name="idProfileContent" value="">

            <input type="submit" name="submit" value="Valider">
            <input type="button" name="cancel" value="Annuler">
        </div>
    </form>
</div>
