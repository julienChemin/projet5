<section id="addPost" class="container">
	<h1>Publication</h1>
	<form method="POST" action="index.php?action=uploadPost" enctype="multipart/form-data">
		<input type="hidden" name="postType" value="userPost">
		<input type="hidden" name="uploadType" value="public">
		<input type="hidden" name="fileTypeValue" value="">
		<input type="hidden" name="folder" value="">
		<input type="hidden" name="listTags" id="listTags" value="">
		<?php
		if ($_SESSION['grade'] === STUDENT) {
			$isStudent = 'true';
		} else {$isStudent = 'false';}
		?>
		<input type="hidden" name="isStudent" id="isStudent" value="<?=$isStudent?>">

		<div id="blockUploadType">
			<figure id="btnAddFolder">
				<figcaption>Dossier</figcaption>
				<img src="public/images/folder.png">
			</figure>
			<figure id="btnAddFile">
				<figcaption>Fichier</figcaption>
				<img src="public/images/file.png">
			</figure>
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
