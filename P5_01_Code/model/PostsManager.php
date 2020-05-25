<?php
namespace Chemin\ArtSchool\Model;
use Chemin\ArtSchool\Model\AbstractManager;

class PostsManager extends AbstractManager
{
	public static $OBJECT_TYPE = 'Chemin\ArtSchool\Model\Post';
	public static $TABLE_NAME = 'as_post';
	public static $TABLE_COMMENTS = 'as_comment';
	public static $TABLE_PK = 'id';
	public static $TABLE_CHAMPS ='id, idAuthor, school, title, filePath, urlVideo, description, DATE_FORMAT(datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication, isPrivate, authorizedGroups, postType, fileType, onFolder, tags';
	public static $TABLE_CHAMPS_WITH_COMMENTS ='a.id, a.idAuthor, a.school, a.title, a.filePath, a.urlVideo, a.description, DATE_FORMAT(a.datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication, a.isPrivate, a.authorizedGroups, a.postType, a.fileType, a.onFolder, a.tags, c.id AS idComment, c.idPost AS commentIdPost, c.idAuthor AS commentIdAuthor, c.NameAuthor AS commentNameAuthor, c.profilePictureAuthor AS commentProfilePictureAuthor, c.content AS commentContent, DATE_FORMAT(c.datePublication, "%d/%m/%Y à %H:%i.%s") AS commentDatePublication, c.nbReport AS commentNbReport';

	public function getOneById(int $id)
	{
		if ($id > 0) {
			$q = $this->sql('SELECT ' . static::$TABLE_CHAMPS_WITH_COMMENTS . ' 
							FROM ' . static::$TABLE_NAME . ' AS a 
							LEFT JOIN ' . static::$TABLE_COMMENTS . ' AS c 
							ON a.id = c.idPost 
							WHERE a.id = :id 
							ORDER BY commentDatePublication', 
							[':id' => $id]);
			$result = $q->fetch();
			$post = new Post($result);
			$arrComments = [];
			if ($result['idComment'] !== null) {
				do {
					$comment = new Comment();
					$comment->setId($result['idComment'])->setIdPost($result['commentIdPost'])->setIdAuthor($result['commentIdAuthor'])->setNameAuthor($result['commentNameAuthor'])->setProfilePictureAuthor($result['commentProfilePictureAuthor'])->setContent($result['commentContent'])->setDatePublication($result['commentDatePublication'])->setNbReport($result['commentNbReport']);
					array_unshift($arrComments, $comment);
				} while ($result = $q->fetch());	
			}
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
									WHERE school = :school AND postType = "userPost" AND onFolder IS NULL AND isPrivate = "0" 
									ORDER BY id DESC 
									LIMIT :limit OFFSET :offset', 
									[':school' => $school, ':offset' => $offset, ':limit' => $limit]);
				} else {
					$q = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
									FROM ' . static::$TABLE_NAME . ' 
									WHERE school = :school AND postType = "userPost" AND onFolder IS NULL AND isPrivate = "0" 
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
							WHERE school = :school AND (postType = "schoolPost" OR (postType = "userPost" AND onFolder != "null" AND tags IS NULL)) 
							ORDER BY id DESC', 
							[':school' => $school]);
			$result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);

			$q->closeCursor();
			return $result;
		}
	}

	public function getPostsOnFolder(int $idFolder)
	{
		if ($idFolder > 0) {
			$q = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
							FROM ' . static::$TABLE_NAME . ' 
							WHERE onFolder = :idFolder', 
							[':idFolder' => $idFolder]);
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
			$post = $this->getOneById($postId);
			if (!empty($post->getFilePath())) {
				unlink($post->getFilePath());
			}
			$this->delete($post->getId());
		}
	}

	public function deleteFolder(int $idFolder)
	{
		if ($idFolder > 0) {
			if ($this->exists($idFolder)) {
				$postsOnFolder = $this->getPostsOnFolder($idFolder);
				if (strlen($postsOnFolder) > 0) {
					foreach ($postsOnFolder as $post) {
						if ($post->getFileType() === 'folder') {
							$this->deleteFolder($post->getId());
						} else {
							$this->deletePost($post->getId());
						}
					}
				}
				$this->deletePost($idFolder);
			} 
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
			if (!empty($arrPOST['folder']) && !$this->canPostOnFolder($this->getOneById(intval($arrPOST['folder'])))) {
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
					if ($arrPOST['uploadType'] === 'public' || empty($_FILES['uploadFile']) || empty($arrPOST['title'])) {
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

	public function uploadPost(array $arrPOST, $schoolPost = false, $authorizedGroups = null)
	{	
		//set folder and privacy
		$arrPOST['uploadType'] === "private" ? $isPrivate = true : $isPrivate = false;
		if (!empty($arrPOST['folder'])) {
			$folder = $this->getOneById(intval($arrPOST['folder']));
			if ($arrPOST['uploadType'] === 'public' && $folder->getIsPrivate()) {
				$isPrivate = true;
				$folder = intval($arrPOST['folder']);
			} elseif ($arrPOST['uploadType'] === 'private' && !$folder->getIsPrivate()) {
				$folder = null;
			} else {
				$folder = intval($arrPOST['folder']);
			}
		} else {$folder = null;}
		!$isPrivate ? $authorizedGroups = null : $authorizedGroups = $authorizedGroups;
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

	public function canPostOnFolder(Post $post)
	{
		if (isset($_SESSION) && $post->getFileType() === 'folder') {
			if ($post->getIdAuthor() === $_SESSION['id']) {
				//folder belong to user
				return true;
			} elseif ($post->getSchool() === $_SESSION['school'] && ($_SESSION['grade'] === MODERATOR || $_SESSION['grade'] === ADMIN)) {
				//admin and moderator can post on school folder
				return true;
			} elseif ($post->getSchool() === $_SESSION['school'] && $post->getIsPrivate() && ($post->getListAuthorizedGroups() === null || in_array($_SESSION['group'], $post->getListAuthorizedGroups()))) {
				//user on this group can post in this folder
				return true;
			} else {return false;}
		} else {return false;}
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
				if ($_SESSION['grade'] === MODERATOR || $_SESSION['grade'] === ADMIN || $_SESSION['id'] === $post['idAuthor'] || $post['listAuthorizedGroups'] === null || in_array($_SESSION['group'], $post['listAuthorizedGroups'])) {
					$arrSortedPosts['private'][] = $post;
				}
			} else {
				$arrSortedPosts['public'][] = $post;
			}
		}
		return $arrSortedPosts;
	}
}
