<?php

namespace Chemin\ArtSchool\Model;

use Chemin\ArtSchool\Model\ReportManager;

class CommentsManager extends ReportManager
{
	public static $OBJECT_TYPE = 'Chemin\ArtSchool\Model\Comment';
	public static $TABLE_NAME = 'as_comments';
	public static $TABLE_PK = 'id';
	public static $TABLE_CHAMPS ='id, idPost, idAuthor, content, nbReport, DATE_FORMAT(datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication';

	public function set(Comment $Comment)
	{
		$this->sql('
			INSERT INTO ' . static::$TABLE_NAME . ' (idPost, content, idAuthor, datePublication) 
			VALUES (:idPost, :content, :idAuthor, NOW())',
			[':idPost' => $Comment->getIdPost(), ':content' => $Comment->getContent(), ':idAuthor' => $Comment->getIdAuthor()]);

		return $this;
	}

	public function update(Comment $Comment)
	{
		$this->sql('
			UPDATE ' . static::$TABLE_CHAMPS . ' 
			SET content = :content 
			WHERE id = :id',
			[':content' => $Comment->getContent(), ':id' => $Comment->getId()]);

		return $this;
	}
}
