<section id="addPost" class="container">
    <h1>Publication</h1>
    <form method="POST" action="index.php?action=uploadPost&type=unreferenced" enctype="multipart/form-data">
        <input type="hidden" name="fileTypeValue" value="">
        <input type="hidden" name="folder" value="">
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
            <hr>
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
                    <input type="hidden" name="MAX_FILE_SIZE" value="6000000">
                    <input type="file" name="uploadFile" id="uploadFile">
                </div>
                <figure id="preview">
                    <img src="" title="preview" alt ="Aperçu">
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
        <div id="blockSubmit">
            <span id="errorMsg"></span>
            <input type="submit" name="submit" value="Publier">
        </div>
    </form>
</section>
