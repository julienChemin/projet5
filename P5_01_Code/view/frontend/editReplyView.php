<?php
$reply = $data['reply'];
?>

<section id="createTopic">
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

    <article class="container">
        <form id="addForumTopic" method="POST" action="index.php?action=updateReply&replyId=<?=$reply->getId()?>" enctype="multipart/form-data">
            <div id="blockTinyMce">
                <h2>Contenu du sujet</h2>
                <textarea name="tinyMCEtextarea" id="tinyMCEtextarea"><?=$reply->getContent()?></textarea>
            </div>

            <div id="blockSubmit">
                <span id="errorMsg"></span>
                <input type="submit" name="submit" value="Publier">
            </div>
        </form>
    </article>
</section>
