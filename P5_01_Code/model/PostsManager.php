<?php

namespace Chemin\ArtSchool\Model;

use Chemin\ArtSchool\Model\AbstractManager;

class PostsManager extends AbstractManager
{
	public static $OBJECT_TYPE = 'Chemin\ArtSchool\Model\Post';
	public static $TABLE_NAME = 'as_posts';
	public static $TABLE_PK = 'id';

	public static $TABLE_CHAMPS ='id, idAuthor, title, filePath, urlVideo, description, DATE_FORMAT(datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication, 
	isPrivate, postType, fileType, onFolder, tags';

	public function getOneById(int $id)
	{
		if ($id > 0) {
			$q = $this->sql(
				'SELECT a.id, a.idAuthor, a.title, a.filePath, a.urlVideo, a.description, DATE_FORMAT(a.datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication, a.isPrivate, a.postType, a.fileType, a.onFolder, a.tags, c.id AS idComment, c.idPost AS commentIdPost, c.idAuthor AS commentIdAuthor, c.content AS commentContent, DATE_FORMAT(c.datePublication, "%d/%m/%Y à %H:%i.%s") AS commentDatePublication, c.nbReport AS commentNbReport
				 FROM as_posts AS a
				 INNER JOIN as_comments AS c
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

	public function set(Post $Post)
	{
		$this->sql(
			'INSERT INTO ' . static::$TABLE_NAME . ' (idAuthor ,title, filePath, urlVideo, description, isPrivate, postType, fileType, onFolder, tags, datePublication) 
			VALUES(:idAuthor, :title, :filePath, :urlVideo, :description, :isPrivate, :postType, :fileType, :onFolder, :tags, NOW())',
			[':idAuthor' => $Post->getIdAuthor(), ':title' => $Post->getTitle(), ':filePath' => $Post->getFilePath(), ':urlVideo' => $Post->getUrlVideo(), ':description' => $Post->getDescription(), ':isPrivate' => intval($Post->getIsPrivate()), ':postType' => $Post->getPostType(), ':fileType' => $Post->getFileType(), ':onFolder' => $Post->getOnFolder(), ':tags' => $Post->getTags()]);

		return $this;
	}

	public function update(Post $Post)
	{
		$this->sql(
			'UPDATE ' . static::$TABLE_NAME . ' 
			SET title = :title, filePath = :filePath, urlVideo = :urlVideo, description = :description, isPrivate = :isPrivate, postType = :postType, fileType = :fileType, onFolder = :onFolder, tags = :tags
			WHERE id = :id',
			[':title' => $Post->getTitle(), ':filePath' => $Post->getFilePath(), ':urlVideo' => $Post->getUrlVideo(), ':description' => $Post->getDescription(), ':isPrivate' => intval($Post->getIsPrivate()), ':postType' => $Post->getPostType(), ':fileType' => $Post->getFileType(), ':onFolder' => $Post->getOnFolder(), ':tags' => $Post->getTags(), ':id' => $Post->getId()]);

		return $this;
	}

	public function folderBelongsToUser(int $idFolder, int $idUser)
	{
		if ($idFolder > 0 && $idUser > 0) {
			$q = $this->sql(
				'SELECT * 
				FROM ' . static::$TABLE_NAME . ' 
				WHERE id = :idFolder AND idAuthor = :idUser AND fileType = :fileType', 
				[':idFolder' => $idFolder, ':idUser' => $idUser, ':fileType' => 'folder']);

			if ($q->fetch()) {
				return true;
			} else {
				return false;
			}
		}
	}
}
