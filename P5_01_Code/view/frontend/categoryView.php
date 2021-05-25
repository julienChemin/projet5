<?php
$category = $data['categoryInfo']['category'];
$pinnedTopics = $data['categoryInfo']['pinnedTopics'];
$nonePinnedTopics = $data['categoryInfo']['nonePinnedTopics'];
$canCreateTopic = $data['canCreateTopic'];
?>

<section id="category">
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

    <h1><?=$category->getName()?></h1>

    <div class="container usefullLink">
        <p class="linkHomeForum">
            <a href="index.php?action=forum&school=<?=$data['school']->getName()?>">
                <i class="fas fa-door-open"></i>Retourner sur l'accueil du forum
            </a>
        </p>

        <?php
        if ($data['user']->getIsAdmin() || $data['user']->getIsModerator()) {
            echo '<p class="manageForumLink container"><a href="indexAdmin.php?action=manageForum&school=' . $data['school']->getName() . '"><i class="fas fa-pencil-alt"></i>   Gérer le forum</a></p>';
        }
        ?>
    </div>

    <?php
    if ($category->getDescription() !== null) {
        echo '<p class="categoryDescription">' . $category->getDescription() . '</p>';
    }
    ?>

    <article id="topics">
        <?php

        if ($canCreateTopic) {
            ?>
            <div id="blockAddNewTopic" class="container">
                <a class="addNewTopic" href="index.php?action=createTopic&categoryId=<?=$category->getId()?>">
                    <i class="fas fa-plus"></i>Créer un nouveau sujet
                </a>
            </div>
            <?php
        }

        if (!empty($pinnedTopics) && count($pinnedTopics) > 0) {
            foreach ($pinnedTopics as $topic) {
                ?>
                <a href="index.php?action=forumTopic&topicId=<?=$topic->getId()?>" class="topic container">
                    <h3><i class="fas fa-thumbtack"></i><?=$topic->getTitle()?></h3>
                    <p><?=$topic->getAuthorName()?>, <?=$topic->getDatePublication()?></p>
                </a>
                <?php
            }
        }

        if (!empty($nonePinnedTopics) && count($nonePinnedTopics) > 0) {
            foreach ($nonePinnedTopics as $topic) {
                ?>
                <a href="index.php?action=forumTopic&topicId=<?=$topic->getId()?>" class="topic container">
                    <h3><?=$topic->getTitle()?></h3>
                    <p><?=$topic->getAuthorName()?>, <?=$topic->getDatePublication()?></p>
                </a>
                <?php
            }
        } else if (empty($pinnedTopics) || (!empty($pinnedTopics) && count($pinnedTopics)) > 0) {
            // 0 topic to display
            ?>
            <div class="blockStyleOne container">
                <span>Il n'y a aucun sujet d'ouvert pour l'instant</span>
            </div>
            <?php
        }
        ?>
    </article>
</section>
