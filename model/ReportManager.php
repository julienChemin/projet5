<?php

namespace Chemin\ArtSchool\Model;

use Chemin\ArtSchool\Model\AbstractManager;

abstract class ReportManager extends AbstractManager
{

	public function getMostReportedComments()
	{
		$query = $this->sql('
			SELECT id, idPost, idAuthor, content, nbReport, DATE_FORMAT(datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication
			FROM ' . static::$TABLE_NAME . ' 
			WHERE nbReport != 0
			ORDER BY nbReport DESC');

		$result = $query->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
			
		$query->closeCursor();

		return $result;
	}

	public function setReport(int $idComment, int $idAuthor, int $nbReportBefore, int $reason)
	{
		if ($idComment > 0 && $nbReportBefore >= 0 && $idAuthor > 0) {
			//add 1 to Comment's nbReport
			$nbReport = $nbReportBefore + 1;
			$this->setNbReport($idComment, $nbReport);

			//add report to "report" table
			$this->sql('
				INSERT INTO as_reported_comments (idComment, idAuthor, reason, dateReport)
				VALUES (:idComment, :idAuthor, :reason, NOW())',
				[':idComment' => $idComment, ':idAuthor' => $idAuthor, ':reason' => $reason]);

			return $this;
		}
	}

	public function deleteReport(int $idReport, int $idComment)
	{
		if ($idReport > 0 && $idComment > 0) {
			if ($this->reportExists($idReport)) {
				//delete report
				$this->sql('
					DELETE FROM as_reported_comments 
					WHERE id = :id',
					[':id' => $idReport]);

				//minus 1 to Comment's nbReport
				$req = $this->getNbReport($idComment)->fetch();
				$nbReportBefore = (int) $req['nbReport'];
				$nbReport = $nbReportBefore - 1;

				$this->setNbReport($idComment, $nbReport);

				return $this;
			}
		}
	}

	public function reportExists(int $id)
	{
		if ($id > 0) {
			$req = $this->sql(
				'SELECT *
				 FROM as_reported_comments 
				 WHERE id = :id',
				[':id' => $id]);

			if ($result = $req->fetch()) {
				return true;
			} else {
				return false;
			}
		}
	}

	public function getNbReport(int $idComment)
	{
		if ($idComment > 0) {
			return $this->sql('
				SELECT nbReport 
				FROM ' . static::$TABLE_NAME . ' 
				WHERE id = :idComment',
				[':idComment' => $idComment]);
		}
	}

	public function setNbReport(int $idComment, int $nbReport)
	{
		if ($idComment > 0 && $nbReport >= 0) {
			$this->sql('
				UPDATE ' . static::$TABLE_NAME . '  
				SET nbReport = :nbReport
				WHERE id = :idComment',
				[':idComment' => $idComment, ':nbReport' => $nbReport]);

			return $this;
		}
	}

	public function deleteReportsFromComment(int $idComment)
	{
		if ($idComment > 0) {
			$this->sql('
				DELETE FROM as_reported_comments 
				WHERE idComment = :idComment',
				[':idComment' => $idComment]);

			return $this;
		}
	}
}
