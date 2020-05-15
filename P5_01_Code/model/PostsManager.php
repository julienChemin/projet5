<?php
namespace Chemin\ArtSchool\Model;
use Chemin\ArtSchool\Model\AbstractManager;

class PostsManager extends AbstractManager
{
	public static $OBJECT_TYPE = 'Chemin\ArtSchool\Model\Post';
	public static $TABLE_NAME = 'as_posts';
	public static $TABLE_COMMENTS = 'as_comments';
	public static $TABLE_PK = 'id';
	public static $TABLE_CHAMPS ='id, idAuthor, title, filePath, urlVideo, description, DATE_FORMAT(datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication, 
							isPrivate, authorizedGroups, postType, fileType, onFolder, tags';
	public static $TABLE_CHAMPS_WITH_COMMENTS ='a.id, a.idAuthor, a.title, a.filePath, a.urlVideo, a.description, DATE_FORMAT(a.datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication, 
							a.isPrivate, a.authorizedGroups, a.postType, a.fileType, a.onFolder, a.tags, c.id AS idComment, c.idPost AS commentIdPost, c.idAuthor AS commentIdAuthor, 
							c.content AS commentContent, DATE_FORMAT(c.datePublication, "%d/%m/%Y à %H:%i.%s") AS commentDatePublication, c.nbReport AS commentNbReport';

	public function getOneById(int $id)
	{
		if ($id > 0) {
			$q = $this->sql('SELECT ' . static::$TABLE_CHAMPS_WITH_COMMENTS . ' 
							FROM ' . static::$TABLE_NAME . ' AS a 
							INNER JOIN ' . static::$TABLE_COMMENTS . ' AS c 
							ON a.id = c.idPost 
							WHERE a.id = :id', 
							[':id' => $id]);
			$result = $q->fetch();
			$post = new Post($result);
			$arrComments = [];
			do {
				$comment = new Comment();
				$comment->setId($result['idComment'])->setIdPost($result['commentIdPost'])->setIdAuthor($result['commentIdAuthor'])->setContent($result['commentContent'])->setDatePublication($result['commentDatePublication'])->setNbReport($result['commentNbReport']);
				array_unshift($arrComments, $comment);
			} while ($result = $q->fetch());
			$post->setComments($arrComments);
			$q->closeCursor();
			return $post;
		}
	}

	public function getPostsByAuthor(int $idAuthor)
	{
		if ($idAuthor > 0) {
			$q = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
							FROM ' . static::$TABLE_NAME . ' 
							WHERE idAuthor = :idAuthor', 
							[':idAuthor' => $idAuthor]);
			$result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
			
			$q->closeCursor();
			return $result;
		}
	}

	public function set(Post $Post)
	{
		$this->sql('INSERT INTO ' . static::$TABLE_NAME . ' (idAuthor ,title, filePath, urlVideo, description, isPrivate, authorizedGroups, postType, 
					fileType, onFolder, tags, datePublication) 
					VALUES(:idAuthor, :title, :filePath, :urlVideo, :description, :isPrivate, :authorizedGroups, :postType, :fileType, :onFolder, :tags, NOW())', 
					[':idAuthor' => $Post->getIdAuthor(), ':title' => $Post->getTitle(), ':filePath' => $Post->getFilePath(), ':urlVideo' => $Post->getUrlVideo(), 
					':description' => $Post->getDescription(), ':isPrivate' => intval($Post->getIsPrivate()), ':authorizedGroups' => $Post->getAuthorizedGroups(), 
					':postType' => $Post->getPostType(), ':fileType' => $Post->getFileType(), ':onFolder' => $Post->getOnFolder(), ':tags' => $Post->getTags()]);
		return $this;
	}

	public function update(Post $Post)
	{
		$this->sql('UPDATE ' . static::$TABLE_NAME . ' 
					SET title = :title, filePath = :filePath, urlVideo = :urlVideo, description = :description, isPrivate = :isPrivate, 
					authorizedGroups = :authorizedGroups, postType = :postType, fileType = :fileType, onFolder = :onFolder, tags = :tags 
					WHERE id = :id', 
					[':title' => $Post->getTitle(), ':filePath' => $Post->getFilePath(), ':urlVideo' => $Post->getUrlVideo(), ':description' => $Post->getDescription(), 
					':isPrivate' => intval($Post->getIsPrivate()), ':authorizedGroups' => $Post->getAuthorizedGroups(), ':postType' => $Post->getPostType(), 
					':fileType' => $Post->getFileType(), ':onFolder' => $Post->getOnFolder(), ':tags' => $Post->getTags(), ':id' => $Post->getId()]);
		return $this;
	}

	public function deletePost(int $postId)
	{
		if ($postId > 0) {
			$TagsManager = new TagsManager();
			$CommentsManager = new CommentsManager();
			$post = $this->getOneById($postId);
			$tags = $post->getListTags();
			foreach ($tags as $tag) {
				$TagsManager->decrementQuantity($tag);
			}
			$comments = $post->getComments();
			foreach ($comments as $comment) {
				$CommentsManager->delete($comment->getId());
			}
			$this->delete($post->getId());
		}
	}

	public function canUploadPost(array $arrPOST, $folder, TagsManager $TagsManager)
	{
		if (!empty($arrPOST['fileTypeValue']) && $this->checkForScriptInsertion([$arrPOST])) {
			//check list tag
			if (!empty($arrPOST['listTags'])) {
				$listTags = explode(',', $arrPOST['listTags']);
				array_shift($listTags);
				if (!$TagsManager->tagsAreValide($listTags)) {
					return false;
				}
			}
			//check privacy
			if ($arrPOST['uploadType'] === "private" && !empty($arrPOST['listTags'])) {
				return false;
			}
			//check folder
			if (!empty($folder) && !$this->folderBelongsToUser(intval($folder), $_SESSION['id'])) {
				return false;
			}
			//check $_post
			switch ($arrPOST['fileTypeValue']) {
				case 'image':
					if (empty($_FILES) || empty($arrPOST['listTags'])) {
						return false;
					}
				break;
				case 'video':
					if (empty($arrPOST['videoLink']) || empty($arrPOST['listTags'])) {
						return false;
					}
				break;
				case 'compressed':
					if (empty($_FILES) || empty($arrPOST['title'])) {
						return false;
					}
				break;
				case 'folder':
					if (empty($arrPOST['title'])) {
						return false;
					}
				break;
			}
		} else {return false;}
		return true;
	}

	public function uploadPost(array $arrPOST, $folder, $schoolPost = false, $isPrivate = false, $authorizedGroups = null)
	{	
		empty($folder) ? $folder = null : $folder = intval($folder);
		switch ($arrPOST['fileTypeValue']) {
			case 'image':
				$arrAcceptedExtention = array("jpeg", "jpg", "png", "gif");
				require('view/upload.php');
				if (!empty($final_path)) {
					$this->set(new Post(['idAuthor' => $_SESSION['id'], 
										'title' => $arrPOST['title'], 
										'filePath' => $final_path, 
										'description' => $arrPOST['tinyMCEtextarea'], 
										'isPrivate' => $isPrivate, 
										'authorizedGroups' => $authorizedGroups, 
										'postType' => 'userPost', 
										'fileType' => $arrPOST['fileTypeValue'], 
										'onFolder' => $folder, 
										'tags' => $arrPOST['listTags']]));
					return true;
				} else {return false;}
			break;
			case 'video':
				$filePath = null;
				if (!empty($_FILES)) {
					$arrAcceptedExtention = array("jpeg", "jpg", "png", "gif");
					require('view/upload.php');
					if (!empty($final_path)) {
						$filePath = $final_path;
					} else {return false;}
				}
				$this->set(new Post(['idAuthor' => $_SESSION['id'], 
									'title' => $arrPOST['title'], 
									'filePath' => $filePath, 
									'urlVideo' => $arrPOST['videoLink'], 
									'description' => $arrPOST['tinyMCEtextarea'], 
									'isPrivate' => $isPrivate, 
									'authorizedGroups' => $authorizedGroups, 
									'postType' => 'userPost', 
									'fileType' => $arrPOST['fileTypeValue'], 
									'onFolder' => $folder, 
									'tags' => $arrPOST['listTags']]));
				return true;
			break;
			case 'compressed':
				if ($schoolPost) {
					$arrAcceptedExtention = array("zip", "rar");
					require('view/upload.php');
					if (!empty($final_path)) {
						$this->set(new Post([
							'idAuthor' => $_SESSION['id'], 
							'title' => $arrPOST['title'], 
							'filePath' => $final_path, 
							'description' => $arrPOST['tinyMCEtextarea'], 
							'isPrivate' => $isPrivate, 
							'authorizedGroups' => $authorizedGroups, 
							'postType' => 'schoolPost', 
							'fileType' => $arrPOST['fileTypeValue'], 
							'onFolder' => $folder]));
						return true;
					} else {return false;}
				} else {return false;}
			break;
			case 'folder':
				$filePath = null;
				if (!empty($_FILES)) {
					$arrAcceptedExtention = array("jpeg", "jpg", "png", "gif");
					require('view/upload.php');
					if (!empty($final_path)) {
						$filePath = $final_path;
					} else {return false;}
				}
				$this->set(new Post(['idAuthor' => $_SESSION['id'], 
									'title' => $arrPOST['title'], 
									'filePath' => $filePath,  
									'description' => $arrPOST['tinyMCEtextarea'], 
									'isPrivate' => $isPrivate, 
									'authorizedGroups' => $authorizedGroups, 
									'postType' => 'userPost', 
									'fileType' => $arrPOST['fileTypeValue'], 
									'onFolder' => $folder]));
				return true;
			break;
		}
	}

	public function folderBelongsToUser(int $idFolder, int $idUser)
	{
		if ($idFolder > 0 && $idUser > 0) {
			$q = $this->sql('SELECT * 
							FROM ' . static::$TABLE_NAME . ' 
							WHERE id = :idFolder AND idAuthor = :idUser AND fileType = :fileType', 
							[':idFolder' => $idFolder, ':idUser' => $idUser, ':fileType' => 'folder']);
			if ($q->fetch()) {
				return true;
			} else {return false;}
		}
	}
}
