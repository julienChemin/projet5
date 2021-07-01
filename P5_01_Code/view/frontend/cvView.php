<?php
$cvOwner = $data['cvOwner'];
$cvOwnerSchool = $data['cvOwnerSchool'];
$cvInfo = $data['cvInfo'];
?>

<article id="cv" class="defaultCv">
    <?php
    $classesNavbar = $cvInfo['info']->getDisplayNavbar() ? "" : "hide ";

    echo '<nav id="navbarCv" class="' . $classesNavbar . ' hideUnder600Width">';
        echo '<ul class="container">';
        foreach ($cvInfo['sections'] as $section) {
            $classesSection = $section->getLinkInNavbar() ? "" : "hide";

            echo '<li class="' . $classesSection . '"><a href="#' . $section->getName() . '">' . $section->getName() . '</a></li>';
        }
        echo '</ul>';

        echo '<span class="container"><a href="index.php?action=editCv">Cliquez ici pour éditer votre CV</a></span>';
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

            <<?=$sectionType?> id="<?=$section->getName()?>" style="<?=$section->getSectionStyle()?>">
                <div class="container <?=$section->getSectionClasses()?>">
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