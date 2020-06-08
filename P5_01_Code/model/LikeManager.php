<?php
namespace Chemin\ArtSchool\Model;
use Chemin\ArtSchool\Model\AbstractManager;

abstract class LikeManager extends AbstractManager
{
	public function userAlreadyLikePost(int $idUser, int $idPost)
	{
		if (!empty($idUser) && !empty($idPost)) {
			$q = $this->sql('SELECT * 
							FROM as_like_post 
							WHERE idUser = :idUser AND idPost = :idPost', 
							[':idUser' => $idUser, ':idPost' => $idPost]);
			if ($result = $q->fetch()) {
				return true;
			} else {return  false;}
		} else {return false;}
	}

	public function toggleLikePost(int $idUser, int $idPost)
	{
		if (!empty($idUser) && !empty($idPost)) {
			if ($this->userAlreadyLikePost($idUser, $idPost)) {
				$this->sql('DELETE FROM as_like_post 
							WHERE idUser = :idUser AND idPost = :idPost', 
							[':idUser' => $idUser, ':idPost' => $idPost]);
				$this->editNbLike('decrement', $idPost);
				return true;
			} else {
				$this->sql('INSERT INTO as_like_post (idUser, idPost) 
							VALUES(:idUser, :idPost)', 
							[':idUser' => $idUser, ':idPost' => $idPost]);
				$this->editNbLike('increment', $idPost);
				return true;
			}
		} else {return false;}
	}

	public function getNbLike(int $idPost)
	{
		if ($idPost > 0) {
			$q = $this->sql('SELECT nbLike 
							FROM ' . static::$TABLE_NAME . ' 
							WHERE id = :idPost', 
							[':idPost' => $idPost]);
			$result = $q->fetch();
			return intval($result['nbLike']);
		}
	}

	public function editNbLike(string $action, int $idPost) {
		if ($idPost > 0) {
			$nbLike = $this->getNbLike($idPost);
			switch ($action) {
				case 'increment' :
					$nbLike += 1;
				break;
				case 'decrement' :
					$nbLike -= 1;
				break;
			}
			$this->sql('UPDATE ' . static::$TABLE_NAME . ' 
						SET nbLike = :nbLike 
						WHERE id = :idPost', 
						[':nbLike' => $nbLike, ':idPost' => $idPost]);
			return $this;
		}
	}
}
