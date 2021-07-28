<?php
$cvOwner = $data['cvOwner'];
$cvOwnerSchool = $data['cvOwnerSchool'];
$cvInfo = $data['cvInfo'];
?>

<article id="editCv" class="defaultCv" ownerId="<?=$cvOwner->getId()?>">
    <aside id="menuEditCv">
        <div id="tabsEditCv" class="hide">
            <?php
            if ($cvInfo['sections'] && count($cvInfo['sections']) > 0) {
                foreach ($cvInfo['sections'] as $section) {
                    ?>
                    <div class="tabEditCv" idSection="<?=$section->getId()?>" style="order: <?=$section->getSectionOrder()?>00;">
                        <p><?=$section->getName()?></p>

                        <div class="editSectionOrder">
                            <i class="fas fa-chevron-up"></i>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>

                    <?php
                    echo '<div class="tabEditBlock belongToSection'.$section->getId().'" idSection="'.$section->getId().'" style="order: '.$section->getSectionOrder().'50;">';

                    if ($cvInfo['blocks'][$section->getId()] && count($cvInfo['blocks'][$section->getId()]) > 0) {
                        foreach ($cvInfo['blocks'][$section->getId()] as $block) {
                            ?>
                            <div style="order: <?=$block->getBlockOrder()?>;" idBlock="<?=$block->getId()?>">
                                <p>Bloc <?=$block->getBlockOrder()?></p>

                                <div class="editBlockOrder">
                                    <i class="fas fa-chevron-up"></i>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    echo '</div>';
                }
            }

            if ($cvInfo['sections'] && count($cvInfo['sections']) < 15) {
                ?>
                <p id="tabAddSection">
                    <i class="fas fa-plus"></i>
                </p>
                <?php
            }
            ?>
        </div>
        
        <div id="contentTabsEditCv">
            <?php
            if ($cvInfo['sections'] && count($cvInfo['sections']) > 0) {
                for ($i = 0; $i < count($cvInfo['sections']); $i++) {
                    $section = $cvInfo['sections'][$i];
                    ?>
                    <div class="contentTabEditCv hide">
                        <div class="blockEditSectionName">
                            <label class="orang">Nom de la section</label>
                            <input type="text" value="<?=$section->getName()?>">
                        </div>

                        <div class="blockEditBoolInNavbar">
                            <span class="orang">Barre de navigation</span>

                            <p>
                                <input type="checkbox" <?=$section->getLinkInNavbar() ? "checked" : ""?>>
                                <label>Afficher un lien vers cette section dans la barre de navigation</label>
                            </p>
                        </div>

                        <div class="blockEditBackground">
                            <div>
                                <span class="orang">Image de fond</span>

                                <p>
                                    <input type="checkbox" class="editBoolCover" <?=$section->getBackgroundCover() !== null ? "checked" : ""?>>
                                    <label>Utiliser une image de fond</label>
                                </p>

                                <form method="POST" enctype="multipart/form-data" class="<?=$section->getBackgroundCover() === null ? "hide" : ""?>">
                                    <p>
                                        <label for="uploadFile<?=$i?>">(max : 5Mo)</label>
                                        <input type="hidden" name="MAX_FILE_SIZE" value="6000000">
                                        <input type="file" name="uploadFile<?=$i?>" max-size="6000000" id="uploadFile<?=$i?>" accept=".jpeg, .jpg, .jfif, .png, .gif">
                                    </p>

                                    <figure class="preview <?=$section->getBackgroundCover() === null ? "hide" : ""?>">
                                        <img src="<?=$section->getBackgroundCover() !== null ? $section->getBackgroundCover() : ""?>" 
                                        title="preview" alt ="Aperçu">
                                    </figure>
                                </form>

                                <p class="editBoolParallax <?=$section->getBackgroundCover() === null ? "hide" : ""?>">
                                    <input type="checkbox" <?=$section->getBackgroundFixed() ? "checked" : ""?>>
                                    <label>Effet de parallaxe</label>
                                </p>
                            </div>
                        </div>

                        <div class="blockEditHeight">
                            <div>
                                <span class="orang">Hauteur de la section</span>

                                <p>
                                    <input type="checkbox" <?=$section->getHeightValue() === null ? "checked" : ""?>>
                                    <label>Auto</label>
                                </p>
                            </div>

                            <p>
                                <?php $heightValue = $section->getHeightValue() !== null ? explode('vh', $section->getHeightValue())[0] : '50' ?>
                                <label class="<?=$section->getHeightValue() !== null ? "" : "elemDisabled"?>"">Taille en pourcentage de l'écran</label>
                                <input type="range" min="5" max="100" step="5" value="<?=$heightValue?>" <?=$section->getHeightValue() !== null ? "" : "disabled"?>>
                            </p>
                        </div>

                        <div class="blockEditAlign">
                            <span class="orang">Alignement des blocs</span>

                            <p style="text-align:left;">Alignement horizontal</p>

                            <div class="blockEditHorizontalAlign">
                                <form>
                                    <label for="horizontalLeft<?=$i?>">
                                        <input type="radio" name="horizontalValue<?=$i?>" id="horizontalLeft<?=$i?>" value="left" 
                                        <?=$section->getHorizontalAlign() === 'left' ? "checked" : ""?>>
                                        <span>Gauche</span>
                                    </label>

                                    <label for="horizontalCenter<?=$i?>">
                                        <input type="radio" name="horizontalValue<?=$i?>" id="horizontalCenter<?=$i?>" value="center" 
                                        <?=$section->getHorizontalAlign() === 'center' ? "checked" : ""?>>
                                        <span>Centre</span>
                                    </label>

                                    <label for="horizontalRight<?=$i?>">
                                        <input type="radio" name="horizontalValue<?=$i?>" id="horizontalRight<?=$i?>" value="right" 
                                        <?=$section->getHorizontalAlign() === 'right' ? "checked" : ""?>>
                                        <span>Droite</span>
                                    </label>

                                    <label for="horizontalAround<?=$i?>">
                                        <input type="radio" name="horizontalValue<?=$i?>" id="horizontalAround<?=$i?>" value="around" 
                                        <?=$section->getHorizontalAlign() === 'around' ? "checked" : ""?>>
                                        <span>Espacé</span>
                                    </label>

                                    <label for="horizontalBetween<?=$i?>">
                                        <input type="radio" name="horizontalValue<?=$i?>" id="horizontalBetween<?=$i?>" value="between" 
                                        <?=$section->getHorizontalAlign() === 'between' ? "checked" : ""?>>
                                        <span>Au bord</span>
                                    </label>
                                </form>
                            </div>

                            <p style="text-align:left;">Alignement vertical</p>

                            <div class="blockEditVerticalAlign">
                                <form>
                                    <label for="verticalTop<?=$i?>">
                                        <input type="radio" name="verticalValue<?=$i?>" id="verticalTop<?=$i?>" value="top" 
                                        <?=$section->getVerticalAlign() === 'top' ? "checked" : ""?>>
                                        <span>Haut</span>
                                    </label>

                                    <label for="verticalCenter<?=$i?>">
                                        <input type="radio" name="verticalValue<?=$i?>" id="verticalCenter<?=$i?>" value="center" 
                                        <?=$section->getVerticalAlign() === 'center' ? "checked" : ""?>>
                                        <span>Centre</span>
                                    </label>

                                    <label for="verticalBottom<?=$i?>">
                                        <input type="radio" name="verticalValue<?=$i?>" id="verticalBottom<?=$i?>" value="bottom" 
                                        <?=$section->getVerticalAlign() === 'bottom' ? "checked" : ""?>>
                                        <span>Bas</span>
                                    </label>
                                </form>
                            </div>
                        </div>

                        <div class="blockSaveSection">
                            <button class="saveChange">Enregistrer les modifications</button>
                            <span class="saving orang hide">. . .</span>
                            <i class="fas fa-check saveSuccess green hide"></i>
                            <p class="saveFailure red hide">
                                Certaines modifications n'ont pas pu être enregistrées, réessayez ou rechargez la page
                            </p>
                        </div>

                        <?php
                        if ($cvInfo['blocks'][$section->getId()] && count($cvInfo['blocks'][$section->getId()]) < 6) {
                            ?>
                            <div class="blockAddNewBlock">
                                <button class="addNewBlock">Ajouter un nouveau bloc</button>
                            </div>
                            <?php
                        }
                        ?>
                        

                        <div class="blockDeleteSection">
                            <button class="deleteSection red">Supprimer la section</button>
                        </div>
                    </div>
                    <?php
                }
            }

            // content menu edit block
            if ($cvInfo['sections'] && count($cvInfo['sections']) > 0) {
                for ($i = 0; $i < count($cvInfo['sections']); $i++) {
                    $section = $cvInfo['sections'][$i];

                    if ($cvInfo['blocks'][$section->getId()] && count($cvInfo['blocks'][$section->getId()]) > 0) {
                        for ($j = 0; $j < count($cvInfo['blocks'][$section->getId()]); $j++) {
                            $block = $cvInfo['blocks'][$section->getId()][$j];
                            ?>
                            <div class="contentTabEditBlock hide">
                                <div class="blockEditBlockContent">
                                    <span class="orang">Contenu</span>
                                    <button idBlock="<?=$block->getId()?>">Modifier le contenu du bloc</button>
                                </div>

                                <div class="blockEditSize">
                                    <p>
                                        <?php 
                                        if ($block->getBlockSize() === 'large') {
                                            $blockSize = 3;
                                        } else if ($block->getBlockSize() === 'medium') {
                                            $blockSize = 2;
                                        } else {
                                            $blockSize = 1;
                                        }
                                        ?>

                                        <span class="orang">Taille du bloc</span>
                                        <input type="range" min="1" max="3" step="1" value="<?=$blockSize?>">
                                    </p>
                                </div>

                                <div>
                                    <span class="orang">Apparence du fond</span>

                                    <p class="editBackgroundOpacity">
                                        <label>Transparence</label>
                                        <input type="range" min="0" max="1" step="0.1" value="<?=$block->getBlockOpacity()?>">
                                    </p>

                                    <p class="editBackgroundColor">
                                        <label>Couleur</label>
                                        <input type="color">
                                    </p>
                                </div>

                                <div class="blockEditBackgroundBorder">
                                    <span class="orang">Bordure du bloc</span>

                                    <p class="editBorderWidth">
                                        <?php $blockWidth = !$block->getBlockBorderWidth() ? 0 : $block->getBlockBorderWidth() ?>
                                        <label>Taille</label>
                                        <input type="range" min="0" max="5" step="1" value="<?=$blockWidth?>">
                                    </p>

                                    <p class="editBorderRadius">
                                        <?php $blockRadius = !$block->getBlockBorderRadius() ? 0 : $block->getBlockBorderRadius() ?>
                                        <label>Arrondie des coins</label>
                                        <input type="range" min="0" max="25" step="1" value="<?=$blockRadius?>">
                                    </p>

                                    <p class="editBorderColor">
                                        <label>Couleur</label>
                                        <input type="color">
                                    </p>
                                </div>
        
                                <div class="blockSaveBlock">
                                    <button class="saveBlockChange">Enregistrer les modifications</button>
                                    <span class="savingBlock orang hide">. . .</span>
                                    <i class="fas fa-check saveBlockSuccess green hide"></i>
                                    <p class="saveBlockFailure red hide">
                                        Certaines modifications n'ont pas pu être enregistrées, réessayez ou rechargez la page
                                    </p>
                                </div>
        
                                <div class="blockDeleteBlock">
                                    <button class="deleteBlock red">Supprimer le bloc</button>
                                </div>
                            </div>
                            <?php
                        }
                    }
                }
            }
            ?>
        </div>
    </aside>

    <?php
    echo '<nav id="navbarCv">';
        echo '<ul class="container">';
            echo '<li>';
                echo '<div id="buttonToggleMenuEditCv">';
                    echo '<i class="fas fa-pencil-alt"></i>';
                echo '</div>';
            echo '</li>';
        foreach ($cvInfo['sections'] as $section) {
            $classesSection = $section->getLinkInNavbar() ? "" : "hide";

            echo '<li class="linkInNavbar ' . $classesSection . '"><a href="#anchor' . $section->getName() . '">' . $section->getName() . '</a></li>';
        }
        echo '</ul>';
    echo '</nav>';

    for ($i = 0; $i < count($cvInfo['sections']); $i++) {
        $section = $cvInfo['sections'][$i];

        if ($i === 0) {
            $sectionType = 'header';
        } else if ($i+1 === count($cvInfo['sections'])) {
            $sectionType = 'footer';
        } else {
            $sectionType = 'section';
        }
        ?>

        <<?=$sectionType?> id="anchor<?=$section->getName()?>" class="cvSection" style="<?=$section->getSectionStyle()?>">
            <div class="container <?=$section->getSectionClasses()?>" style="min-height:<?=$section->getHeightValue()?>;">
                <?php
                if ($cvInfo['blocks'][$section->getId()] && count($cvInfo['blocks'][$section->getId()]) > 0) {
                    foreach ($cvInfo['blocks'][$section->getId()] as $block) {
                        ?>
                        <div class="cvBlock <?=$block->getBlockClasses()?>" style="<?=$block->getBlockStyle()?>" backColor="<?=$block->getBlockBackgroundColor()?>" borderColor="<?=$block->getBlockBorderColor()?>" idBlock="<?=$block->getId()?>" idSection="<?=$section->getId()?>">
                            <?=$block->getContent()?>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </<?=$sectionType?>>

        <?php
    }
    ?>
</article>

<div id="modal">
    <div id="confirmDeleteSection">
        <p>
            Supprimer définitivement la section ainsi que tous son contenu ?
            <br><br>
            <span class="orang">la page va s'actualiser lors de la suppression</span>
        </p>

        <div>
            <span class="closeModal">Annuler</span>
            <button><a href="" id="linkDeleteInModal" title="Supprimer la section">Confirmer</a></button>
        </div>
    </div>

    <div id="confirmDeleteBlock">
        <p>
            Supprimer définitivement le bloc ainsi que tous son contenu ?
            <br><br>
            <span class="orang">la page va s'actualiser lors de la suppression</span>
        </p>

        <div>
            <span class="closeModal">Annuler</span>
            <button><a href="" id="linkDeleteBlockInModal" title="Supprimer le bloc">Confirmer</a></button>
        </div>
    </div>

    <form id="modalTinyMce" method="POST" action="">
        <textarea id="tinyMCEtextarea" name="tinyMCEtextarea"></textarea>

        <div>
            <span class="closeModal">Annuler</span>
            <input type="submit" value="Confirmer"></button>
        </div>
    </form>
</div>