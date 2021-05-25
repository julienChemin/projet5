<?php
$topic = $data['topicInfo']['topic'];
$replies = $data['topicInfo']['replies'];
$author = $data['topicAuthor'];
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
        <p class="linkHomeForum">
            <a href="index.php?action=forum&school=<?=$data['school']->getName()?>">
                <i class="fas fa-door-open"></i>Retourner sur l'accueil du forum
            </a>
        </p>

        <?php
        if ($data['user']->getIsAdmin() || $data['user']->getIsModerator() || $user->getId() === $topic->getIdAuthor()) {
            //TODO edit topic content
        }
        ?>
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
        <?php
        if ($replies && count($replies) > 0) {
            $uniqueParticipants = [];
            echo'<h2>Participants (sur cette page) : </h2>';
                echo '<div>';
                foreach ($replies as $reply) {
                    if (!in_array($reply->getIdAuthor(), $uniqueParticipants)) {//TODO add modal hover with user name
                        ?>
                        <div class="participant" href="#participant<?=$reply->getIdAuthor()?>" style="background-image:src('<?=$reply->getProfilePictureAuthor()?>')"></div>
                        <?php

                        $uniqueParticipants[] = $reply->getIdAuthor();
                    }
                }
                echo '</div>';
        }
        ?>
    </div>

    <div id="replies">
        <!--TODO-->
    </div>

    <div id="addReply">

    </div>
</section>
