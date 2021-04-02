<section id="blockProfile">
    <?php
    if (!empty($_SESSION) && ($data['user']->getId() === $_SESSION['id'] || $_SESSION['school'] === ALL_SCHOOL)) {
        $authorizedUser = true;
    } else {
        $authorizedUser = false;
    }
    $data['user']->getSchool() === NO_SCHOOL ? $visibility = 'hide' : $visibility = '';
    ?>

    <?php $data['user']->getNoBanner() ? $backgroundImgAttribut = '' : $backgroundImgAttribut = "background-image: url('" . $data['user']->getProfileBanner() . "')"?>
    <div id="banner" class="editable" style="<?=$backgroundImgAttribut?>">
        <?php if ($authorizedUser) {
            echo '<i class="fas fa-pencil-alt iconeEdit iconeEditHeader" title="Editer la bannière"></i>';
        }
        ?>
    </div>
    <div id="colorFade"></div>

    <article id="profile" class="container">
        <header>
            <div class="<?=$data['user']->getProfilePictureSize()?> editable">
                <img src="<?=$data['user']->getProfilePicture()?>" alt="Photo de profil" class="<?=$data['user']->getProfilePictureOrientation()?>">
                <?php
                if ($authorizedUser) {
                    echo '<i class="fas fa-pencil-alt iconeEdit iconeEditHeader" title="Editer la photo de profil"></i>';
                }
                ?>
            </div>
            <div class="<?=$data['user']->getProfileTextBlock()?> editable">
                <span class="<?=$data['user']->getProfileTextPseudo()?>"><?=$data['user']->getFirstName()?> <?=$data['user']->getLastName()?></span>
                <a href="index.php?action=schoolProfile&school=<?=$data['user']->getSchool()?>" class="<?=$data['user']->getProfileTextSchool()?> <?=$visibility?>">
                    <?=$data['user']->getSchool()?>
                </a>
                <?php
                if ($authorizedUser) {
                    echo '<i class="fas fa-pencil-alt iconeEdit iconeEditHeader" title="Editer la position du texte"></i>';
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
                if (!empty($_SESSION)) {
                    echo '<ul id="blockTabsEditProfile">';
                        if ($authorizedUser) {
                            ?>

                            <li title="éditer le profil">
                                <i class="fas fa-pencil-alt iconeEdit"></i>
                            </li>

                            <?php
                            if ($_SESSION['school'] === ALL_SCHOOL) {
                                if ($data['user']->getIsBan()) {
                                    $class = 'alreadyBan';
                                    $titleForWarnIcone = 'Ce compte est suspendu';
                                } else {
                                    $class = '';
                                    $titleForWarnIcone = 'ajouter un avertissement à cet utilisateur';
                                }
                                ?>

                                <li title="<?=$titleForWarnIcone?>">
                                    <a href="indexAdmin.php?action=warnUser&idUser=<?=$data['user']->getId()?>">
                                        <i class="fas fa-exclamation-triangle iconeEdit <?=$class?>"></i>
                                    </a>
                                </li>

                                <?php
                            }
                        }

                        if ($_SESSION['id'] !== $data['user']->getId()) {
                            ?>

                            <li title="Signaler le profil">
                                <a href="index.php?action=report&elem=profile&id=<?=$data['user']->getId()?>">
                                    <i class="far fa-flag iconeEdit"></i>
                                </a>
                            </li>

                            <?php
                        }
                    echo '</ul>';
                }
                ?>
            </nav>
            <div id="slideTab">
                <div id="tabProfile" class="editable">
                    <?php
                    if ($authorizedUser && $data['user']->getIsActive()) {
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
                                        if ($authorizedUser && $data['user']->getIsActive()) {
                                            echo '<i class="fas fa-pencil-alt iconeEdit iconeEditProfile" atrsize="' . $profileContent->getSize() .  
                                            '" atralign="' . $profileContent->getAlign() . '"> N° ' . $profileContent->getContentOrder() . '</i>';
                                        }
                                        ?>
                                        <?=$profileContent->getContent()?>
                                    </div>
                                    <span class="hide"><?=$profileContent->getId()?></span>
                                    <?php
                                    if (!empty($profileContent->getAlign())) {
                                        echo '</div>';
                                    }
                                }
                            }
                        } elseif ($authorizedUser && $data['user']->getIsActive()) {
                            ?>
                            <div class="blockContentProfile blockTuto big">
                                <p>
                                    Cliquez sur  <i class="fas fa-pencil-alt orang"></i>  pour éditer votre profil. Vous pouvez aussi consulter la <a href="index.php?action=faq">F.A.Q</a> pour plus d'informations
                                </p>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div id="tabPublication" class="editable noHeight">
                    <?php
                    if ($authorizedUser && $data['user']->getIsActive()) {
                        echo '<a href="index.php?action=addPost"><i class="far fa-plus-square iconeEdit"></i></a>';
                    }
                    ?>
                    <div></div>
                </div>
                <div id="tabAbout" class="editable noHeight">
                    <?php
                    if ($authorizedUser && $data['user']->getIsActive()) {
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
                                        if ($authorizedUser && $data['user']->getIsActive()) {
                                            echo '<i class="fas fa-pencil-alt iconeEdit iconeEditAbout" atrsize="' . $profileContent->getSize() .  
                                            '" atralign="' . $profileContent->getAlign() . '"> N° ' . $profileContent->getContentOrder() . '</i>';
                                        }
                                        ?>
                                        <?=$profileContent->getContent()?>
                                    </div>
                                    <span class="hide"><?=$profileContent->getId()?></span>
                                    <?php
                                    if (!empty($profileContent->getAlign())) {
                                        echo '</div>';
                                    }
                                }
                            }
                        } elseif ($authorizedUser && $data['user']->getIsActive()) {
                            ?>
                            <div class="blockContentAbout blockTuto big">
                                <p>
                                    Cliquez sur  <i class="fas fa-pencil-alt orang"></i>  pour éditer votre profil. Vous pouvez aussi consulter la <a href="index.php?action=faq">F.A.Q</a> pour plus d'informations
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
if ($authorizedUser) {
    ?>
    <div id="modal">
        <!-- Banniere -->
        <form id="contentMenuEditBanner" class="contentMenuEdit menuEditHeader" method="POST" 
        action="index.php?action=upload&elem=banner&noBanner=<?=$data['user']->getNoBanner() ? 'true' : 'false'?>&userId=<?=$data['user']->getId()?>" enctype="multipart/form-data">
            <input type="hidden" name="userId" value="<?=$data['user']->getId()?>">
            <p>
                <label for="dlBanner">Télécharger une image (max : 5Mo) : </label>
                <input type="hidden" name="MAX_FILE_SIZE" value="6000000">
                <input type="file" name="dlBanner" id="dlBanner" accept="image/*">
            </p>
            <p>- ou -</p>
            <p>
                <span>
                    <input type="checkbox" name="noBanner" id="noBanner" <?=$data['user']->getNoBanner() === true ? 'checked' : 'unchecked'?>>
                    <label for="noBanner">Pas d'image de bannière</label>
                </span>
            </p>
            <hr>
            <p>
                <button name="saveBanner" id="saveBanner">
                    <i class="fas fa-check"></i>
                </button>
            </p>
        </form>
        <!-- Profile picture -->
        <form id="contentMenuEditProfilePicture" class="contentMenuEdit menuEditHeader" method="POST" 
        action="index.php?action=upload&elem=picture&orientation=<?=$data['user']->getProfilePictureOrientation()?>&size=<?=$data['user']->getProfilePictureSize()?>&userId=<?=$data['user']->getId()?>" 
        enctype="multipart/form-data">
            <p>
                <label for="dlPicture">Télécharger une image (max : 5Mo): </label>
                <input type="hidden" name="MAX_FILE_SIZE" value="6000000">
                <input type="file" name="dlPicture" id="dlPicture" accept="image/*">
            </p>
            <hr>
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
            <hr>
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
            <p>
                <button name="saveProfilePicture" id="saveProfilePicture">
                    <i class="fas fa-check"></i>
                </button>
            </p>
        </form>
        <!-- Text position -->
        <form id="contentMenuEditText" class="contentMenuEdit menuEditHeader">
            <div>
                <input type="radio" name="blockTextPosition" id="blockTextTop"
                value="elemStart" <?=$data['user']->getProfileTextBlock() === 'elemStart' ? 'checked' : 'unchecked'?>>
                <label for="blockTextTop">
                    <img src="public/images/blockTextTop.jpg" title="Texte aligné en haut" alt="Texte aligné en haut">
                </label>

                <input type="radio" name="blockTextPosition" id="blockTextCenter"
                value="elemCenter" <?=$data['user']->getProfileTextBlock() === 'elemCenter' ? 'checked' : 'unchecked'?>>
                <label for="blockTextCenter">
                    <img src="public/images/blockTextCenter.jpg" title="Texte centré verticalement" alt="Texte centré verticalement">
                </label>

                <input type="radio" name="blockTextPosition" id="blockTextBottom"
                value="elemEnd" <?=$data['user']->getProfileTextBlock() === 'elemEnd' ? 'checked' : 'unchecked'?>>
                <label for="blockTextBottom">
                    <img src="public/images/blockTextBottom.jpg" title="Texte aligné en bas" alt="Texte aligné en bas">
                </label>
            </div>
            <hr>
            <div>
                <input type="radio" name="pseudoPosition" id="pseudoLeft"
                value="elemStart" <?=$data['user']->getProfileTextPseudo() === 'elemStart' ? 'checked' : 'unchecked'?>>
                <label for="pseudoLeft">
                    <img src="public/images/pseudoLeft.jpg" title="Pseudo aligné à gauche" alt="Pseudo aligné à gauche">
                </label>

                <input type="radio" name="pseudoPosition" id="pseudoCenter"
                value="elemCenter" <?=$data['user']->getProfileTextPseudo() === 'elemCenter' ? 'checked' : 'unchecked'?>>
                <label for="pseudoCenter">
                    <img src="public/images/pseudoCenter.jpg" title="Pseudo centré" alt="Pseudo centré">
                </label>

                <input type="radio" name="pseudoPosition" id="pseudoRight"
                value="elemEnd" <?=$data['user']->getProfileTextPseudo() === 'elemEnd' ? 'checked' : 'unchecked'?>>
                <label for="pseudoRight">
                    <img src="public/images/pseudoRight.jpg" title="Pseudo aligné à droite" alt="Pseudo aligné à droite">
                </label>
            </div>
            <hr class="<?=$visibility?>">
            <div class="<?=$visibility?>">
                <input type="radio" name="schoolPosition" id="schoolLeft"
                value="elemStart" <?=$data['user']->getProfileTextSchool() === 'elemStart' ? 'checked' : 'unchecked'?>>
                <label for="schoolLeft">
                    <img src="public/images/schoolLeft.jpg" title="École aligné à gauche" alt="École aligné à gauche">
                </label>

                <input type="radio" name="schoolPosition" id="schoolCenter"
                value="elemCenter" <?=$data['user']->getProfileTextSchool() === 'elemCenter' ? 'checked' : 'unchecked'?>>
                <label for="schoolCenter">
                    <img src="public/images/schoolCenter.jpg" title="École centré" alt="École centré">
                </label>

                <input type="radio" name="schoolPosition" id="schoolRight"
                value="elemEnd" <?=$data['user']->getProfileTextSchool() === 'elemEnd' ? 'checked' : 'unchecked'?>>
                <label for="schoolRight">
                    <img src="public/images/schoolRight.jpg" title="École aligné à droite" alt="École aligné à droite">
                </label>
            </div>
            <hr>
            <div>
                <button name="saveProfileText" id="saveProfileText">
                    <i class="fas fa-check"></i>
                </button>
            </div>
        </form>
        <?php
        if ($data['user']->getIsActive()) {
            ?>
            <!-- modal on top to edit block profile content -->
            <form id="contentMenuEditBlock" class="contentMenuEdit">
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
                    <i title="Supprimer" class="fas fa-trash"></i>
                </div>
            </form>
            <!-- text area for editing content of block profile content -->
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
                    <input type="hidden" name="idProfileContent" value="">

                    <input type="submit" name="submit" value="Valider">
                    <input type="button" name="cancel" value="Annuler">
                </div>
            </form>
            <?php
        }
        ?>
    </div>
    <?php
}
?>
