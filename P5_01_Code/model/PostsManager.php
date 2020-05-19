<?php
namespace Chemin\ArtSchool\Model;
use Chemin\ArtSchool\Model\AbstractManager;

class PostsManager extends AbstractManager
{
	public static $OBJECT_TYPE = 'Chemin\ArtSchool\Model\Post';
	public static $TABLE_NAME = 'as_posts';
	public static $TABLE_COMMENTS = 'as_comments';
	public static $TABLE_PK = 'id';
	public static $TABLE_CHAMPS ='id, idAuthor, school, title, filePath, urlVideo, description, DATE_FORMAT(datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication, 
							isPrivate, authorizedGroups, postType, fileType, onFolder, tags';
	public static $TABLE_CHAMPS_WITH_COMMENTS ='a.id, a.idAuthor, a.school, a.title, a.filePath, a.urlVideo, a.description, DATE_FORMAT(a.datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication, a.isPrivate, a.authorizedGroups, a.postType, a.fileType, a.onFolder, a.tags, c.id AS idComment, c.idPost AS commentIdPost, c.idAuthor AS commentIdAuthor, c.content AS commentContent, DATE_FORMAT(c.datePublication, "%d/%m/%Y à %H:%i.%s") AS commentDatePublication, c.nbReport AS commentNbReport';

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

	public function getPostsByAuthor(int $idAuthor, int $offset = 0, int $limit = null)
	{
		if ($idAuthor > 0) {
			if (!empty($limit)) {
				$q = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
								FROM ' . static::$TABLE_NAME . ' 
								WHERE idAuthor = :idAuthor AND postType = "userPost" AND isPrivate = "0" 
								ORDER BY id DESC 
								LIMIT :limit OFFSET :offset', 
								[':idAuthor' => $idAuthor, ':offset' => $offset, ':limit' => $limit]);
			} else {
				$q = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
								FROM ' . static::$TABLE_NAME . ' 
								WHERE idAuthor = :idAuthor AND postType = "userPost" AND isPrivate = "0" 
								ORDER BY id DESC', 
								[':idAuthor' => $idAuthor]);
			}
			$result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
			
			$q->closeCursor();
			return $result;
		}
	}

	public function getPostsBySchool(string $school, bool $withFolder = false, int $offset = 0, int $limit = null)
	{
		if (strlen($school) > 0) {
			if ($withFolder) {
				if (!empty($limit)) {
					$q = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
									FROM ' . static::$TABLE_NAME . ' 
									WHERE school = :school AND postType = "userPost" AND onFolder = null AND isPrivate = "0" 
									ORDER BY id DESC 
									LIMIT :limit OFFSET :offset', 
									[':school' => $school, ':offset' => $offset, ':limit' => $limit]);
				} else {
					$q = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
									FROM ' . static::$TABLE_NAME . ' 
									WHERE school = :school AND postType = "userPost" AND onFolder = null AND isPrivate = "0" 
									ORDER BY id DESC', 
									[':school' => $school]);
				}
			} else {
				if (!empty($limit)) {
					$q = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
									FROM ' . static::$TABLE_NAME . ' 
									WHERE school = :school AND postType = "userPost" AND fileType != "folder" AND isPrivate = "0" 
									ORDER BY id DESC 
									LIMIT :limit OFFSET :offset', 
									[':school' => $school, ':offset' => $offset, ':limit' => $limit]);
				} else {
					$q = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
									FROM ' . static::$TABLE_NAME . ' 
									WHERE school = :school AND postType = "userPost" AND fileType != "folder" AND isPrivate = "0" 
									ORDER BY id DESC', 
									[':school' => $school]);
				}
			}
			$result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
			
			$q->closeCursor();
			return $result;
		}
	}

	public function getSchoolPosts(string $school)
	{
		if (strlen($school)) {
			$q = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
							FROM ' . static::$TABLE_NAME . ' 
							WHERE school = :school AND postType = "schoolPost" 
							ORDER BY id DESC', 
							[':school' => $school]);
			$result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
			
			$q->closeCursor();
			return $result;
		}
	}

	public function set(Post $Post)
	{
		$this->sql('INSERT INTO ' . static::$TABLE_NAME . ' (idAuthor, school ,title, filePath, urlVideo, description, isPrivate, authorizedGroups, postType, 
					fileType, onFolder, tags, datePublication) 
					VALUES(:idAuthor, :school, :title, :filePath, :urlVideo, :description, :isPrivate, :authorizedGroups, :postType, :fileType, :onFolder, :tags, NOW())', 
					[':idAuthor' => $Post->getIdAuthor(), ':school' => $Post->getSchool(), ':title' => $Post->getTitle(), ':filePath' => $Post->getFilePath(), ':urlVideo' => $Post->getUrlVideo(), 
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

	public function toArray(Post $post)
	{
		$arr = ['id' => $post->getId(),
				'idAuthor' => $post->getIdAuthor(), 
				'school' => $post->getSchool(), 
				'title' => $post->getTitle(), 
				'filePath' => $post->getFilePath(), 
				'urlVideo' => $post->getUrlVideo(), 
				'description' => $post->getDescription(), 
				'datePublication' => $post->getDatePublication(), 
				'isPrivate' => $post->getIsPrivate(), 
				'listAuthorizedGroups' => $post->getListAuthorizedGroups(), 
				'postType' => $post->getPostType(), 
				'fileType' => $post->getFileType(), 
				'onFolder' => $post->getOnFolder(),
				'listTags' => $post->getListTags()];
		return $arr;
	}

	public function canUploadPost(array $arrPOST, TagsManager $TagsManager)
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
			//check title length
			if (!empty($arrPOST['title']) && strlen($arrPOST['title']) > 30) {
				return false;
			}
			//check privacy
			if ($arrPOST['uploadType'] === "private" && !empty($arrPOST['listTags'])) {
				return false;
			}
			//check folder
			if (!empty($arrPOST['folder']) && !$this->folderBelongsToUser(intval($arrPOST['folder']), $_SESSION['id'])) {
				return false;
			}
			//check $_post
			switch ($arrPOST['fileTypeValue']) {
				case 'image':
					if (empty($_FILES['uploadFile']) || (empty($arrPOST['listTags']) && $arrPOST['uploadType'] === 'public' && $arrPOST['postType'] === 'userPost'
					&& $arrPOST['isStudent'] === 'true')) {
						return false;
					}
				break;
				case 'video':
					if (empty($arrPOST['videoLink']) || (empty($arrPOST['listTags']) && $arrPOST['uploadType'] === 'public' && $arrPOST['postType'] === 'userPost'
					&& $arrPOST['isStudent'] === 'true')) {
						return false;
					}
				break;
				case 'compressed':
					if (empty($_FILES['uploadFile']) || empty($arrPOST['title'])) {
						return false;
					}
				break;
				case 'folder':
					if (empty($arrPOST['title'])) {
						return false;
					}
				break;
				default :
					return false;
			}
		} else {return false;}
		return true;
	}

	public function uploadPost(array $arrPOST, $schoolPost = false, $isPrivate = false, $authorizedGroups = null)
	{	
		empty($arrPOST['folder']) ? $folder = null : $folder = intval($arrPOST['folder']);
		switch ($arrPOST['fileTypeValue']) {
			case 'image':
				$arrAcceptedExtention = array("jpeg", "jpg", "png", "gif");
				require('view/upload.php');
				if (!empty($final_path)) {
					$this->set(new Post(['idAuthor' => $_SESSION['id'], 
										'school' => $_SESSION['school'], 
										'title' => $arrPOST['title'], 
										'filePath' => $final_path, 
										'description' => $arrPOST['tinyMCEtextarea'], 
										'isPrivate' => $isPrivate, 
										'authorizedGroups' => $authorizedGroups, 
										'postType' => $arrPOST['postType'], 
										'fileType' => $arrPOST['fileTypeValue'], 
										'onFolder' => $folder, 
										'tags' => $arrPOST['listTags']]));
					return true;
				} else {return false;}
			break;
			case 'video':
				$filePath = null;
				if ($_FILES['uploadFile']['error'] === 0) {
					$arrAcceptedExtention = array("jpeg", "jpg", "png", "gif");
					require('view/upload.php');
					if (!empty($final_path)) {
						$filePath = $final_path;
					} else {return false;}
				}
				$this->set(new Post(['idAuthor' => $_SESSION['id'], 
									'school' => $_SESSION['school'], 
									'title' => $arrPOST['title'], 
									'filePath' => $filePath, 
									'urlVideo' => $arrPOST['videoLink'], 
									'description' => $arrPOST['tinyMCEtextarea'], 
									'isPrivate' => $isPrivate, 
									'authorizedGroups' => $authorizedGroups, 
									'postType' => $arrPOST['postType'], 
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
							'school' => $_SESSION['school'], 
							'title' => $arrPOST['title'], 
							'filePath' => $final_path, 
							'description' => $arrPOST['tinyMCEtextarea'], 
							'isPrivate' => $isPrivate, 
							'authorizedGroups' => $authorizedGroups, 
							'postType' => $arrPOST['postType'], 
							'fileType' => $arrPOST['fileTypeValue'], 
							'onFolder' => $folder]));
						return true;
					} else {return false;}
				} else {return false;}
			break;
			case 'folder':
				$filePath = null;
				if ($_FILES['uploadFile']['error'] === 0) {
					$arrAcceptedExtention = array("jpeg", "jpg", "png", "gif");
					require('view/upload.php');
					if (!empty($final_path)) {
						$filePath = $final_path;
					} else {return false;}
				}
				$this->set(new Post(['idAuthor' => $_SESSION['id'], 
									'school' => $_SESSION['school'], 
									'title' => $arrPOST['title'], 
									'filePath' => $filePath,  
									'description' => $arrPOST['tinyMCEtextarea'], 
									'isPrivate' => $isPrivate, 
									'authorizedGroups' => $authorizedGroups, 
									'postType' => $arrPOST['postType'], 
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

	public function sortForProfile($posts)
	{
		$arrSortedPosts = ['folder' => [], 'private' => [], 'public' => []];
		foreach ($posts as $post) {
			//sort post on folder, public post and private post
			$post = $this->toArray($post);
			if ($post['onFolder'] !== null) {
				$idPost = $post['onFolder'];
				if (!isset($arrSortedPosts['folder'][$idPost])) {
					$arrSortedPosts['folder'][$idPost] = [];
				}
				$arrSortedPosts['folder'][$idPost][] = $post;
			} elseif ($post['isPrivate'] === '1' && ($post['school'] === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL)) {
				if ($_SESSION['grade'] === MODERATOR || $_SESSION['grade'] === ADMIN || $_SESSION['id'] === $post->getIdAuthor() || $post['authorizedGroups'] === null 
				|| stristr($post['authorizedGroups'], $_SESSION['group']) !== false) {
					$arrSortedPosts['private'][] = $post;
				}
			} else {
				$arrSortedPosts['public'][] = $post;
			}
		}
		return $arrSortedPosts;
	}
}
