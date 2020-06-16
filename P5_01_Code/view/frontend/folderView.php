<?php
$post = $data['post'];
$author = $data['author'];
$user = $data['user'];
$post->getPostType() === 'schoolPost' ? $postIsSchoolPost = true : $postIsSchoolPost = false;
!empty($user) && $post->getIdAuthor() === intval($user->getId()) ? $userIsAuthor = true : $userIsAuthor = false;
!empty($_SESSION['grade']) && $_SESSION['grade'] === ADMIN ? $userIsAdmin = true : $userIsAdmin = false;
!empty($_SESSION['grade']) && $_SESSION['grade'] === MODERATOR ? $userIsModerator = true : $userIsModerator = false;
?>
<section id="viewFolder" class="container">
	<article class="fullWidth">
		<div id="blockDescription" class="fullWidth">
			<?php
			if (!empty($post->getTitle())) {
				echo '<h1>' . $post->getTitle() . '</h1>';
			}
			if (!empty($post->getDescription())) {
				echo '<div>' . $post->getDescription() . '</div>';
			}
			echo '<span>Publié le ' . $post->getDatePublication() . ' </span>';
			?>
		</div>
		<section>
			<div></div>
		</section>
		<hr>
	</article>
	<?php
	if (!empty($post->getOnFolder())) {
		echo '<div class="postIsOnFolder">Cette publication fait parti d\'un dossier - <a href="index.php?action=post&id=' . $post->getOnFolder() . '">consulter</a></div>';
	}
	?>
	<section id="commentsAndRelatedPosts">
		<div id="blockComments">
			<form id="addComment">
				<?php
				if (!empty($user)) {
					?>
					<input type="hidden" name="userId" value="<?=$user->getId()?>">
					<input type="hidden" name="userName" value="<?=$user->getName()?>">
					<input type="hidden" name="userPicture" value="<?=$user->getProfilePicture()?>">
					<span id="msgComment"></span>
					<textarea wrap="hard" name="commentContent" placeholder="Ajouter un commentaire"></textarea>
					<input type="hidden" name="idPost" value="<?=$_GET['id']?>">
					<input type="button" name="submitComment" id="submitComment" value="Ajouter">
					<?php
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
									if (!empty($_SESSION['id']) && (intval($_SESSION['id']) === $comment->getIdAuthor() || $_SESSION['school'] === ALL_SCHOOL)) {
										echo ' - <span class="deleteComment" idcomment="' . $comment->getId() . '">Supprimer le commentaire</span><span class="confirmDelete">Supprimer définitivement ?</span>';
									}
									?>
								</p>
								<?php
								if (empty($_SESSION['id']) || intval($_SESSION['id']) !== $comment->getIdAuthor()) {
									echo '<a href="index.php?action=report&elem=comment&id=' . $comment->getId() . '&idPost=' . $post->getId() . '" title="Signaler le commentaire" class="reportComment"><i class="far fa-flag"></i></a>';
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
		<aside>
			<div id="optionList">
				<nav>
					<ul>
						<?php
						if (!empty($user)) {
							echo '<li id="heart"><i class="far fa-heart" idpost="' . $post->getId() . '"></i></li>';
						}
						echo '<li id="nbLike"><span><span>' . $post->getNbLike() . '</span><i class="fas fa-heart"></i></span></li>';
						if (!empty($user) && ($userIsAuthor || ($postIsSchoolPost && $post->getSchool() === $user->getSchool() && ($userIsAdmin || $userIsModerator || $post->getAuthorizedGroups() === null || in_array($user->getSchoolGroup(), $post->getAuthorizedGroups()))))) {
							echo '<li id="postOnFolder"><a href="index.php?action=addPost&folder=' . $post->getId() . '"><i class="fas fa-folder-plus"></i></a></li>';
						}
						if (!empty($user) && ($userIsAuthor || $_SESSION['school'] === ALL_SCHOOL || ($postIsSchoolPost && $post->getSchool() === $_SESSION['school'] && $userIsAdmin))) {
							echo '<li id="deletePost" title="Supprimer la publication"><i class="far fa-trash-alt"></i></li>';
							echo '<a href="index.php?action=deletePost&id=' . $post->getId() . '" id="confirmDeletePost" title="Supprimer la publication">Supprimer définitivement la publication ?</i></a>';
						} elseif (!empty($user)) {
							echo '<li title="Signaler"><a href="index.php?action=report&elem=post&id=' . $post->getId() . '"><i class="far fa-flag"></i></a></li>';
						}
						?>
					</ul>
				</nav>
			</div>
			<div id="authorProfile">
				<?php
				if ($author !== null) {
					?>
					<a href="index.php?action=userProfile&userId=<?=$author->getId()?>">
						<img src="<?=$author->getProfilePicture()?>">
					</a>
					<div>
						<a href="index.php?action=userProfile&userId=<?=$author->getId()?>">
							<span><?=$author->getName()?></span>
						</a>
						<a href="index.php?action=schoolProfile&school=<?=$author->getSchool()?>">
							<span><?=$author->getSchool()?></span>
						</a>
					</div>
					<?php
				} else {
					echo '<div>L\'auteur de cette publication n\'existe plus</div>';
				}
				?>
			</div>
		</aside>
	</section>
</section>
