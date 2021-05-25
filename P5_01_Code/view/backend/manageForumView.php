<?php
$categories = $data['forumInfo']['categories'];
$pinnedTopics = $data['forumInfo']['pinnedTopics'];
$nonePinnedTopics = $data['forumInfo']['nonePinnedTopics'];
$listSchoolGroups = $data['listSchoolGroups'];
?>

<section id="manageForum" schoolName = "<?=$data['school']->getName()?>">
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

    <h1>Forum <?=$data['school']->getName()?></h1>

    <p class="container">
        <a href="index.php?action=forum&school=<?=$data['school']->getName()?>">
            <i class="fas fa-door-open"></i>Retourner sur le forum
        </a>
    </p>

    <div id="addNewCategory" class="container">
        <h2>Créer une nouvelle catégorie<i class="fas fa-plus iconeEdit"></i></h2>

        <p class="categoryDescription">
            Les catégories vous permettent d'organiser votre forum en séparant les différents sujets par thème
        </p>

        <div id="addNewCategoryCheck">
            <i class="fas fa-check iconeEdit"></i> La nouvelle catégorie a été créée
        </div>
        
        <form id="addForumCategory">
            <input type="hidden" name="schoolName" value="<?=$data['school']->getName()?>">
            <input type="hidden" name="listAuthorizedGroupsToSee" value="">
            <input type="hidden" name="listAuthorizedGroupsToPost" value="">

            <p>
                <input type="text" name="newCategoryName" placeholder="Nom de la catégorie (lettres et chiffres uniquement)">
            </p>

            <textarea name="newCategoryDescription" id="newCategoryDescription" cols="10" rows="10" placeholder="Vous pouvez ajouter une description (lettres et chiffres uniquement)"></textarea>

            <div id="blockGroupsSelection" class="fullWidth">
                <div id="selectionGroupsToSee">
                    <div>
                        <label for="authorizedGroupsToSee">Qui peut voir la catégorie : </label>

                        <select name="authorizedGroupsToSee" id="authorizedGroupsToSee">
                            <option value="groups" selected></option>
                            <option value="all">Tous les Groupes</option>
                            <option value="none">Admins / Modérateurs</option>
                            <?php
                            if (!empty($listSchoolGroups) && count($listSchoolGroups) > 0) {
                                foreach ($listSchoolGroups as $group) {
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
                            if (!empty($listSchoolGroups) && count($listSchoolGroups) > 0) {
                                foreach ($listSchoolGroups as $group) {
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

            <div>
                <span id="cancelAddCategory">Annuler</span>
                <input type="button" value="Créer">
            </div>
        </form>
    </div>

    <article id="forumCategories">
        <h1 class="container" style="order:0">Gérer le forum</h1>

        <?php
        if ($categories && count($categories) > 0) {
            // display categories
            for ($i = 0; $i < count($categories); $i++) {
                ?>
                <section class="forumCategory container" style="order: <?=$i+1?>;">
                    <header>
                        <div class="changCategoryOrder">
                            <i class="fas fa-chevron-up"></i>
                            <i class="fas fa-chevron-down"></i>
                        </div>

                        <div>
                            <h2><?=$categories[$i]->getName()?></h2>

                            <?php
                            if ($categories[$i]->getDescription() !== null) {
                                echo '<p class="categoryDescription">' . $categories[$i]->getDescription() . '</p>';
                            }
                            ?>
                        </div>

                        <div class="deleteCategory">
                            <i class="fas fa-pencil-alt" categoryId="<?=$categories[$i]->getId()?>"></i>
                            <?php
                            if ($data['user']->getIsAdmin()) {
                                ?>
                                <i class="fas fa-trash" categoryId="<?=$categories[$i]->getId()?>"></i>
                                <?php
                            }
                            ?>
                        </div>
                    </header>

                    <div class="topics">
                        <?php
                        if ($pinnedTopics[$categories[$i]->getName()] && count($pinnedTopics[$categories[$i]->getName()]) > 0) {
                            foreach ($pinnedTopics[$categories[$i]->getName()] as $topic) {
                                ?>
                                <a href="index.php?action=forumTopic&topicId=<?=$topic->getId()?>" class="topic">
                                    <h3><?=$topic->getName()?></h3>
                                    <p><?=$topic->getAuthor()?>, <?=$topic->getDatePublication()?></p>
                                </a>
                                <?php
                            }
                        } else {
                            // 0 topic to display
                            ?>
                            <div class="blockStyleOne container">
                                Il n'y a aucun sujet d'ouvert pour l'instant
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </section>
                <?php
            }
        }
        ?>
    </article>
</section>

<div id="modal">
    <?php
    if ($_SESSION['grade'] === "admin") {
        ?>
        <div id="confirmDeleteCategory">
            <p>Supprimer définitivement la catégorie ? Tous les sujets faisant partie de cette catégorie seront aussi supprimés</p>

            <div>
                <span id="closeModalDelete" class="closeModal">Annuler</span>
                <a href="" title="Supprimer la catégorie">Confirmer</a>
            </div>
        </div>
        <?php
    }
    ?>

    <div id="confirmEditCategory">
        <form id="formEditCategory" method="POST" action="indexAdmin.php?action=editCategory" enctype="multipart/form-data">
            <input type="hidden" name="idCategory" value="">

            <div id="blockTitle">
                <h2>Ajoutez un titre</h2>
                <input type="text" name="title" id="title">
            </div>

            <div id="blockContent">
                <h2>Ajoutez une description</h2>
                <textarea name="content" id="content"></textarea>
            </div>
        </form>

        <div>
            <span id="closeModalEdit" class="closeModal">Annuler</span>
            <button id="btnConfirmEdit" title="Editer la catégorie">Confirmer</button>
        </div>
    </div>
</div>
