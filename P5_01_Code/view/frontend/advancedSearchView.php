<section id="advancedSearchView">
<?php
if (empty($_POST)) {
    ?>
    <form id="formAdvancedSearch" method="POST" action="index.php?action=advancedSearch">
        <h1>Filtrer par établissement</h1>

        <section class="fullWidth">
            <div id="blockFilterBySchool" class="fullWidth container">
                <div>
                    <input type="radio" name="schoolFilter" id="noSchoolFilter" value="noSchoolFilter" checked>
                    <label for="noSchoolFilter"><i class="fas fa-times"></i>pas de filtre</label>
                    <?php
                    for ($i=0;$i<count($data['schools']); $i++) {
                        $school = $data['schools'][$i];
                        if ($school->getName() !== NO_SCHOOL) {
                            echo '<input type="radio" name="schoolFilter" id="schoolFilter' . $i .  '" value="' . $school->getName() .  '">';
                            echo '<label for="schoolFilter' . $i .  '">';
                            echo '<img src="' . $school->getLogo() . '" alt="Logo de l\'établissement">' . $school->getName();
                            echo '</label>';
                        }
                    }
                    ?>
                </div>
            </div>
        </section>

        <h1>Trier par : </h1>

        <section class="fullWidth">
            <div id="blockSortBy" class="fullWidth container">
                <div>
                    <input type="radio" name="sortBy" value="lastPosted" id="radioLastPosted" checked>
                    <label for="radioLastPosted">Les plus récentes</label>
                    <input type="radio" name="sortBy" value="firstPosted" id="radioFirstPosted">
                    <label for="radioFirstPosted">Les moins récentes</label>
                    <input type="radio" name="sortBy" value="mostLiked" id="radioMostLiked">
                    <label for="radioMostLiked">Les plus appréciées</label>
                </div>
            </div>
        </section>

        <h1>Avec les tags : </h1>

        <section class="fullWidth">
            <div id="blockSortWithTag" class="fullWidth container">
                <input type="hidden" name="listTags" id="listTags">
                <div>
                    <div>
                        <label for="tagName">Entrez le nom d'un tag</label>
                        <input type="text" name="tagName" id="tagName" autocomplete="off">
                    </div>
                    <div id="selectedTags">
                            <h2>Tags sélectionné</h2>
                            <div></div>
                        </div>
                    <div id="recommendedTags">
                        <h2>Selectionnez un ou plusieurs tags</h2>
                        <div></div>
                    </div>
                </div>
            </div>
        </section>

        <input type="submit" name="submit" value="Rechercher">
    </form>
    <?php
} else {
    //display result of advanced search
    echo '<h1 class="container">Recherche avancée</h1>';
    //display filter for the advanced search
    echo '<div id="blockFilterAdvancedSearch" class="container">';
        echo '<p>Filtres de recherche : </p>';

        if ($_POST['schoolFilter'] !== 'noSchoolFilter') {
            echo '<p class="tag">' . htmlspecialchars($_POST['schoolFilter']) . '</p>';
        }

        if ($_POST['sortBy'] === 'lastPosted') {
            echo '<p class="tag">Les plus récentes</p>';
        } elseif ($_POST['sortBy'] === 'firstPosted') {
            echo '<p class="tag">Les plus anciennes</p>';
        } elseif ($_POST['sortBy'] === 'mostLiked') {
            echo '<p class="tag">Les plus appréciées</p>';
        }
        
        if ($_POST['listTags'] !== '') {
            $listTags = explode(',', htmlspecialchars($_POST['listTags']));
            for ($i = 1; $i < count($listTags); $i++) {
                echo '<p class="tag">tag : ' . $listTags[$i] . '</p>';
            }
        }
    echo '</div>';

    //display posts
    if ($data['posts']['count'] > 0) {
        echo '<article>';
            echo '<div id="resultQuickSearch" class="container">';
                foreach ($data['posts']['posts'] as $post) {
                    echo '<figure>';
                    if (empty($post->getFilePath())) {
                        switch ($post->getFileType()) {
                            case 'image' :
                                $post->setFilePath('public/images/fileImage.png');
                            break;

                            case 'video' :
                                $post->setFilePath('public/images/defaultVideoThumbnail.png');
                            break;
                        }
                    } elseif ($post->getFileType() === 'video') {
                        echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png" alt="Publication de type vidéo">';
                    } elseif ($post->getFileType() === 'grouped') {
                        echo '<img class="iconeVideo" src="public/images/file.png" alt="Publication groupé">';
                    }
                    ?>
                    <a href="index.php?action=post&id=<?=$post->getId()?>">
                        <img src="<?=$post->getFilePath()?>" alt="Aperçu de la publication">
                    </a>
                    <?php
                    echo '</figure>';
                }
            echo '</div>';
        echo '</article>';

        //display paging
        if (!empty($data['posts']['posts'])) {
            echo '<nav id="pagingQuickSearch" class="fullWidth container">';
            echo '<ol>';
            !empty($_POST['pageToGo']) ? $actualPage = intval($_POST['pageToGo']) : $actualPage = 1;
            if ($data['nbPage'] > 9) {
                if ($actualPage < 9) {
                    //display the 9 first page and the last one
                    for ($i=0; $i<9; $i++) {
                        $offset = $i*$data['nbPostsByPage'];
                        if ($i+1 === $actualPage) {
                            echo '<li class="actualPage">';
                        } else {
                            echo '<li>';
                        }
                        echo ($i+1) . '</li>';
                    }
                    if ($data['nbPage'] > 10) {
                        echo '<li>...</li>';
                    }
                    echo '<li>' . $data['nbPage'] . '</li>';
                } else {
                    //first page
                    echo '<li>1</li>';
                    echo '<li>...</li>';
                    //2 page before actual
                    echo '<li>' . ($actualPage-2) . '</li>';
                    echo '<li>' . ($actualPage-1) . '</li>';
                    //actual page
                    echo '<li class="actualPage">' . ($actualPage) . '</li>';
                    //2 page after actual
                    if ($actualPage+1 < $data['nbPage']) {
                        echo '<li>' . ($actualPage+1) . '</li>';
                    }
                    if ($actualPage+2 < $data['nbPage']) {
                        echo '<li>' . ($actualPage+2) . '</li>';
                    }
                    //last page
                    if ($actualPage+2 < $data['nbPage']-1) {
                        echo '<li>...</li>';
                    }
                    if ($actualPage < $data['nbPage']) {
                        echo '<li>' . $data['nbPage'] . '</li>';
                    }
                }
            } else {
                for ($i=0; $i<$data['nbPage']; $i++) {
                    $offset = $i*$data['nbPostsByPage'];
                    if ($i+1 === $actualPage) {
                        echo '<li class="actualPage">';
                    } else {
                        echo '<li>';
                    }
                    echo ($i+1) . '</li>';
                }
            }
            echo '</ol></nav>';
            //hidden form to keep variables
            ?>
            <form method="POST" action="index.php?action=advancedSearch" class="hide">
                <input type="hidden" name="schoolFilter" value="<?=$_POST['schoolFilter']?>">
                <input type="hidden" name="sortBy" value="<?=$_POST['sortBy']?>">
                <input type="hidden" name="listTags" value="<?=$_POST['listTags']?>">
                <input type="hidden" name="pageToGo" value="">
                <input type="submit" name="submit">
            </form>
            <?php
        }
    } else {
        echo '<p class="blockStyleOne">Aucun résultats n\'a été trouvé</p>';
    }
}
?>
</section>
