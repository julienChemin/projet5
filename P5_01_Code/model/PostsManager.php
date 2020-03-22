<?php

namespace Chemin\ArtSchool\Model;

use Chemin\ArtSchool\Model\AbstractManager;

class PostsManager extends AbstractManager
{
	public static $OBJECT_TYPE = 'Chemin\ArtSchool\Model\Post';
	public static $TABLE_NAME = 'as_posts';
	public static $TABLE_PK = 'id';
	public static $TABLE_CHAMPS ='id, idAuthor, imgPath, description, DATE_FORMAT(datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication';

	public function getOneById(int $id)
	{
		if ($id > 0) {
			$q = $this->sql(
				'SELECT a.id, a.idAuthor, a.imgPath, a.description, DATE_FORMAT(a.datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication, c.id AS commentId, c.idArticle AS commentIdArticle, c.content AS commentContent, c.nbReport AS commentNbReport, c.author AS commentAuthor, c.authorIsAdmin AS commentAuthorIsAdmin, c.authorEdit AS commentAuthorEdit, DATE_FORMAT(c.datePublication, "%d/%m/%Y à %H:%i.%s") AS commentDatePublication, DATE_FORMAT(c.dateEdit, "%d/%m/%Y à %H:%i.%s") AS commentDateEdit
				 FROM as_posts AS a
				 INNER JOIN as_comments AS c
				 ON a.id = c.idArticle
				 WHERE a.id = :id',
				 [':id' => $id]);

				$result = $q->fetch();

				$post = new Post($result);
				$arrComments = [];
				$comment = new Comment();
				$comment->setId($result['commentId'])->setIdArticle($result['commentIdArticle'])->setContent($result['commentContent'])->setNbReport($result['commentNbReport'])->setAuthor($result['commentAuthor'])->setAuthorIsAdmin($result['commentAuthorIsAdmin'])->setDatePublication($result['commentDatePublication']);
				if (isset($result['commentAuthorEdit'])) {
					$comment->setAuthorEdit($result['commentAuthorEdit'])->setDateEdit($result['commentDateEdit']);
				}
				array_unshift($arrComments, $comment);

				while ($result = $q->fetch()) {
					$comment = new Comment();
					$comment->setId($result['commentId'])->setIdArticle($result['commentIdArticle'])->setContent($result['commentContent'])->setNbReport($result['commentNbReport'])->setAuthor($result['commentAuthor'])->setAuthorIsAdmin($result['commentAuthorIsAdmin'])->setDatePublication($result['commentDatePublication']);
					if (isset($result['commentAuthorEdit'])) {
						$comment->setAuthorEdit($result['commentAuthorEdit'])->setDateEdit($result['commentDateEdit']);
					}
					array_unshift($arrComments, $comment);
				}
				$post->setComments($arrComments);
			
			$q->closeCursor();

			return $post;
		}
	}

	public function getAll()
	{
		$q = $this -> sql(
			'SELECT ' . static::$TABLE_CHAMPS . ' 
			FROM ' . static::$TABLE_NAME . ' 
			ORDER BY id DESC');

		$result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
			
		$q->closeCursor();

		return $result;
	}

	public function getGroup(int $limit, int $nb)
	{
		if ($nb > 0){
			$q = $this -> sql(
				'SELECT ' . static::$TABLE_CHAMPS . ' 
				FROM ' . static::$TABLE_NAME . ' 
				ORDER BY id DESC
				LIMIT :limit, :nb',
				[':limit' => $limit, ':nb' => $nb]);

			$result = $q->fetchObject(static::$OBJECT_TYPE);
				
			$q->closeCursor();

			return $result;
		}
	}

	public function set(Post $Post)
	{
		$this -> sql(
			'INSERT INTO ' . static::$TABLE_NAME . ' (author, imgPath, description, datePublication) 
			VALUES(:author, :imgPath, :description, NOW())',
			[':author' => $Post->getAuthor(), ':imgPath' => $Post->getImgPath(), ':description' => $Post->getDescription()]);

		return $this;
	}

	public function update(Post $Post)
	{
		$this -> sql(
			'UPDATE ' . static::$TABLE_NAME . ' 
			SET description = :description, imgPath = :imgPath
			WHERE id = :id',
			[':imgPath' => $Post->getImgPath(), ':description' => $Post->getDescription(), ':id' => $Post->getId()]);

		return $this;
	}
}
