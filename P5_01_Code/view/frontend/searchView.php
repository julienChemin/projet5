<section id="searchView" class="container">
    <?php
    if (empty($_POST['keyWord']) && empty($_GET['sortBy'])) {
        //'home' of search
        ?>
        <h1>Rechercher du contenu</h1>
        <div id="homeSearch" class="fullWidth">
            <div id="searchByKeyWord">
                <h2>Recherche par mots-clés</h2>
                <form id="formSearchByKeyWord" method="POST" action="index.php?action=search">
                    <input type="text" name="keyWord">
                    <input type="submit" name="Rechercher">
                </form>
            </div>
            <div id="frequentlySearched">
                <h2>Recherches fréquentes</h2>
                <div>
                    <a class="fullWidth" href="index.php?action=search&sortBy=lastPosted">Les plus récentes</a>
                    <a class="fullWidth" href="index.php?action=search&sortBy=mostLiked">Les plus appréciées</a>
                    <a class="fullWidth" href="index.php?action=search&sortBy=school">Filtrer par établissement</a>
                </div>
            </div>
            <div id="advancedSearch">
                <h2>Recherche avancée</h2>
                <p>Pour mélanger plusieurs critères de recherche</p>
                <p><a href="index.php?action=advancedSearch">Cliquez ici</a></p>
            </div>
        </div>
        <h2>Voir aussi</h2>
        <div id="alsoSee">
            <div>
                <p><a href="index.php?action=listSchools">Voir la liste des établissement scolaire</a></p>
            </div>
            <div>
                <p><a href="index.php?action=listTags">Voir la liste des tags</a></p>
            </div>
        </div>
        <?php
    } elseif (!empty($_POST['keyWord'])) {
        //result for the search by key word
        ?>
        <div id="searchByKeyWord">
            <h1>Recherche par mots-clés</h1>
            <form id="formSearchByKeyWord" method="POST" action="index.php?action=search">
                <input type="text" name="keyWord">
                <input type="submit" name="Rechercher">
            </form>
        </div>
        <?php
        if (!empty($data['result'])) {
            if (!empty($data['result']['school'])) {
                //school where name contains the key word searched
                $data['PostsManager']->displayResultSearchByKeyWord($data['result']['school'], 'school');
            }
            if (!empty($data['result']['user'])) {
                //user where name contains the key word searched
                $data['PostsManager']->displayResultSearchByKeyWord($data['result']['user'], 'user');
            }
            if (!empty($data['result']['post'])) {
                //post where title contains the key word searched
                $data['PostsManager']->displayResultSearchByKeyWord($data['result']['post'], 'post');
            }
            if (!empty($data['result']['tag'])) {
                //tag who contains the key word searched
                $data['PostsManager']->displayResultSearchByKeyWord($data['result']['tag'], 'tag');
            }
        } else {
            ?>
            <p>Aucun résultats n'a été trouvé</p>
            <?php
        }
    } elseif (!empty($_GET['sortBy'])) {
        //result for quick search
        if (!empty($data['items'])) {
            switch ($_GET['sortBy']) {
                case 'lastPosted' :
                    echo '<h1>Les plus récentes</h1>';
                    echo '<div id="resultQuickSearch">';
                    foreach ($data['items'] as $post) {
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
                            echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png">';
                        }
                        ?>
                        <a href="index.php?action=post&id=<?=$post->getId()?>">
                            <img src="<?=$post->getFilePath()?>">
                        </a>
                        <?php
                        echo '</figure>';
                    }
                    echo '</div>';
                    break;
                case 'mostLiked' :
                    echo '<h1>Les plus appréciées</h1>';
                    echo '<div id="resultQuickSearch">';
                    foreach ($data['items'] as $post) {
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
                            echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png">';
                        }
                        ?>
                        <a href="index.php?action=post&id=<?=$post->getId()?>">
                            <img src="<?=$post->getFilePath()?>">
                        </a>
                        <?php
                        echo '</figure>';
                    }
                    echo '</div>';
                    break;
                case 'school' :
                    if (empty($_GET['school'])) {
                        //display all school
                        echo '<h1>Publications filtrées par établissement</h1>';
                        echo '<div id="blockSchools">';
                        foreach ($data['items'] as $school) {
                            if ($school->getName() !== NO_SCHOOL) {
                                !$school->getIsActive() ? $classIsActive = ' inactiveSchool' : $classIsActive = '';
                                !$school->getIsActive() ? $title = ' Cet établissement est inactif sur le site' : $title = '';
                                ?>
                                <a href="index.php?action=search&sortBy=school&school=<?=$school->getName()?>" class="blockSchool">
                                    <div class="<?=$classIsActive?>" title="<?=$title?>">
                                        <div>
                                            <figure>
                                                <img src="<?=$school->getLogo()?>">
                                            </figure>
                                        </div>
                                        <div>
                                            <h2><?=$school->getName()?></h2>
                                        </div>
                                    </div>
                                </a>
                                <?php
                            }
                        }
                        echo '</div>';
                    } else {
                        //display posts publish by user affiliated to this school
                        echo '<h1>Publications filtrées par établissement : ' . $_GET['school'] . '</h1>';
                        echo '<div id="resultQuickSearch">';
                        foreach ($data['items'] as $post) {
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
                                echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png">';
                            }
                            ?>
                            <a href="index.php?action=post&id=<?=$post->getId()?>">
                                <img src="<?=$post->getFilePath()?>">
                            </a>
                            <?php
                            echo '</figure>';
                        }
                        echo '</div>';
                    }
                    break;
                case 'tag' :
                    echo '<h1>Publications filtrées par tag : ' . $_GET['tag'] . '</h1>';
                    echo '<div id="resultQuickSearch">';
                    foreach ($data['items'] as $post) {
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
                            echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png">';
                        }
                        ?>
                        <a href="index.php?action=post&id=<?=$post->getId()?>">
                            <img src="<?=$post->getFilePath()?>">
                        </a>
                        <?php
                        echo '</figure>';
                    }
                    echo '</div>';
                    break;
            }
        } else {
            echo '<p class="blockStyleOne">Aucun résultats n\'a été trouvé</p>';
        }
        if (!empty($data['items']) && ($_GET['sortBy'] !== 'school' || $_GET['sortBy'] === 'school' && !empty($_GET['school']))) {
            //display paging
            echo '<nav id="pagingQuickSearch" class="fullWidth">';
            echo '<ol>';
            !empty($_GET['page']) ? $actualPage = intval($_GET['page']) : $actualPage = 1;
            //$_get[school] is fill only on the search by school
            !empty($_GET['school']) ? $schoolOnUrl = '&school=' . $_GET['school'] : $schoolOnUrl = '';
            !empty($_GET['tag']) ? $tagOnUrl = '&tag=' . $_GET['tag'] : $tagOnUrl = '';
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
                        echo '<a href="index.php?action=search&sortBy=' . $_GET['sortBy'] . $schoolOnUrl . $tagOnUrl . '&page=' . ($i+1) . '&offset=' . $offset . '">' . ($i+1) . '</a></li>';
                    }
                    if ($data['nbPage'] > 10) {
                        echo '<li>...</li>';
                    }
                    echo '<li><a href="index.php?action=search&sortBy=' . $_GET['sortBy'] . $schoolOnUrl . $tagOnUrl . '&page=' . $data['nbPage'] . '&offset=' . ($data['nbPage']-1)*$data['nbPostsByPage'] . '">' . $data['nbPage'] . '</a></li>';
                } else {
                    //first page
                    echo '<li><a href="index.php?action=search&sortBy=' . $_GET['sortBy'] . $schoolOnUrl . $tagOnUrl . '&page=1&offset=0">1</a></li>';
                    echo '<li>...</li>';
                    //2 page before actual
                    echo '<li><a href="index.php?action=search&sortBy=' . $_GET['sortBy'] . $schoolOnUrl . $tagOnUrl . '&page=' . ($actualPage-2) . '&offset=' . ($actualPage-3)*$data['nbPostsByPage'] . '">' . ($actualPage-2) . '</a></li>';
                    echo '<li><a href="index.php?action=search&sortBy=' . $_GET['sortBy'] . $schoolOnUrl . $tagOnUrl . '&page=' . ($actualPage-1) . '&offset=' . ($actualPage-2)*$data['nbPostsByPage'] . '">' . ($actualPage-1) . '</a></li>';
                    //actual page
                    echo '<li class="actualPage"><a href="index.php?action=search&sortBy=' . $_GET['sortBy'] . $schoolOnUrl . $tagOnUrl . '&page=' . ($actualPage) . '&offset=' . ($actualPage-1)*$data['nbPostsByPage'] . '">' . ($actualPage) . '</a></li>';
                    //2 page after actual
                    if ($actualPage+1 < $data['nbPage']) {
                        echo '<li><a href="index.php?action=search&sortBy=' . $_GET['sortBy'] . $schoolOnUrl . $tagOnUrl . '&page=' . ($actualPage+1) . '&offset=' . ($actualPage)*$data['nbPostsByPage'] . '">' . ($actualPage+1) . '</a></li>';
                    }
                    if ($actualPage+2 < $data['nbPage']) {
                        echo '<li><a href="index.php?action=search&sortBy=' . $_GET['sortBy'] . $schoolOnUrl . $tagOnUrl . '&page=' . ($actualPage+2) . '&offset=' . ($actualPage+1)*$data['nbPostsByPage'] . '">' . ($actualPage+2) . '</a></li>';
                    }
                    //last page
                    if ($actualPage+2 < $data['nbPage']-1) {
                        echo '<li>...</li>';
                    }
                    if ($actualPage < $data['nbPage']) {
                        echo '<li><a href="index.php?action=search&sortBy=' . $_GET['sortBy'] . $schoolOnUrl . $tagOnUrl . '&page=' . $data['nbPage'] . '&offset=' . ($data['nbPage']-1)*$data['nbPostsByPage'] . '">' . $data['nbPage'] . '</a></li>';
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
                    echo '<a href="index.php?action=search&sortBy=' . $_GET['sortBy'] . $schoolOnUrl . $tagOnUrl . '&page=' . ($i+1) . '&offset=' . $offset . '">' . ($i+1) . '</a></li>';
                }
            }
            echo '</ol></nav>';
        }
    }
    ?>
</section>
