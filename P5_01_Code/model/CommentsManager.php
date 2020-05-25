<?php
namespace Chemin\ArtSchool\Model;
use Chemin\ArtSchool\Model\ReportManager;

class CommentsManager extends ReportManager
{
	public static $OBJECT_TYPE = 'Chemin\ArtSchool\Model\Comment';
	public static $TABLE_NAME = 'as_comment';
	public static $TABLE_PK = 'id';
	public static $TABLE_CHAMPS ='id, idPost, idAuthor, nameAuthor, profilePictureAuthor, content, nbReport, DATE_FORMAT(datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication';

	public function setComment(array $POST, user $user)
	{
		if ($this->checkForScriptInsertion($POST)) {
			$this->sql('INSERT INTO ' . static::$TABLE_NAME . ' (idPost, content, idAuthor, nameAuthor, profilePictureAuthor, datePublication) 
						VALUES (:idPost, :content, :idAuthor, :nameAuthor, :profilePictureAuthor, NOW())', 
						[':idPost' => $POST['idPost'], ':content' => $POST['commentContent'], ':idAuthor' => $user->getId(), ':nameAuthor' => $user->getName(), ':profilePictureAuthor' => $user->getProfilePicture()]);
			return true;
		} else {return false;}
	}
}
