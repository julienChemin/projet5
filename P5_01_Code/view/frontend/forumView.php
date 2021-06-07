<?php
$categories = $data['forumInfo']['categories'];
$pinnedTopics = $data['forumInfo']['pinnedTopics'];
$nonePinnedTopics = $data['forumInfo']['nonePinnedTopics'];
?>

<section id="forum">
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

    <h1>Forum <?=$data['school']->getName()?></h1>

    <?php
    if ($data['user']->getIsAdmin() || $data['user']->getIsModerator()) {
        echo '<p class="manageForumLink container"><a href="indexAdmin.php?action=manageForum&school=' . $data['school']->getName() . '"><i class="fas fa-pencil-alt"></i>   Gérer le forum</a></p>';
    }
    
    if ($categories && count($categories) > 0) {
        echo '<h2 class="container">Accès rapide</h2>';

        echo '<div id="categoryFastAccess" class="container">';
        foreach ($categories as $category) {
            $anchor = "anchorCategory" . $category->getId();
            echo '<p><a href="#' . $anchor . '">' . $category->getName() . '</a></p>';
        }
        echo'</div>';
    }
    ?>
    <article id="forumCategories">
        <?php
        if ($categories && count($categories) > 0) {
            // display categories
            foreach ($categories as $category) {
                $anchor = "anchorCategory" . $category->getId();
                ?>
                <section id="<?=$anchor?>" class="forumCategory container">
                    <header>
                        <h2>
                            <a href="index.php?action=category&categoryId=<?=$category->getId()?>"><?=$category->getName()?></a>
                        </h2>

                        <?php
                        if ($category->getDescription() !== null) {
                            echo '<p class="categoryDescription">' . $category->getDescription() . '</p>';
                        }
                        ?>
                    </header>

                    <div class="topics">
                        <?php
                        if (!empty($pinnedTopics[$category->getName()]) && count($pinnedTopics[$category->getName()]) > 0) {
                            foreach ($pinnedTopics[$category->getName()] as $topic) {
                                ?>
                                <a href="index.php?action=forumTopic&topicId=<?=$topic->getId()?>" class="topic pin">
                                    <h3><i class="fas fa-thumbtack"></i><?=$topic->getTitle()?></h3>
                                    <p><?=$topic->getAuthorName()?>, <?=$topic->getDatePublication()?></p>
                                </a>
                                <?php
                            }
                        }

                        if (!empty($nonePinnedTopics[$category->getName()]) && count($nonePinnedTopics[$category->getName()]) > 0) {
                            foreach ($nonePinnedTopics[$category->getName()] as $topic) {
                                ?>
                                <a href="index.php?action=forumTopic&topicId=<?=$topic->getId()?>" class="topic">
                                    <h3><?=$topic->getTitle()?></h3>
                                    <p><?=$topic->getAuthorName()?>, <?=$topic->getDatePublication()?></p>
                                </a>
                                <?php
                            }

                            echo "<p><a href='index.php?action=category&categoryId=" . $category->getId() . "'>Voir tous les sujets dans la categorie : " . $category->getName() . "</a></p>";
                        } else if (empty($pinnedTopics[$category->getName()]) || (!empty($pinnedTopics[$category->getName()]) && count($pinnedTopics[$category->getName()])) > 0) {
                            // 0 topic to display
                            ?>
                            <div class="blockStyleOne container">
                                <span>Il n'y a aucun sujet d'ouvert pour l'instant</span>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </section>
                <?php
            }
        } else {
            // 0 category : link FAQ
            ?>
            <div class="blockStyleOne container">
                <span>Le forum est vide pour l'instant.</span>
                <?php
                if ($_SESSION['grade'] === "admin" || $_SESSION['grade'] === "moderator") {
                    ?>
                    <span>Vous pouvez regarder la <a href="index.php?action=faq#forum">F.A.Q</a> pour plus d'info sur comment gérer le forum</span>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>
    </article>
</section>
