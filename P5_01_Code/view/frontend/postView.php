<?php
$post = $data['post'];
$author = $data['author'];
$user = $data['user'];
?>
<section id="viewPost" class="container">
	<article class="fullWidth">
		<section>
			<aside id="authorProfile">
				<a href="index.php?action=userProfile&userId=<?=$author->getId()?>">
					<img src="<?=$author->getProfilePicture()?>">
				</a>
				<a href="index.php?action=userProfile&userId=<?=$author->getId()?>" class="<?=$data['author']->getProfileTextPseudo()?>">
					<span><?=$author->getName()?></span>
				</a>
				<a href="index.php?action=schoolProfile&school=<?=$author->getSchool()?>" class="<?=$data['author']->getProfileTextSchool()?>">
					<span><?=$author->getSchool()?></span>
				</a>
			</aside>
			<aside id="optionList">
				<nav>
					<ul>
						<li><i class="far fa-heart"></i></li>
						<?php
						if (!empty($_SESSION['id']) && $post->getIdAuthor() === intval($_SESSION['id'])) {
							echo '<li id="deletePost" title="Supprimer"><i class="far fa-trash-alt"></i></li>';
							echo '<a href="index.php?action=deletePost&id=' . $post->getId() . '" id="confirmDeletePost" title="Supprimer">Supprimer définitivement la publication ?</i></a>';
						} else {
							echo '<li title="Signaler"><i class="far fa-flag"></i></li>';
						}
						?>
					</ul>
				</nav>
			</aside>
		</section>
		<section>
			<?php
			switch ($post->getFileType()) {
				case 'image' :
					echo '<a href="' . $post->getFilePath() . '"><img class="fileImage" src="' . $post->getFilePath() . '"></a>';
				break;
				case 'video' :
					echo '<iframe width="90%" height="90%" src="https://www.youtube.com/embed/' . $post->getUrlVideo() . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
				break;
				case 'compressed' :
					echo '<a title="Cliquez pour télécharger" href="' . $post->getFilePath() . '"><i class="fas fa-download"><img class="fileCompressed" src="public/images/fileOther.png"></i></a>';
				break;
			}
			?>
		</section>
	</article>
	<?php
	if (!empty($post->getOnFolder())) {
		echo '<div class="postIsOnFolder">Cette publication fait parti d\'un dossier - <a href="index.php?action=post&id=' . $post->getOnFolder() . '">consulter</a></div>';
	}
	if ($post->getFileType() === 'compressed') {
		echo '<p id="warningMsg">Ne téléchargez un fichier seulement si vous savez d\'où il vient et ce qu\'il contient</p>';
	}
	?>
	<hr>
	<div id="blockDescription" class="fullWidth">
		<?php
		if (!empty($post->getTitle())) {
			echo '<h1>' . $post->getTitle() . '</h1>';
		}
		if (!empty($post->getDescription())) {
			echo '<div>' . $post->getDescription() . '</div>';
		}
		if ($post->getFileType() === 'video') {
			echo '<p id="directLink"><a href="https://www.youtube.com/watch?v=' . $post->getUrlVideo() . '">Voir la vidéo sur youtube</a></p>';
		}
		echo '<span>Publié le ' . $post->getDatePublication() . ' </span>';
		?>
	</div>
	<hr>
	<?php
	if (!empty($post->getlistTags())) {
		echo '<section id="listTags">';
		echo '<h2>Tags</h2>';
		echo '<aside>';
		foreach ($post->getlistTags() as $tag) {
			echo '<a class="tag" href="index.php?action=search&sortby=tag&value=' . $tag . '">' . $tag . '</a>';
		}
		echo '</aside></section>';
	}
	?>
	<section id="commentsAndRelatedPosts">
		<div id="blockComments">
			<form id="addComment">
				<?php
				if (!empty($user)) {
					echo '<input type="hidden" name="userId" value="' . $user->getId() . '">';
					echo '<input type="hidden" name="userName" value="' . $user->getName() . '">';
					echo '<input type="hidden" name="userPicture" value="' . $user->getProfilePicture() . '">';
					echo '<span id="msgComment"></span>';
					echo '<textarea wrap="hard" name="commentContent" placeholder="Ajouter un commentaire"></textarea>';
					echo '<input type="hidden" name="idPost" value="' . $_GET['id'] . '">';
					echo '<input type="button" name="submitComment" id="submitComment" value="Ajouter">';
				} else {
					echo '<p>Vous devez être connecté pour poster un commentaire</p>';
				}
				?>
				
			</form>
			<div class="fullWidth">
				<?php
				if (!empty($post->getComments())) {
					foreach ($post->getComments() as $comment) {
						?>
						<div class="comment fullWidth">
							<a href="index.php?action=userProfile&userId=<?=$comment->getIdAuthor()?>">
								<img src="<?=$comment->getProfilePictureAuthor()?>">
							</a>
							<div>
								<a href="index.php?action=userProfile&userId=<?=$comment->getIdAuthor()?>">
									<?=$comment->getNameAuthor()?>
								</a>
								<p>
									<?=nl2br($comment->getContent())?>
								</p>
								<p>
									<?=$comment->getDatePublication()?>
									<?php
									if (!empty($_SESSION['id']) && intval($_SESSION['id']) === $comment->getIdAuthor()) {
										echo ' - <span class="deleteComment" idcomment="' . $comment->getId() . '">Supprimer mon commentaire</span><span class="confirmDelete">Supprimer définitivement ?</span>';
									}
									?>
								</p>
								<?php
								if (empty($_SESSION['id']) || intval($_SESSION['id']) !== $comment->getIdAuthor()) {
									echo '<i title="Signaler" class="far fa-flag"></i>';
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
		<aside id="relatedPosts">
			<div></div>
		</aside>
	</section>
</section>
