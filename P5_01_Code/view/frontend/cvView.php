<?php
$cvOwner = $data['cvOwner'];
$cvOwnerSchool = $data['cvOwnerSchool'];
$cvInfo = $data['cvInfo'];
?>

<article id="cv" class="defaultCv">
    <?php
    echo '<nav id="navbarCv">';
        echo '<ul class="container">';
        if ($cvInfo['sections'] && count($cvInfo['sections']) > 0) {
            foreach ($cvInfo['sections'] as $section) {
                $classesSection = $section->getLinkInNavbar() ? "" : "hide";
    
                echo '<li class="' . $classesSection . '"><a href="#' . $section->getName() . '">' . $section->getName() . '</a></li>';
            }
        }
        echo '</ul>';

        if (!empty($_SESSION['id']) && ($_SESSION['id'] == $cvOwner->getId() || $_SESSION['school'] === ALL_SCHOOL)) {
            $infoLink = $_SESSION['school'] === ALL_SCHOOL ? "&idUser=" . $cvOwner->getId() : "";
            echo '<span class="container"><a href="index.php?action=editCv' . $infoLink . '">';
            echo '<i class="fas fa-pencil-alt"></i>';
            echo '   Cliquez ici pour éditer votre CV</a></span>';
        }
    echo '</nav>';

    if ($cvInfo['sections'] && count($cvInfo['sections']) > 0) {
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
                            <div class="cvBlock <?=$block->getBlockClasses()?>" style="<?=$block->getBlockStyle()?>">
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
    }
    ?>
</article>