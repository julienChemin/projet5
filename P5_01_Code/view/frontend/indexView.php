<?php
$posts = $data['posts'];
?>
<article id=home>
    <section class="sliderPosts container">
        <?php
        if (count($posts['lastPosted']) > 0) {
            ?>
            <h1>Les publications les plus récentes</h1>
            <div class="posts">
                <div class="arrow arrowLeft">
                    <i class="fas fa-chevron-left"></i>
                </div>
                <div class="arrow arrowRight">
                    <i class="fas fa-chevron-left"></i>
                </div>
                <div class="slider" slidetype="lastPosted">
                    <div class="slide">
                        <?php
                        foreach ($posts['lastPosted'] as $post) {
                            !empty($post->getTitle()) ? $title = $post->getTitle() : $title = '';
                            !empty($post->getTitle()) ? $alt = $post->getTitle() : $alt = 'Aperçu de la publication';
                            echo '<figure class="items" title="' . $title . '">';
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
                            echo '<a href="index.php?action=post&id=' . $post->getId() . '"><img src="' . $post->getFilePath() . '" alt="' . $alt . '"></a></figure>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <a href="index.php?action=search&sortBy=lastPosted">Tout voir</a>
            <hr>
            <?php
        }
        if (count($posts['mostLiked']) > 0) {
            ?>
            <h1>Les publications les plus appréciées</h1>
            <div class="posts">
                <div class="arrow arrowLeft">
                    <i class="fas fa-chevron-left"></i>
                </div>
                <div class="arrow arrowRight">
                    <i class="fas fa-chevron-left"></i>
                </div>
                <div class="slider" slidetype="mostLiked">
                    <div class="slide">
                        <?php
                        foreach ($posts['mostLiked'] as $post) {
                            !empty($post->getTitle()) ? $title = $post->getTitle() : $title = '';
                            !empty($post->getTitle()) ? $alt = $post->getTitle() : $alt = 'Aperçu de la publication';
                            echo '<figure class="items" title="' . $title . '">';
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
                            echo '<a href="index.php?action=post&id=' . $post->getId() . '"><img src="' . $post->getFilePath() . '" alt="' . $alt . '"></a></figure>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <a href="index.php?action=search&sortBy=mostLiked">Tout voir</a>
            <hr>
            <?php
        }
        if (count($posts['bySchool']) > 0) {
            //display posts for 4 random popular tags
            foreach ($posts['bySchool'] as $key => $arrSchoolPosts) {
                if (!empty($arrSchoolPosts)) {
                    ?>
                    <h1>Publié par des élèves de l'établissement <span><?=$key?></span></h1>
                    <div class="posts">
                        <div class="arrow arrowLeft">
                            <i class="fas fa-chevron-left"></i>
                        </div>
                        <div class="arrow arrowRight">
                            <i class="fas fa-chevron-left"></i>
                        </div>
                        <div class="slider" slidetype="bySchool" slidevalue="<?=$key?>">
                            <div class="slide">
                                <?php
                                foreach ($arrSchoolPosts as $post) {
                                    !empty($post->getTitle()) ? $title = $post->getTitle() : $title = '';
                                    !empty($post->getTitle()) ? $alt = $post->getTitle() : $alt = 'Aperçu de la publication';
                                    echo '<figure class="items" title="' . $title . '">';
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
                                    echo '<a href="index.php?action=post&id=' . $post->getId() . '"><img src="' . $post->getFilePath() . '" alt="' . $alt . '"></a></figure>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <a href="index.php?action=search&sortBy=school&school=<?=$key?>">Tout voir</a>
                    <hr>
                    <?php
                }
            }
        }
        if (count($posts['withTag']) > 0) {
            //display posts for the 4 random popular tags
            foreach ($posts['withTag'] as $key => $arrTagPosts) {
                if (!empty($arrTagPosts)) {
                    ?>
                    <h1>Publication au hasard avec le tag <span><?=$key?></span></h1>
                    <div class="posts">
                        <div class="arrow arrowLeft">
                            <i class="fas fa-chevron-left"></i>
                        </div>
                        <div class="arrow arrowRight">
                            <i class="fas fa-chevron-left"></i>
                        </div>
                        <div class="slider" slidetype="withTag" slidevalue="<?=$key?>">
                            <div class="slide">
                                <?php
                                foreach ($arrTagPosts as $post) {
                                    !empty($post->getTitle()) ? $title = $post->getTitle() : $title = '';
                                    !empty($post->getTitle()) ? $alt = $post->getTitle() : $alt = 'Aperçu de la publication';
                                    echo '<figure class="items" title="' . $title . '">';
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
                                    echo '<a href="index.php?action=post&id=' . $post->getId() . '"><img src="' . $post->getFilePath() . '" alt="' . $alt . '"></a></figure>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <a href="index.php?action=search&sortBy=tag&tag=<?=$key?>">Tout voir</a>
                    <hr>
                    <?php
                }
            }
        }
        ?>
        <div class="blockStyleOne">
            <p>Vous pouvez faire une recherche pour obtenir des résultats plus ciblés</p>
            <a href="index.php?action=search">
                Cliquez ici, ou sur < <i class="fas fa-search"></i> >  dans la barre de navigation
            </a>
        </div>
    </section>
</article>
