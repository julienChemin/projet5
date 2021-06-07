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

    <?php
    if ($category->getDescription() !== null) {
        echo '<div class="categoryDescription container">' . $category->getDescription() . '</div>';
    }
    ?>

    <div class="container usefullLink">
        <p class="linkHomeForum">
            <a href="index.php?action=forum&school=<?=$data['school']->getName()?>">
                <i class="fas fa-door-open"></i>Accueil du forum
            </a>
        </p>

        <?php
        if ($data['user']->getIsAdmin() || $data['user']->getIsModerator()) {
            echo '<p class="manageForumLink container"><a href="indexAdmin.php?action=manageForum&school=' . $data['school']->getName() . '"><i class="fas fa-pencil-alt"></i>   Gérer le forum</a></p>';
        }
        ?>
    </div>

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
            echo '<div class="container topics">';
            foreach ($pinnedTopics as $topic) {
                ?>
                <a href="index.php?action=forumTopic&topicId=<?=$topic->getId()?>" class="topic pin">
                    <h3><i class="fas fa-thumbtack"></i><?=$topic->getTitle()?></h3>
                    <p><?=$topic->getAuthorName()?>, <?=$topic->getDatePublication()?></p>
                </a>
                <?php
            }
            echo '</div>';
        }

        if (!empty($nonePinnedTopics) && count($nonePinnedTopics) > 0) {
            echo '<div class="container topics">';
                foreach ($nonePinnedTopics as $topic) {
                    ?>
                    <a href="index.php?action=forumTopic&topicId=<?=$topic->getId()?>" class="topic">
                        <h3><?=$topic->getTitle()?></h3>
                        <p><?=$topic->getAuthorName()?>, <?=$topic->getDatePublication()?></p>
                    </a>
                    <?php
                }
            echo '</div>';

            //display paging
            echo '<nav id="paging" class="fullWidth container">';
                echo '<ol>';
                !empty($_GET['page']) ? $actualPage = intval($_GET['page']) : $actualPage = 1;

                if ($data['nbPage'] > 9) {
                    if ($actualPage < 9) {
                        //display the 9 first page and the last one
                        for ($i=0; $i<9; $i++) {
                            $offset = $i*$data['nbElemByPage'];

                            if ($i+1 === $actualPage) {
                                echo '<li class="actualPage">';
                            } else {
                                echo '<li>';
                            }

                            echo '<a href="index.php?action=category&categoryId=' . $category->getId() . '&page=' . ($i+1) . '&offset=' . $offset . '">' . ($i+1) . '</a></li>';
                        }

                        if ($data['nbPage'] > 10) {
                            echo '<li>...</li>';
                        }

                        echo '<li><a href="index.php?action=category&categoryId=' . $category->getId() . '&page=' . $data['nbPage'] . '&offset=' . ($data['nbPage']-1)*$data['nbElemByPage'] . '">' . $data['nbPage'] . '</a></li>';
                    } else {
                        //first page
                        echo '<li><a href="index.php?action=category&categoryId=' . $category->getId() . '&page=1&offset=0">1</a></li>';
                        echo '<li>...</li>';

                        //2 page before actual
                        echo '<li><a href="index.php?action=category&categoryId=' . $category->getId() . '&page=' . ($actualPage-2) . '&offset=' . ($actualPage-3)*$data['nbElemByPage'] . '">' . ($actualPage-2) . '</a></li>';
                        echo '<li><a href="index.php?action=category&categoryId=' . $category->getId() . '&page=' . ($actualPage-1) . '&offset=' . ($actualPage-2)*$data['nbElemByPage'] . '">' . ($actualPage-1) . '</a></li>';

                        //actual page
                        echo '<li class="actualPage"><a href="index.php?action=category&categoryId=' . $category->getId() . '&page=' . ($actualPage) . '&offset=' . ($actualPage-1)*$data['nbElemByPage'] . '">' . ($actualPage) . '</a></li>';

                        //2 page after actual
                        if ($actualPage+1 < $data['nbPage']) {
                            echo '<li><a href="index.php?action=category&categoryId=' . $category->getId() . '&page=' . ($actualPage+1) . '&offset=' . ($actualPage)*$data['nbElemByPage'] . '">' . ($actualPage+1) . '</a></li>';
                        }

                        if ($actualPage+2 < $data['nbPage']) {
                            echo '<li><a href="index.php?action=category&categoryId=' . $category->getId() . '&page=' . ($actualPage+2) . '&offset=' . ($actualPage+1)*$data['nbElemByPage'] . '">' . ($actualPage+2) . '</a></li>';
                        }

                        //last page
                        if ($actualPage+2 < $data['nbPage']-1) {
                            echo '<li>...</li>';
                        }

                        if ($actualPage < $data['nbPage']) {
                            echo '<li><a href="index.php?action=category&categoryId=' . $category->getId() . '&page=' . $data['nbPage'] . '&offset=' . ($data['nbPage']-1)*$data['nbElemByPage'] . '">' . $data['nbPage'] . '</a></li>';
                        }
                    }
                } else {
                    for ($i=0; $i<$data['nbPage']; $i++) {
                        $offset = $i*$data['nbElemByPage'];

                        if ($i+1 === $actualPage) {
                            echo '<li class="actualPage">';
                        } else {
                            echo '<li>';
                        }

                        echo '<a href="index.php?action=category&categoryId=' . $category->getId() . '&page=' . ($i+1) . '&offset=' . $offset . '">' . ($i+1) . '</a></li>';
                    }
                }

                echo '</ol>';
            echo '</nav>';
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
