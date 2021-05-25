<?php
$category = $data['category'];
if (!empty($data['listGroupsToSee'])) {
    $listGroupsToSee = $data['listGroupsToSee'];
    $listGroupsToPost = $data['listGroupsToPost'];
}
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

    <h1 class="container">Vous allez ouvrir un sujet dans la catégorie : <?=$category->getName()?></h1>

    <article class="container">
        <form id="addForumTopic" method="POST" action="index.php?action=addTopic&categoryId=<?=$category->getId()?>" enctype="multipart/form-data">
            <?php
            if ($data['user']->getIsAdmin() || $data['user']->getIsModerator()) {
                echo '<input type="hidden" name="listAuthorizedGroupsToSee" value="">';
                echo '<input type="hidden" name="listAuthorizedGroupsToPost" value="">';
            }
            ?>

            <div id="blockTitle">
                <h2>Titre du sujet</h2>
                <input type="text" name="title" id="title">
            </div>

            <div id="blockTinyMce">
                <h2>Contenu du sujet</h2>
                <textarea name="tinyMCEtextarea" id="tinyMCEtextarea"></textarea>
            </div>

            <?php
            if ($data['user']->getIsAdmin() || $data['user']->getIsModerator()) {
                ?>
                <p>
                    Si vous ne sélectionnez pas d'autorisations, celles de la catégorie seront appliqués à ce sujet
                </p>

                <div id="blockGroupsSelection" class="fullWidth">
                    <div id="selectionGroupsToSee">
                        <div>
                            <label for="authorizedGroupsToSee">Qui peut voir la catégorie : </label>

                            <select name="authorizedGroupsToSee" id="authorizedGroupsToSee">
                                <option value="groups" selected></option>
                                <option value="all">Tous les Groupes</option>
                                <option value="none">Admins / Modérateurs</option>
                                <?php
                                if (!empty($listGroupsToSee) && count($listGroupsToSee) > 0) {
                                    foreach ($listGroupsToSee as $group) {
                                        echo '<option value="' . $group . '">' . $group . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div id="blockAuthorizedGroupsToSee">

                        </div>
                    </div>

                    <div id="selectionGroupsToPost">
                        <div>
                            <label for="authorizedGroupsToPost">Qui peut créer un sujet dans la catégorie : </label>

                            <select name="authorizedGroupsToPost" id="authorizedGroupsToPost">
                                <option value="groups" selected></option>
                                <option value="all">Tous les Groupes</option>
                                <option value="none">Admins / Modérateurs</option>
                                <?php
                                if (!empty($listGroupsToPost) && count($listGroupsToPost) > 0) {
                                    foreach ($listGroupsToPost as $group) {
                                        echo '<option value="' . $group . '">' . $group . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div id="blockAuthorizedGroupsToPost">

                        </div>
                    </div>
                </div>
                <?php
            }
            ?>

            <div id="blockSubmit">
                <span id="errorMsg"></span>
                <input type="submit" name="submit" value="Publier">
            </div>
        </form>
    </article>
</section>
