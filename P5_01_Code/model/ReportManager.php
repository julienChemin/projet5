<?php

namespace Chemin\ArtSchool\Model;

use Chemin\ArtSchool\Model\AbstractManager;

class ReportManager extends AbstractManager
{
	public static $REPORT_POST_TABLE_NAME = 'as_report_post';
	public static $REPORT_POST_TABLE_CHAMPS = 'idPost, idUser, userName, DATE_FORMAT(dateReport, "%d/%m/%Y à %H:%i %s") AS dateReport, content';
	public static $REPORT_COMMENT_TABLE_NAME = 'as_report_comment';
	public static $REPORT_COMMENT_TABLE_CHAMPS = 'idComment, idUser, userName, DATE_FORMAT(dateReport, "%d/%m/%Y à %H:%i %s") AS dateReport, content';
	public static $LIMIT = 10;

	public function getReports(string $elem, bool $limit = false, int $offset = 0)
	{
		if (!empty($elem)) {
			$limit ? $clauseLimit = 'LIMIT ' . static::$LIMIT . ' OFFSET ' . $offset : $clauseLimit = '';
			switch ($elem) {
				case 'post' :
					$query = $this->sql('SELECT ' . static::$REPORT_POST_TABLE_CHAMPS . ' 
										FROM ' . static::$REPORT_POST_TABLE_NAME . ' 
										' . $clauseLimit);
				break;
				case 'comment' :
					$query = $this->sql('SELECT ' . static::$REPORT_COMMENT_TABLE_CHAMPS . ' 
										FROM ' . static::$REPORT_COMMENT_TABLE_NAME . ' 
										' . $clauseLimit);
				break;
			}
			$result = $query->fetchAll();
			$query->closeCursor();
			return $result;
		}
	}

	public function getReportsFromElem(string $elem, int $idElem)
	{
		if (!empty($elem) && $idElem > 0) {
			switch ($elem) {
				case 'post' :
					$query = $this->sql('SELECT ' . static::$REPORT_POST_TABLE_CHAMPS . ' 
										FROM ' . static::$REPORT_POST_TABLE_NAME . ' 
										WHERE idPost = :idPost', 
										[':idPost' => $idElem]);
				break;
				case 'comment' :
					$query = $this->sql('SELECT ' . static::$REPORT_COMMENT_TABLE_CHAMPS . ' 
										FROM ' . static::$REPORT_COMMENT_TABLE_NAME . ' 
										WHERE idComment = :idComment', 
										[':idComment' => $idElem]);
				break;
			}
			$result = $query->fetchAll();
			$query->closeCursor();
			return $result;
		}
	}

	public function setReport(string $elem, int $idElem, int $idUser, string $content)
	{
		if (!empty($elem) && !empty($content) && $this->checkForScriptInsertion([$content]) && $idElem > 0 && $idUser > 0) {
			switch ($elem) {
				case 'post' :
					$this->sql('INSERT INTO ' . static::$REPORT_POST_TABLE_NAME . ' (idPost, idUser, userName, content, dateReport) 
								VALUES (:idPost, :idUser, :userName, :content, NOW())', 
								[':idPost' => $idElem, ':idUser' => $idUser, ':userName' => $_SESSION['pseudo'], ':content' => $content]);
				break;
				case 'comment' :
					$this->sql('INSERT INTO ' . static::$REPORT_COMMENT_TABLE_NAME . ' (idComment, idUser, userName, content, dateReport) 
								VALUES (:idComment, :idUser, :userName, :content, NOW())', 
								[':idComment' => $idElem, ':idUser' => $idUser, ':userName' => $_SESSION['pseudo'], ':content' => $content]);
				break;
			}
		}
		return $this;
	}

	public function deleteReport(string $elem, int $idElem, int $idUser)
	{
		if (!empty($elem) && $idElem > 0 && $idUser > 0 && $this->reportExists($elem, $idElem, $idUser)) {
			switch ($elem) {
				case 'post' :
					$this->sql('DELETE FROM ' . static::$REPORT_POST_TABLE_NAME . ' 
								WHERE idUser = :idUser AND idPost = :idPost',
								[':idUser' => $idUser, ':idPost' => $idElem]);
				break;
				case 'comment' :
					$this->sql('DELETE FROM ' . static::$REPORT_COMMENT_TABLE_NAME . ' 
								WHERE idUser = :idUser AND idComment = :idComment',
								[':idUser' => $idUser, ':idComment' => $idElem]);
				break;
			}
		}
		return $this;
	}

	public function deleteReportsFromElem(string $elem, int $idElem)
	{
		if (!empty($elem) && $idElem > 0) {
			switch ($elem) {
				case 'post' :
					$this->sql('DELETE FROM ' . static::$REPORT_POST_TABLE_NAME . ' 
								WHERE idPost = :idPost',
								[':idPost' => $idElem]);
				break;
				case 'comment' :
					$this->sql('DELETE FROM ' . static::$REPORT_COMMENT_TABLE_NAME . ' 
								WHERE idComment = :idComment',
								[':idComment' => $idElem]);
				break;
			}
		}
		return $this;
	}

	public function reportExists(string $elem, int $idElem, int $idUser)
	{
		if (!empty($elem) && $idElem > 0 && $idUser > 0) {
			switch ($elem) {
				case 'post' :
					$req = $this->sql('SELECT * 
									FROM ' . static::$REPORT_POST_TABLE_NAME . ' 
									WHERE idUser = :idUser AND idPost = :idPost',
									[':idUser' => $idUser, ':idPost' => $idElem]);
				break;
				case 'comment' :
					$req = $this->sql('SELECT * 
									FROM ' . static::$REPORT_COMMENT_TABLE_NAME . ' 
									WHERE idUser = :idUser AND idComment = :idComment',
									[':idUser' => $idUser, ':idComment' => $idElem]);
				break;
				default : return false;
			}
			if ($result = $req->fetch()) {
				$req->closeCursor();
				return true;
			} else {
				$req->closeCursor();
				return false;
			}
		} else {return false;}
	}

	public function getCount(string $elem, int $idElem = null)
	{
		if (!empty($elem) && ($idElem === null || $idElem > 0)) {
			$idElem !== null ? $clauseWhere = 'WHERE id' . ucfirst($elem) . ' = ' . $idElem : $clauseWhere = "";
			switch ($elem) {
				case 'post' :
					if (!empty($idElem)) {
						//count for one elem
						$query = $this->sql('SELECT COUNT(*) 
											FROM ' . static::$REPORT_POST_TABLE_NAME . ' 
											WHERE idPost = :idPost', 
											[':idPost' => $idElem]);
					} else {
						//count all post reports
						$query = $this->sql('SELECT COUNT(*) 
											FROM ' . static::$REPORT_POST_TABLE_NAME);
					}
				break;
				case 'comment' :
					if (!empty($idElem)) {
						//count for one elem
						$query = $this->sql('SELECT COUNT(*) 
											FROM ' . static::$REPORT_COMMENT_TABLE_NAME . ' 
											WHERE idComment = :idComment', 
											[':idComment' => $idElem]);
					} else {
						//count all comment reports
						$query = $this->sql('SELECT COUNT(*) 
											FROM ' . static::$REPORT_COMMENT_TABLE_NAME);
					}
				break;
			}
			$response = $query->fetch();
			$query->closeCursor();
			return $response[0];
		}
	}
}
