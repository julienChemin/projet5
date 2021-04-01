<?php
$post = $data['post'];
$comments = $data['comments'];
$asidePosts = $data['asidePosts'];
$urlAddPostOnFolder = $data['urlAddPostOnFolder'];
$author = $data['author'];
$userSchool = $data['userSchool'];
$user = $data['user'];
$userIsAuthor = $data['userInfo']['userIsAuthor'];
$userIsAdmin = $data['userInfo']['userIsAdmin'];
$userIsModerator = $data['userInfo']['userIsModerator'];
?>
<section id="viewFolder" class="container">
    <article class="fullWidth">
        <div id="blockDescription" class="fullWidth">
            <?php
            if (!empty($post->getTitle())) {
                echo '<h1>' . $post->getTitle() . '</h1>';
            }
            if (!empty($post->getDescription())) {
                echo '<div>' . $post->getDescription() . '</div>';
            }
            echo '<span>Publié le ' . $post->getDatePublication() . ' </span>';
            ?>
        </div>
        <section>
            <div></div>
        </section>
        <hr>
    </article>
    <?php
    if (!empty($post->getOnFolder())) {
        echo '<div class="postIsOnFolder">Cette publication fait parti d\'un dossier - <a href="index.php?action=post&id=' . $post->getOnFolder() . '">consulter</a></div>';
    }
    ?>
    <section id="commentsAndRelatedPosts">
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
                            <a href="index.php?action=userProfile&userId=<?=$comment->getIdAuthor()?>" style="border-top-color:<?=$userColor?>; border-bottom-color:<?=$userColor?>;">
                                <img src="<?=$comment->getProfilePictureAuthor()?>" alt="Photo de profil">
                            </a>
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
                                    if (!empty($_SESSION['id']) && (intval($_SESSION['id']) === $comment->getIdAuthor() || $_SESSION['school'] === ALL_SCHOOL)) {
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
                    echo '<div class="comment"><p>La section commentaire est vide pour le moment, soyez le premier a donner votre avis !</p></div>';
                }
                ?>
            </div>
        </div>
        <aside>
            <div id="optionList">
                <nav>
                    <ul>
                        <?php
                        if (!empty($user)) {
                            echo '<li id="heart"><i class="far fa-heart" idpost="' . $post->getId() . '"></i></li>';
                        }
                        echo '<li id="nbLike"><span><span>' . $post->getNbLike() . '</span><i class="fas fa-heart"></i></span></li>';
                        if (!empty($user) && $user->getIsActive() && ($userIsAuthor 
                        || ($post->getPostType() === 'schoolPost' && $post->getIsPrivate() && $userSchool && $post->getIdSchool() === $userSchool->getId() 
                        && ($userIsAdmin || $userIsModerator || empty($post->getListAuthorizedGroups()) || in_array($user->getSchoolGroup(), $post->getListAuthorizedGroups()))))) {
                            echo '<li id="postOnFolder"><a href="' . $urlAddPostOnFolder . '"><i class="fas fa-folder-plus"></i></a></li>';
                        }
                        if (!empty($user) && ($userIsAuthor || $_SESSION['school'] === ALL_SCHOOL || ($post->getPostType() === 'schoolPost' && $post->getIdSchool() === $_SESSION['idSchool'] && $userIsAdmin))) {
                            echo '<li id="deletePost" title="Supprimer la publication"><i class="far fa-trash-alt"></i></li>';
                            echo '<a href="index.php?action=deletePost&id=' . $post->getId() . '" id="confirmDeletePost" title="Supprimer la publication">Supprimer définitivement la publication ?</i></a>';
                        } elseif (!empty($user)) {
                            echo '<li title="Signaler"><a href="index.php?action=report&elem=post&id=' . $post->getId() . '"><i class="far fa-flag"></i></a></li>';
                        }
                        ?>
                    </ul>
                </nav>
            </div>
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

                    <a href="index.php?action=userProfile&userId=<?=$author->getId()?>">
                        <img src="<?=$author->getProfilePicture()?>" alt="Photo de profil">
                    </a>

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
            <div id="relatedPosts">
                <?php
                echo '<hr>';
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
                            echo '<a title="' . $post['title'] . '" href="index.php?action=post&id=' . $post['id'] . '">';
                            echo '<img class="thumbnailFolder" src="' . $post['filePath'] . '" alt="Aperçu de la publication">';
                            echo '<img src="public/images/folder.png" alt="Publication de type dossier">';
                            echo '</a></figure>';
                        } else {
                            echo '<figure title="' . $post['title'] . '" class="postOnAside">';
                            echo '<a href="index.php?action=post&id=' . $post['id'] . '">';
                            if ($post['fileType'] === 'video' && $post['filePath'] !== 'public/images/defaultVideoThumbnail.png') {
                                echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png" alt="Publication de type vidéo">';
                            }
                            echo '<img src="' . $post['filePath'] . '" alt="Aperçu de la publication">';
                            echo '</a></figure>';
                        }
                    }
                    echo '</div><hr>';
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
                            echo '<img class="thumbnailFolder" src="' . $post['filePath'] . '" alt="Aperçu de la publication">';
                            echo '<img src="public/images/folder.png" alt="Publication de type dossier">';
                            echo '</a></figure>';
                        } else {
                            echo '<figure title="' . $post['title'] . '" class="postOnAside">';
                            echo '<a href="index.php?action=post&id=' . $post['id'] . '">';
                            if ($post['fileType'] === 'video' && $post['filePath'] !== 'public/images/defaultVideoThumbnail.png') {
                                echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png" alt="Publication de type vidéo">';
                            }
                            echo '<img src="' . $post['filePath'] . '" alt="Aperçu de la publication">';
                            echo '</a></figure>';
                        }
                    }
                    echo '</div><hr>';
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
                            echo '<img class="thumbnailFolder" src="' . $post->getFilePath() . '" alt="Aperçu de la publication">';
                            echo '<img src="public/images/folder.png" alt="Publication de type dossier">';
                            echo '</a></figure>';
                        } else {
                            echo '<figure title="' . $post->getTitle() . '" class="postOnAside">';
                            echo '<a href="index.php?action=post&id=' . $post->getId() . '">';
                            if ($post->getFileType() === 'video' && $post->getFilePath() !== 'public/images/defaultVideoThumbnail.png') {
                                echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png" alt="Publication de type vidéo">';
                            }
                            echo '<img src="' . $post->getFilePath() . '" alt="Aperçu de la publication">';
                            echo '</a></figure>';
                        }
                    }
                    echo '</div><hr>';
                }
                ?>
            </div>
        </aside>
    </section>
</section>
