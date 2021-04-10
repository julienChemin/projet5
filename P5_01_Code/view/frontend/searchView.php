<section id="searchView">
    <?php
    if (empty($_POST['keyWord']) && empty($_GET['sortBy'])) {
        //'home' of search
        ?>
        <h1 class="container">Rechercher du contenu</h1>

        <div id="homeSearch" class="fullWidth container">
            <div id="searchByKeyWord">
                <h2>Recherche par mots-clés</h2>

                <form id="formSearchByKeyWord" method="POST" action="index.php?action=search">
                    <input type="text" name="keyWord">
                    <input type="submit" name="Rechercher" value="Rechercher">
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

        <h2 class="container">Voir aussi</h2>

        <div id="alsoSee" class="container">
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
        <div id="searchByKeyWord" class="container">
            <h1>Recherche par mots-clés</h1>

            <p>Résultat pour : <?=$_POST['keyWord']?></p>
            <form id="formSearchByKeyWord" method="POST" action="index.php?action=search">
                <input type="text" name="keyWord" value="<?=$_POST['keyWord']?>">
                <input type="submit" name="Rechercher">
            </form>
        </div>

        <?php
        if (!empty($data['result'])) {
            if (!empty($data['result']['school'])) {
                //school where name contains the key word searched
                $result = $data['result']['school'];
                ?>
                <div class="container">
                    <h2>Établissements</h2>
                </div>

                <article>
                    <div id="blockSchools" class="blockResult blockResultSchool fullWidth container">
                        <?php
                        for($i=0; $i<count($result); $i++) {
                            if ($result[$i]->getName() !== NO_SCHOOL) {
                                ?>
                                <div class="blockSchool">
                                    <a href="index.php?action=schoolProfile&school=<?=$result[$i]->getName()?>">
                                        <div>
                                            <figure>
                                                <img src="<?=$result[$i]->getLogo()?>" alt="Logo de l'établissement">
                                            </figure>
                                        </div>

                                        <div>
                                            <?=$result[$i]->getName()?>
                                        </div>
                                    </a>
                                </div>
                                <?php
                            }
                        }
                    echo '</div>';
                echo '</article>';
            }

            if (!empty($data['result']['user'])) {
                //user where name contains the key word searched
                $result = $data['result']['user'];
                ?>
                <div class="container">
                    <h2>Utilisateurs</h2>
                </div>

                <article>
                    <div class="blockResult blockResultUser fullWidth container">
                        <?php
                        for($i=0; $i<count($result); $i++) {
                            if ($result[$i]->getSchool() !== ALL_SCHOOL) {
                                ?>
                                <div>
                                    <a href="index.php?action=userProfile&userId=<?=$result[$i]->getId()?>">
                                        <div title="<?=$result[$i]->getFirstName()?> <?=$result[$i]->getLastName()?>" style="background-image: url('<?=$result[$i]->getProfilePicture()?>');"></div>

                                        <div>
                                            <p><?=$result[$i]->getFirstName()?></p>
                                            <p><?=$result[$i]->getLastName()?></p>
                                        </div>
                                    </a>
                                </div>
                                <?php
                            }
                        }
                    echo '</div>';
                echo '</article>';
            }
            if (!empty($data['result']['post'])) {
                //post where title contains the key word searched
                $result = $data['result']['post'];
                ?>
                <div class="container">
                    <h2>Publications</h2>
                </div>

                <article>
                    <div class="blockResult blockResultPost fullWidth container">
                        <?php
                        for($i=0; $i<count($result); $i++) {
                            echo '<div>';
                                echo '<a href="index.php?action=post&id=' . $result[$i]->getId() . '">';
                                    if (empty($result[$i]->getFilePath())) {
                                        switch ($result[$i]->getFileType()) {
                                            case 'image' :
                                                $result[$i]->setFilePath('public/images/fileImage.png');
                                            break;

                                            case 'video' :
                                                $result[$i]->setFilePath('public/images/defaultVideoThumbnail.png');
                                            break;
                                        }
                                    } elseif ($result[$i]->getFileType() === 'video') {
                                        echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png" alt="Publication de type vidéo">';
                                    }

                                    echo '<figure class="figureProfilePicture fullWidth">';
                                        echo '<figcaption>';
                                            echo '<p>' . $result[$i]->getTitle() . '</p>';
                                        echo '</figcaption>';

                                        echo '<div><img src="' . $result[$i]->getFilePath() . '" alt="Aperçu de la publication"></div>';
                                    echo '</figure>';
                                echo '</a>';
                            echo '</div>';
                        }
                    echo '</div>';
                echo '</article>';
            }
            if (!empty($data['result']['tag'])) {
                //tag who contains the key word searched
                $result = $data['result']['tag'];
                ?>
                <div class="container">
                    <h2>Tags</h2>
                </div>

                <article>
                    <div class="blockResult blockResultTag fullWidth container">
                        <?php
                        for($i=0; $i<count($result); $i++) {
                            ?>
                            <div>
                                <a href="index.php?action=search&sortBy=tag&tag=<?=$result[$i]['name']?>">
                                    <p class="tag"><?=$result[$i]['name']?></p>
                                    <span>- (<?=$result[$i]['tagCount']?>)</span>
                                </a>
                            </div>
                            <?php
                        }
                    echo '</div>';
                echo '</article>';
            }
        } else {
            ?>
            <p class="container">Aucun résultats n'a été trouvé</p>
            <?php
        }
    } elseif (!empty($_GET['sortBy'])) {
        //result for quick search
        if (!empty($data['items'])) {
            switch ($_GET['sortBy']) {
                case 'lastPosted' :
                    echo '<h1 class="container">Les plus récentes</h1>';

                    echo '<article>';
                        echo '<div id="resultQuickSearch" class="container">';
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
                                        echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png" alt="Publication de type vidéo">';
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
                break;

                case 'mostLiked' :
                    echo '<h1 class="container">Les plus appréciées</h1>';

                    echo '<article>';
                        echo '<div id="resultQuickSearch" class="container">';
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
                                        echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png" alt="Publication de type vidéo">';
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
                break;

                case 'school' :
                    if (empty($_GET['school'])) {
                        //display all school
                        echo '<h1 class="container">Publications filtrées par établissement</h1>';

                        echo '<article>';
                            echo '<div id="blockSchools" class="container">';
                                foreach ($data['items'] as $school) {
                                    if ($school->getName() !== NO_SCHOOL) {
                                        !$school->getIsActive() ? $classIsActive = ' inactiveSchool' : $classIsActive = '';
                                        !$school->getIsActive() ? $title = ' Cet établissement est inactif sur le site' : $title = '';
                                        ?>

                                        <div class="blockSchool <?=$classIsActive?>">
                                            <a a href="index.php?action=search&sortBy=school&school=<?=$school->getName()?>" title="<?=$title?>">
                                                <div>
                                                    <figure>
                                                        <img src="<?=$school->getLogo()?>" alt="Logo de l'établissement">
                                                    </figure>
                                                </div>

                                                <div>
                                                    <h2><?=$school->getName()?></h2>
                                                </div>
                                            </a>
                                        </div>

                                        <?php
                                    }
                                }
                            echo '</div>';
                        echo '</article>';
                    } else {
                        //display posts publish by user affiliated to this school
                        echo '<h1 class="container">Publications filtrées par établissement : ' . $_GET['school'] . '</h1>';

                        echo '<article>';
                            echo '<div id="resultQuickSearch" class="container">';
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
                                            echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png" alt="Publication de type vidéo">';
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
                    }
                break;

                case 'tag' :
                    echo '<h1 class="container">Publications filtrées par tag : ' . $_GET['tag'] . '</h1>';

                    echo '<article>';
                        echo '<div id="resultQuickSearch" class="container">';
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
                                        echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png" alt="Publication de type vidéo">';
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
                break;
            }
        } else {
            echo '<p class="blockStyleOne">Aucun résultats n\'a été trouvé</p>';
        }

        if (!empty($data['items']) && ($_GET['sortBy'] !== 'school' || $_GET['sortBy'] === 'school' && !empty($_GET['school']))) {
            //display paging
            echo '<nav id="pagingQuickSearch" class="fullWidth container">';
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

                echo '</ol>';
            echo '</nav>';
        }
    }
    ?>
</section>
