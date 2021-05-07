<section id="addPost" class="container">
    <h1>Publication</h1>

    <form method="POST" action="index.php?action=uploadPost&type=referenced" enctype="multipart/form-data">
        <input type="hidden" name="fileTypeValue" value="">
        <input type="hidden" name="folder" value="">
        <input type="hidden" name="fileCountOnGrouped" value="0">
        <input type="hidden" name="listTypeGroupedFile" value="">
        <input type="hidden" name="listTags" id="listTags" value="">

        <div id="blockUploadType">
            <figure id="btnAddFolder">
                <figcaption>Dossier</figcaption>
                <img src="public/images/folder.png" title="Publier un dossier" alt="Publier un dossier">
            </figure>

            <figure id="btnAddFile">
                <figcaption>Fichier</figcaption>
                <img src="public/images/file.png" title="Publier un fichier" alt="Publier un fichier">
            </figure>
        </div>

        <div id="blockAddFile">
            <div id="fileTypeSelection">
                <figure>
                    <input type="radio" name="fileType" id="typeImage" value="image">
                    <label for="typeImage">
                        <figcaption>Images</figcaption>
                        <img src="public/images/fileImage.png" title="Publier une image" alt="Publier une image">
                    </label>
                </figure>

                <figure>
                    <input type="radio" name="fileType" id="typeVideo" value="video">
                    <label for="typeVideo">
                        <figcaption>Vidéos</figcaption>
                        <img src="public/images/fileVideo.png" title="Publier une vidéo" alt="Publier une vidéo">
                    </label>
                </figure>

                <figure>
                    <input type="radio" name="fileType" id="typeGrouped" value="grouped">
                    <label for="typeGrouped">
                        <figcaption>Groupe de fichier</figcaption>
                        <img src="public/images/file.png" title="Publier plusieurs fichier" alt="Publier plusieurs fichier">
                    </label>
                </figure>
            </div>
        </div>

        <div id="blockTitle">
            <h2>Ajoutez un titre</h2>
            <input type="text" name="title" id="title">
        </div>

        <div id="blockTinyMce">
            <h2>Ajoutez une description</h2>
            <textarea name="tinyMCEtextarea" id="tinyMCEtextarea"></textarea>
        </div>

        <div id="blockUploadFile">
            <div>
                <div>
                    <label for="uploadFile">(max : 5Mo)</label>
                    <input type="hidden" name="MAX_FILE_SIZE" value="6000000">
                    <input type="file" name="uploadFile" id="uploadFile" accept=".jpeg, .jpg, .jfif, .png, .gif">
                </div>

                <figure id="preview" class="preview emptyPreview">
                    <img src="" title="preview" alt ="Aperçu">
                </figure>
            </div>
        </div>

        <div id="blockVideoLink">
            <div>
                <h2>Adresse de la vidéo youtube</h2>
                <input type="text" name="videoLink" id="videoLink">
            </div>
        </div>

        <div id="blockUploadGroupedFile">
            <hr>
            <div id="groupedFileTypeSelection">
                <span>
                    Cliquez sur l'icone correspondant pour ajouter un élément à la publication
                </span>

                <figure>
                    <input type="radio" name="groupedFileType" id="groupedTypeImage" value="image">
                    <label for="groupedTypeImage">
                        <figcaption>Images</figcaption>
                        <img src="public/images/fileImage.png" title="Publier une image" alt="Publier une image">
                    </label>
                </figure>

                <figure>
                    <input type="radio" name="groupedFileType" id="groupedTypeVideo" value="video">
                    <label for="groupedTypeVideo">
                        <figcaption>Vidéos</figcaption>
                        <img src="public/images/fileVideo.png" title="Publier une vidéo" alt="Publier une vidéo">
                    </label>
                </figure>
            </div>
        </div>

        <div id="blockTags">
            <hr>
            <h2>Ajoutez des tags</h2>
            <p>Les tags permettent de répertorier vos publications<br>Vous pouvez par exemple mettre la catégorie, le nom de votre établissement scolaire, etc...</p>
            <p>Privilégiez les tags déjà existant pour avoir une meilleure visibilité, mais rien ne vous empêche de créer le votre !</p>
            <p class="tagRules">Les tags ne peuvent contenir que des chiffres, des lettres et des espaces (30 caractères max.)</p>

            <div id="blockAddTags">
                <div>
                    <label for="tags">Entrez un tag : </label>
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
