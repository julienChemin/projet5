<?php
$post = $data['post'];
$comments = $data['comments'];
$asidePosts = $data['asidePosts'];
$author = $data['author'];
$user = $data['user'];
if (!empty($data['fileInfo'])) {
    $fileInfo = $data['fileInfo'];
}
$post->getFileType() === 'compressed' ? $classSection = 'showCompressedFile' : $classSection = '';
?>
<section id="viewPost">
    <article>
        <section class='<?=$classSection?>'>
            <div class="container">
                <?php
                    !empty($post->getTitle()) ? $alt = $post->getTitle() : $alt = 'Publication';
                    switch ($post->getFileType()) {
                        case 'image' :
                            echo '<a href="' . $post->getFilePath() . '"><img class="fileImage" src="' . $post->getFilePath() . '" alt="' . $alt . '"></a>';
                        break;

                        case 'video' :
                            echo '<iframe width="90%" height="90%" src="https://www.youtube.com/embed/' . $post->getUrlVideo() . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                        break;

                        case 'compressed' :
                            ?>
                            <div class="compressedFile">
                                <img src="public/images/fileOther.png" alt="<?=$alt?>">

                                <a title="Cliquez pour télécharger" href="<?=$post->getFilePath()?>">
                                    <i class="fas fa-download"></i>
                                    <span>Télécharger</span>
                                </a>
                            </div>

                            <div class="compressedFileDescription">
                                <?php
                                if (empty($fileInfo)) {
                                    echo "<p>Aucune information n'a pu être récupéré sur cette archive</p>";
                                } else if (!is_array($fileInfo) && $fileInfo === 'empty') {
                                    echo "<p>L\'archive est vide</p>";
                                } else if (!is_array($fileInfo) && $fileInfo === 'cannotReadRar') {
                                    echo "<p>";
                                        echo "<h2>La récupération d'information sur les archives de type '.rar' n'est pas disponible pour le moment</h2>";
                                        echo "<h3>Utilisez des archives '.zip' si vous souhaitez que les informations soit affiché</h3>";
                                    echo "</p>";
                                } else if (is_array($fileInfo)) {
                                    echo "<h2>L'archive contient : </h2>";
                                    for ($i = 0; $i < count($fileInfo['name']); $i++) {
                                        echo'<p>';
                                            echo "<span>" . $fileInfo['name'][$i] . "</span>";
                                            echo "<br>";
                                            echo "<span>" . $fileInfo['size'][$i] . "</span>";
                                        echo'</p>';
                                    }
                                }
                                ?>
                            </div>
                            <?php
                        break;
                    }
                ?>
            </div>
        </section>

        <aside id="optionList" class="fullWidth">
            <nav class="container">
                <ul>
                    <?php
                    if (!empty($user)) {
                        echo '<li id="heart">';
                            echo '<i class="far fa-heart" idpost="' . $post->getId() . '"></i>';
                            echo '<span>' . $post->getNbLike() . '</span>';
                        echo '</li>';
                    }
                    ?>
                </ul>

                <ul>
                    <?php
                    if (!empty($_SESSION['id']) && ($post->getIdAuthor() === $_SESSION['id'] 
                    || $_SESSION['school'] === ALL_SCHOOL 
                    || ($post->getPostType() === 'schoolPost' && $_SESSION['grade'] === ADMIN && $post->getIdSchool() === $_SESSION['idSchool']))) {
                        echo '<li id="deletePost" title="Supprimer la publication"><i class="far fa-trash-alt"></i></li>';
                    } elseif (!empty($_SESSION['id'])) {
                        echo '<li title="Signaler"><a href="index.php?action=report&elem=post&id=' . $post->getId() . '"><i class="far fa-flag"></i></a></li>';
                    }
                    ?>
                </ul>
            </nav>
        </aside>

        <?php
        if ($post->getFileType() === 'compressed') {
            echo '<p id="warningMsg">Ne téléchargez un fichier que si vous savez d\'où il vient et ce qu\'il contient</p>';
        }

        if (!empty($post->getOnFolder())) {
            echo '<div class="postIsOnFolder container">Cette publication fait partie d\'un dossier - <a href="index.php?action=post&id=' . $post->getOnFolder() . '">consulter</a></div>';
        }
        ?>

        <div id="blockDescription" class="fullWidth">
            <div id="authorProfile">
                <?php
                if ($author !== null) {
                    if ($author->getIsAdmin() || $author->getIsModerator()) {
                        $authorColor = '#de522f';
                    } elseif ($author->getSchool() !== NO_SCHOOL) {
                        $authorColor = '#CF8B3F';
                    } else {
                        $authorColor = '#b0a396';
                    }
                    ?>

                    <a href="index.php?action=userProfile&userId=<?=$author->getId()?>" style="background-image: url('<?=$author->getProfilePicture()?>');"></a>

                    <div>
                        <a href="index.php?action=userProfile&userId=<?=$author->getId()?>" style="color:<?=$authorColor?>;">
                            <span><?=$author->getFirstName()?> <?=$author->getLastName()?></span>
                        </a>
                        
                        <?php
                            if ($author->getSchool() !== NO_SCHOOL) {
                                ?>
                                <a href="index.php?action=schoolProfile&school=<?=$author->getSchool()?>">
                                    <span><?=$author->getSchool()?></span>
                                </a>
                                <?php
                            }
                        ?>
                    </div>
                    <?php
                } else {
                    echo '<div>L\'auteur de cette publication n\'existe plus</div>';
                }
                ?>
            </div>

            <div>
                <?php
                if (!empty($post->getTitle())) {
                    echo '<h1><strong>' . $post->getTitle() . '</strong></h1>';
                }

                if (!empty($post->getDescription())) {
                    echo '<div class="publicationDescription">' . $post->getDescription() . '</div>';
                } else {
                    echo '<div class="publicationDescription"><p class="noPostDescription">Il n\'y a pas de description pour cette publication</p></div>';
                }

                if ($post->getFileType() === 'video') {
                    echo '<p id="directLink"><a href="https://www.youtube.com/watch?v=' . $post->getUrlVideo() . '">Voir la vidéo sur youtube</a></p>';
                }

                echo '<p class="folderDatePublication">Publié le ' . $post->getDatePublication() . ' </p>';
                ?>
            </div>
        </div>
    </article>

    <?php
    if (!empty($post->getlistTags())) {
        echo '<section id="listTags">';
            echo '<aside class="container">';
                foreach ($post->getlistTags() as $tag) {
                    echo '<a class="tag" href="index.php?action=search&sortBy=tag&tag=' . $tag . '"><strong>' . $tag . '</strong></a>';
                }
            echo '</aside>';
        echo '</section>';
    }
    ?>
    
    <section id="commentsAndRelatedPosts" class="container">
        <div id="blockComments">
            <form id="addComment">
                <?php
                if (!empty($user)) {
                    ?>
                    <input type="hidden" name="userId" value="<?=$user->getId()?>">
                    <input type="hidden" name="userName" value="<?=$user->getFirstName()?> <?=$user->getLastName()?>">
                    <input type="hidden" name="userPicture" value="<?=$user->getProfilePicture()?>">
                    <span id="msgComment"></span>
                    <textarea wrap="hard" name="commentContent" placeholder="Ajouter un commentaire"></textarea>
                    <input type="hidden" name="idPost" value="<?=$_GET['id']?>">
                    <input type="button" name="submitComment" id="submitComment" value="Ajouter">
                    <?php
                } else {
                    echo '<p>Vous devez être connecté pour poster un commentaire</p>';
                }
                ?>
            </form>
            <div class="fullWidth">
                <?php
                if (!empty($comments)) {
                    foreach ($comments as $comment) {
                        if ($comment->getAuthorIsAdmin() || $comment->getAuthorIsModerator()) {
                            $userColor = '#de522f';
                        } elseif ($comment->getAuthorSchoolName() !== NO_SCHOOL) {
                            $userColor = '#CF8B3F';
                        } else {
                            $userColor = '#b0a396';
                        }
                        ?>

                        <div class="comment fullWidth">
                            <a href="index.php?action=userProfile&userId=<?=$comment->getIdAuthor()?>" style="background-image: url('<?=$comment->getProfilePictureAuthor()?>');"></a>
                            <div>
                                <a href="index.php?action=userProfile&userId=<?=$comment->getIdAuthor()?>" style="color:<?=$userColor?>;">
                                    <?=$comment->getFirstNameAuthor()?> <?=$comment->getLastNameAuthor()?>
                                </a>
                                <p>
                                    <?=nl2br($comment->getContent())?>
                                </p>
                                <p>
                                    <?=$comment->getDatePublication()?>
                                    <?php
                                    if (!empty($_SESSION['id']) && (intval($_SESSION['id']) === $post->getIdAuthor() || intval($_SESSION['id']) === $comment->getIdAuthor() || $_SESSION['school'] === ALL_SCHOOL)) {
                                        echo ' - <span class="deleteComment" idcomment="' . $comment->getId() . '">Supprimer le commentaire</span><span class="confirmDelete">Supprimer définitivement ?</span>';
                                    }
                                    ?>
                                </p>
                                <?php
                                if (!empty($_SESSION['id']) && intval($_SESSION['id']) !== $comment->getIdAuthor()) {
                                    echo '<a href="index.php?action=report&elem=comment&id=' . $comment->getId() . '&idPost=' . $post->getId() . '" title="Signaler le commentaire" class="reportComment"><i class="far fa-flag"></i></a>';
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="comment"><p>La section commentaire est vide pour le moment, soyez le premier à donner votre avis !</p></div>';
                }
                ?>
            </div>
        </div>

        <aside>
            <div id="relatedPosts">
                <?php
                if (!empty($asidePosts['onFolder'])) {
                    //post is on folder, display other post on this folder
                    echo '<h1>Autres publications dans ce dossier</h1>';
                    echo '<div>';
                    foreach ($asidePosts['onFolder'] as $post) {
                        if (empty($post['filePath']) || $post['fileType'] === 'compressed') {
                            switch ($post['fileType']) {
                                case 'image' :
                                    $post['filePath'] = 'public/images/fileImage.png';
                                    break;
                                case 'video' :
                                    $post['filePath'] = 'public/images/defaultVideoThumbnail.png';
                                    break;
                                case 'folder' :
                                        $post['filePath'] = 'public/images/folder.png';
                                    break;
                                case 'compressed' :
                                        $post['filePath'] = 'public/images/fileOther.png';
                                    break;
                            }
                        }
                        if ($post['fileType'] === 'folder' && $post['filePath'] !== 'public/images/folder.png') {
                            echo '<figure title="' . $post['title'] . '" class="postOnAside">';
                            echo '<a href="index.php?action=post&id=' . $post['id'] . '">';
                            echo '<img src="' . $post['filePath'] . '" alt="Aperçu de la publication">';
                            echo '<img class="iconeFolder" src="public/images/folder.png" alt="Publication de type dossier">';
                            echo '</a></figure>';
                        } else {
                            echo '<figure title="' . $post['title'] . '" class="postOnAside">';
                            echo '<a href="index.php?action=post&id=' . $post['id'] . '">';
                            if ($post['fileType'] === 'video' && $post['filePath'] !== 'public/images/defaultVideoThumbnail.png') {
                                echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png" alt="Publication de type vidéo">';
                            }
                            echo '<img src="' . $post['filePath'] . '" alt="Publication">';
                            echo '</a></figure>';
                        }
                    }
                    echo '</div>';
                }
                if (!empty($asidePosts['public'])) {
                    //display public post of this user/school
                    $asidePosts['postType'] === 'userPost' ? $entity = 'utilisateur' : $entity = 'établissement';
                    echo '<h1>Publications de cet ' . $entity . '</h1>';
                    echo '<div>';
                    foreach ($asidePosts['public'] as $post) {
                        if (empty($post['filePath']) || $post['fileType'] === 'compressed') {
                            switch ($post['fileType']) {
                                case 'image' :
                                    $post['filePath'] = 'public/images/fileImage.png';
                                    break;
                                case 'video' :
                                    $post['filePath'] = 'public/images/defaultVideoThumbnail.png';
                                    break;
                                case 'folder' :
                                        $post['filePath'] = 'public/images/folder.png';
                                    break;
                                case 'compressed' :
                                        $post['filePath'] = 'public/images/fileOther.png';
                                    break;
                            }
                        }
                        if ($post['fileType'] === 'folder' && $post['filePath'] !== 'public/images/folder.png') {
                            echo '<figure title="' . $post['title'] . '" class="postOnAside">';
                            echo '<a href="index.php?action=post&id=' . $post['id'] . '">';
                            echo '<img src="' . $post['filePath'] . '" alt="Aperçu de la publication">';
                            echo '<img class="iconeFolder" src="public/images/folder.png" alt="Publication de type dossier">';
                            echo '</a></figure>';
                        } else {
                            echo '<figure title="' . $post['title'] . '" class="postOnAside">';
                            echo '<a href="index.php?action=post&id=' . $post['id'] . '">';
                            if ($post['fileType'] === 'video' && $post['filePath'] !== 'public/images/defaultVideoThumbnail.png') {
                                echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png" alt="Publication de type vidéo">';
                            }
                            !empty($post['title']) ? $alt = $post['title'] : $alt = 'Publication';
                            echo '<img src="' . $post['filePath'] . '" alt="' . $alt . '">';
                            echo '</a></figure>';
                        }
                    }
                    echo '</div>';
                }
                if (!empty($asidePosts['lastPosted'])) {
                    //display most recent post
                    echo '<h1>Posté récemment</h1>';
                    echo '<div>';
                    foreach ($asidePosts['lastPosted'] as $post) {
                        if (empty($post->getFilePath()) || $post->getFileType() === 'compressed') {
                            switch ($post->getFileType()) {
                                case 'image' :
                                    $post->setFilePath('public/images/fileImage.png');
                                    break;
                                case 'video' :
                                    $post->setFilePath('public/images/defaultVideoThumbnail.png');
                                    break;
                                case 'folder' :
                                        $post->setFilePath('public/images/folder.png');
                                    break;
                                case 'compressed' :
                                        $post->setFilePath('public/images/fileOther.png');
                                    break;
                            }
                        }
                        if ($post->getFileType() === 'folder' && $post->getFilePath() !== 'public/images/folder.png') {
                            echo '<figure title="' . $post->getTitle() . '" class="postOnAside">';
                            echo '<a href="index.php?action=post&id=' . $post->getId() . '">';
                            echo '<img src="' . $post->getFilePath() . '" alt="Aperçu de la publication">';
                            echo '<img class="iconeFolder" src="public/images/folder.png" alt="Publication de type dossier">';
                            echo '</a></figure>';
                        } else {
                            echo '<figure title="' . $post->getTitle() . '" class="postOnAside">';
                            echo '<a href="index.php?action=post&id=' . $post->getId() . '">';
                            if ($post->getFileType() === 'video' && $post->getFilePath() !== 'public/images/defaultVideoThumbnail.png') {
                                echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png" alt="Publication de type vidéo">';
                            }
                            !empty($post->getTitle()) ? $alt = $post->getTitle() : $alt = 'Publication';
                            echo '<img src="' . $post->getFilePath() . '" alt="' . $alt . '">';
                            echo '</a></figure>';
                        }
                    }
                    echo '</div>';
                }
                if (!empty($asidePosts['withTag'])) {
                    //display posts for the 6 most popular tags
                    foreach ($asidePosts['withTag'] as $key => $arrTagPosts) {
                        if (!empty($arrTagPosts)) {
                            echo '<h1>Avec le tag ' . $key . '</h1>';
                            echo '<div>';
                            foreach ($arrTagPosts as $post) {
                                if (empty($post->getFilePath()) || $post->getFileType() === 'compressed') {
                                    switch ($post->getFileType()) {
                                        case 'image' :
                                            $post->setFilePath('public/images/fileImage.png');
                                            break;
                                        case 'video' :
                                            $post->setFilePath('public/images/defaultVideoThumbnail.png');
                                            break;
                                        case 'folder' :
                                            $post->setFilePath('public/images/folder.png');
                                            break;
                                        case 'compressed' :
                                            $post->setFilePath('public/images/fileOther.png');
                                            break;
                                    }
                                }
                                if ($post->getFileType() === 'folder' && $post->getFilePath() !== 'public/images/folder.png') {
                                    echo '<figure title="' . $post->getTitle() . '" class="postOnAside">';
                                    echo '<a href="index.php?action=post&id=' . $post->getId() . '">';
                                    echo '<img src="' . $post->getFilePath() . '" alt="Aperçu de la publication">';
                                    echo '<img class="iconeFolder" src="public/images/folder.png" alt="Publication de type dossier">';
                                    echo '</a></figure>';
                                } else {
                                    echo '<figure title="' . $post->getTitle() . '" class="postOnAside">';
                                    echo '<a href="index.php?action=post&id=' . $post->getId() . '">';
                                    if ($post->getFileType() === 'video' && $post->getFilePath() !== 'public/images/defaultVideoThumbnail.png') {
                                        echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png" alt="Publication de type vidéo">';
                                    }
                                    !empty($post->getTitle()) ? $alt = $post->getTitle() : $alt = 'Publication';
                                    echo '<img src="' . $post->getFilePath() . '" alt="' . $alt . '">';
                                    echo '</a></figure>';
                                }
                            }
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </aside>
    </section>
</section>

<div id="modal">
    <div id="confirmDeletePost">
        <p>Supprimer définitivement la publication ?</p>

        <div>
            <span class="closeModal">Annuler</span>
            <a href="index.php?action=deletePost&id=<?=$data['post']->getId()?>"  title="Supprimer la publication">Confirmer</a>
        </div>
    </div>
</div>