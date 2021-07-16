<?php
$topic = $data['topicInfo']['topic'];
$replies = $data['topicInfo']['replies'];
$author = $data['topicAuthor'];
$user = $data['user'];
?>

<section id="forumTopic">
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

    <h1><?=$topic->getTitle()?></h1>

    <div class="container usefullLink">
        <p class="linkHomeForum hideUnder600Width">
            <a href="index.php?action=forum&school=<?=$data['school']->getName()?>">
                <i class="fas fa-door-open"></i>    Accueil du forum
            </a>

            <?php
            if ($user->getIsAdmin() || $user->getIsModerator()) {
                ?>
                <a href="indexAdmin.php?action=manageForum&school=<?=$data['school']->getName()?>">
                    <i class="fas fa-pencil-alt"></i>    Gérer le forum
                </a>
                <?php
            }
            ?>
        </p>
    </div>

    <article id="mainTopic" class="container">
        <div id="authorDescription">
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
                        <span><?=$author->getPseudo()?></span>
                    </a>

                    <a href="index.php?action=userProfile&userId=<?=$author->getId()?>" style="color:<?=$authorColor?>;">
                        <span><?=$author->getFirstName()?> <?=$author->getLastName()?></span>
                    </a>
                </div>

                <div id="topicOption">
                    <?php
                    if ($data['user']->getIsAdmin() || $data['user']->getIsModerator() || $user->getId() === $topic->getIdAuthor()) {
                        echo "<p><i class='fas fa-trash deleteTopic' title='Supprimer le fil de discution' topicId='" . $topic->getId() . "'></i></p>";

                        echo "<p>";
                            echo "<a href='index.php?action=editTopic&topicId=" . $topic->getId() . "'>";
                                echo "<i class='fas fa-pen editTopic' title='Modifier' topicId='" . $topic->getId() . "'></i>";
                            echo "</a>";
                        echo "</p>";

                        echo "<p id='blockToggleIsClose'>";
                            if ($topic->getIsClose()) {
                                echo "<i class='fas fa-lock topicIsClose' title='Déverrouiller le sujet' topicId='" . $topic->getId() . "'></i>";
                            } else {
                                echo "<i class='fas fa-unlock topicIsOpen' title='Verrouiller le sujet' topicId='" . $topic->getId() . "'></i>";
                            }
                        echo "</p>";
                        
                        if ($data['user']->getIsAdmin() || $data['user']->getIsModerator()) {
                            echo "<p id='blockToggleIsPinned'>";
                                $pinnedElemTitle = $topic->getIsPinned() ? "Désépingler le sujet" : "Épingler le sujet";
                                $classPinnedTopic = $topic->getIsPinned() ? "pinnedTopic" : "nonePinnedTopic";
                                echo "<i class='fas fa-thumbtack " . $classPinnedTopic . "' title='" . $pinnedElemTitle . "' topicId='" . $topic->getId() . "'></i>";
                            echo "</p>";
                        }
                    }
                    ?>
                </div>
                <?php
            } else {
                echo '<div>L\'auteur de cette publication n\'existe plus</div>';
            }
            ?>
        </div>

        <div id="mainTopicContent">
            <?php
            if ($topic->getContent()) {
                echo $topic->getContent();
            } else {
                ?>
                <p>Il n'y a aucune description pour ce sujet</p>
                <?php
            }
            ?>
        </div>
    </article>

    <div id="blockParticipants" class="container">
        <h2>Participants (sur cette page) : </h2>

        <div>
            <?php
            if ($replies && count($replies) > 0) {
                $uniqueParticipants = [];
                foreach ($replies as $reply) {
                    if (!in_array($reply->getIdAuthor(), $uniqueParticipants)) {
                        ?>
                        <a class="participant" href="#replyAnchor<?=$reply->getIdAuthor()?>" style="background-image:url('<?=$reply->getProfilePictureAuthor()?>')">
                            <div>
                                <span><?=$reply->getPseudoAuthor()?></span>
                                <span><?=$reply->getFirstNameAuthor()?> <?=$reply->getLastNameAuthor()?></span>
                            </div>
                        </a>
                        <?php

                        $uniqueParticipants[] = $reply->getIdAuthor();
                    }
                }
            } else {
                echo "<p>Il n'y a aucun participant</p>";
            }
            ?>
        </div>
    </div>

    <?php
    if ($replies && count($replies) > 0) {
        echo '<hr>';

        echo "<div id='replies'>";
    
            $uniqueParticipants = [];
            foreach ($replies as $reply) {
                if (!in_array($reply->getIdAuthor(), $uniqueParticipants)) {
                    $anchor = !in_array($reply->getIdAuthor(), $uniqueParticipants) ? 'id="replyAnchor' . $reply->getIdAuthor() . '"' : "";
                    $uniqueParticipants[] = $reply->getIdAuthor();
                }
                ?>

                <div class="container" <?=$anchor?>>
                    <div class="userDescription">
                        <?php
                        if ($reply->getPseudoAuthor() !== null) {
                            if ($reply->getAuthorIsAdmin() || $reply->getAuthorIsModerator()) {
                                $userColor = '#de522f';
                            } else {
                                $userColor = '#CF8B3F';
                            }
                            ?>

                            <a href="index.php?action=userProfile&userId=<?=$reply->getIdAuthor()?>" style="background-image: url('<?=$reply->getProfilePictureAuthor()?>');"></a>

                            <div>
                                <a href="index.php?action=userProfile&userId=<?=$reply->getIdAuthor()?>" style="color:<?=$userColor?>;">
                                    <span><?=$reply->getPseudoAuthor()?></span>
                                </a>

                                <a href="index.php?action=userProfile&userId=<?=$reply->getIdAuthor()?>" style="color:<?=$userColor?>;">
                                    <span><?=$reply->getFirstNameAuthor()?> <?=$reply->getLastNameAuthor()?></span>
                                </a>
                            </div>
                            <?php
                        } else {
                            echo "<p>Compte supprimé</p>";
                        }
                        ?>
                    </div>

                    <?php
                    if (($user->getIsAdmin() || $user->getIsModerator()) || $reply->getIdAuthor() === $user->getId()) {
                        ?>
                        <div class="replyOption">
                            <i class="fas fa-trash" title="Supprimer cette réponse" replyId="<?=$reply->getId()?>"></i>
                            <?php
                            if ($user->getIsAdmin() || $user->getIsModerator() || !$topic->getIsClose()) {
                                ?>
                                <a href="index.php?action=editReply&replyId=<?=$reply->getId()?>">
                                    <i class="fas fa-pen" title="Modifier cette réponse"></i>
                                </a>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>

                    <div class="reply">
                        <?=$reply->getContent()?>
                        <p><?=$reply->getDatePublication()?></p>
                    </div>
                </div>
                <?php
            }
        echo "</div>";
    }
    
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

                    echo '<a href="index.php?action=forumTopic&topicId=' . $topic->getId() . '&page=' . ($i+1) . '&offset=' . $offset . '">' . ($i+1) . '</a></li>';
                }

                if ($data['nbPage'] > 10) {
                    echo '<li>...</li>';
                }

                echo '<li><a href="index.php?action=forumTopic&topicId=' . $topic->getId() . '&page=' . $data['nbPage'] . '&offset=' . ($data['nbPage']-1)*$data['nbElemByPage'] . '">' . $data['nbPage'] . '</a></li>';
            } else {
                //first page
                echo '<li><a href="index.php?action=forumTopic&topicId=' . $topic->getId() . '&page=1&offset=0">1</a></li>';
                echo '<li>...</li>';

                //2 page before actual
                echo '<li><a href="index.php?action=forumTopic&topicId=' . $topic->getId() . '&page=' . ($actualPage-2) . '&offset=' . ($actualPage-3)*$data['nbElemByPage'] . '">' . ($actualPage-2) . '</a></li>';
                echo '<li><a href="index.php?action=forumTopic&topicId=' . $topic->getId() . '&page=' . ($actualPage-1) . '&offset=' . ($actualPage-2)*$data['nbElemByPage'] . '">' . ($actualPage-1) . '</a></li>';

                //actual page
                echo '<li class="actualPage"><a href="index.php?action=forumTopic&topicId=' . $topic->getId() . '&page=' . ($actualPage) . '&offset=' . ($actualPage-1)*$data['nbElemByPage'] . '">' . ($actualPage) . '</a></li>';

                //2 page after actual
                if ($actualPage+1 < $data['nbPage']) {
                    echo '<li><a href="index.php?action=forumTopic&topicId=' . $topic->getId() . '&page=' . ($actualPage+1) . '&offset=' . ($actualPage)*$data['nbElemByPage'] . '">' . ($actualPage+1) . '</a></li>';
                }

                if ($actualPage+2 < $data['nbPage']) {
                    echo '<li><a href="index.php?action=forumTopic&topicId=' . $topic->getId() . '&page=' . ($actualPage+2) . '&offset=' . ($actualPage+1)*$data['nbElemByPage'] . '">' . ($actualPage+2) . '</a></li>';
                }

                //last page
                if ($actualPage+2 < $data['nbPage']-1) {
                    echo '<li>...</li>';
                }

                if ($actualPage < $data['nbPage']) {
                    echo '<li><a href="index.php?action=forumTopic&topicId=' . $topic->getId() . '&page=' . $data['nbPage'] . '&offset=' . ($data['nbPage']-1)*$data['nbElemByPage'] . '">' . $data['nbPage'] . '</a></li>';
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

                echo '<a href="index.php?action=forumTopic&topicId=' . $topic->getId() . '&page=' . ($i+1) . '&offset=' . $offset . '">' . ($i+1) . '</a></li>';
            }
        }

        echo '</ol>';
    echo '</nav>';
    ?>

    <hr>

    <?php
    if ($data['canReply']) {
        ?>
        <aside id="addReply">
            <div class="container">
                <div class="userDescription">
                    <?php
                    if ($user !== null) {
                        if ($user->getIsAdmin() || $user->getIsModerator()) {
                            $userColor = '#de522f';
                        } else if ($user->getSchool() !== NO_SCHOOL) {
                            $userColor = '#CF8B3F';
                        } else {
                            $userColor = '#b0a396';
                        }
                        ?>

                        <a href="index.php?action=userProfile&userId=<?=$user->getId()?>" style="background-image: url('<?=$user->getProfilePicture()?>');"></a>

                        <div>
                            <a href="index.php?action=userProfile&userId=<?=$user->getId()?>" style="color:<?=$userColor?>;">
                                <span><?=$user->getPseudo()?></span>
                            </a>

                            <a href="index.php?action=userProfile&userId=<?=$user->getId()?>" style="color:<?=$userColor?>;">
                                <span><?=$user->getFirstName()?> <?=$user->getLastName()?></span>
                            </a>
                        </div>
                        <?php
                    }
                    ?>
                </div>

                <form id="formAddReply" method="POST" action="index.php?action=addReply&topicId=<?=$topic->getId()?>" enctype="multipart/form-data">
                    <div id="blockTinyMce">
                        <h2>Ajouter une réponse</h2>
                        <textarea name="tinyMCEtextarea" id="tinyMCEtextarea"></textarea>
                    </div>

                    <div id="blockSubmit">
                        <span id="errorMsg"></span>
                        <input type="submit" name="submit" value="Publier">
                    </div>
                </form>
            </div>
        </aside>
        <?php
    }
    ?>
</section>

<div id="modal">
    <div id="confirmDeleteReply">
        <p>Supprimer définitivement la réponse ?</p>

        <div>
            <span class="closeModal">Annuler</span>
            <a href="" class="button" title="Supprimer la réponse">Confirmer</a>
        </div>
    </div>

    <div id="confirmDeleteTopic">
        <p>Supprimer définitivement le fil de discution ?</p>

        <div>
            <span class="closeModal">Annuler</span>
            <a href="" class="button" title="Supprimer le sujet">Confirmer</a>
        </div>
    </div>
</div>