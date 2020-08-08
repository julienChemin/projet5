<section id="addSchoolPost" class="container">
    <h1>Publication pour l'établissement</h1>
    <form method="POST" action="<?=$data['urlForm']?>" enctype="multipart/form-data">
        <input type="hidden" name="postType" value="schoolPost">
        <input type="hidden" name="uploadType" value="<?=$data['uploadType']?>">
        <input type="hidden" name="fileTypeValue" value="">
        <input type="hidden" name="folder" value="">
        <input type="hidden" name="listAuthorizedGroups" value="">
        <input type="hidden" name="listTags" id="listTags" value="">
        <input type="hidden" name="isStudent" id="isStudent" value="<?=$data['isStudent']?>">

        <div id="blockUploadType">
            <figure id="btnAddFolder">
                <figcaption>Dossier</figcaption>
                <img src="public/images/folder.png">
            </figure>
            <figure id="btnAddFile">
                <figcaption>Fichier</figcaption>
                <img src="public/images/schoolFile.png">
            </figure>
            <?php
            if ($data['isStudent'] === 'false') {
                ?>
                <div id="blockIsPrivate" class="fullWidth">
                    <div>
                        <div>
                            <input type="checkbox" name="isPrivate" id="isPrivate">
                            <label for="isPrivate">Publication privée</label>
                        </div>
                        <div>
                            <label for="listGroup">Qui peut voir la publication : </label>
                            <select id="listGroup" name="listGroup">
                                <option value="all">Tous les groupes</option>
                                <option value="" selected></option>
                                <?php
                                if (!empty($data['groups'])) {
                                    foreach ($data['groups'] as $group) {
                                        echo '<option value=' . $group . '>' . $group . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div id="authorizedGroups"></div>
                </div>
                <?php
            }
            ?>
        </div>
        <div id="blockAddFile">
            <hr>
            <div id="fileTypeSelection">
                <figure>
                    <input type="radio" name="fileType" id="typeImage" value="image">
                    <label for="typeImage">
                        <figcaption>Images</figcaption>
                        <img src="public/images/fileImage.png">
                    </label>
                </figure>
                <figure>
                    <input type="radio" name="fileType" id="typeVideo" value="video">
                    <label for="typeVideo">
                        <figcaption>Vidéos</figcaption>
                        <img src="public/images/fileVideo.png">
                    </label>
                </figure>
                <figure>
                    <input type="radio" name="fileType" id="typeOther" value="other">
                    <label for="typeOther">
                        <figcaption>Autres</figcaption>
                        <img src="public/images/fileOther.png">
                    </label>
                </figure>
            </div>
        </div>
        <div id="blockTitle">
            <hr>
            <h2>Ajoutez un titre</h2>
            <input type="text" name="title" id="title">
        </div>
        <div id="blockUploadFile">
            <hr>
            <div>
                <div>
                    <label for="uploadFile">(max : 5Mo)</label>
                    <input type="hidden" name="MAX_FILE_SIZE" value="5000000">
                    <input type="file" name="uploadFile" id="uploadFile">
                </div>
                <figure id="preview">
                    <img src="" title="preview">
                </figure>
            </div>
        </div>
        <div id="blockVideoLink">
            <hr>
            <h2>Adresse de la vidéo youtube</h2>
            <input type="text" name="videoLink" id="videoLink">
        </div>
        <div id="blockTinyMce">
            <hr>
            <h2>Ajoutez une description</h2>
            <textarea name="tinyMCEtextarea" id="tinyMCEtextarea"></textarea>
        </div>
        <div id="blockTags">
            <hr>
            <h2>Ajoutez des tags</h2>
            <p>Les tags permettent de répertorier vos publications<br>Vous pouvez par exemple mettre la catégorie (perspective, chara design, court métrage, etc...), le nom de votre établissement scolaire, etc...</p>
            <p>Privilégiez les tags déjà existant pour avoir une meilleure visibilité, mais rien ne vous empêche de créer le votre !</p>
            <p class="tagRules">Les tags ne peuvent contenir que des chiffres, des lettres et des espaces (30 caractères max.)</p>
            <div id="blockAddTags">
                <div>
                    <label  for="tags">Entrez un tag : </label>
                    <input type="text" name="tags" id="tags" autocomplete="off">
                    <button>Ajouter le tag</button>
                </div>
                <div id="selectedTags">
                    <h2>Tags sélectionné</h2>
                    <div></div>
                </div>
                <div id="recommendedTags">
                    <h2>Tags recommandé</h2>
                    <div></div>
                </div>
            </div>
            <hr>
        </div>
        <div id="blockSubmit">
            <span id="errorMsg"></span>
            <input type="submit" name="submit" value="Publier">
        </div>
    </form>
</section>
