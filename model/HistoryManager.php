<?php

namespace Chemin\ArtSchool\Model;

use Chemin\ArtSchool\Model\Database;

class HistoryManager extends Database
{
	public static $OBJECT_TYPE = 'Chemin\ArtSchool\Model\HistoryEntry';
	public static $TABLE_NAME = 'as_history';
	public static $TABLE_PK = 'id';
	public static $TABLE_CHAMPS ='id, idSchool, category, entry, DATE_FORMAT(dateEntry, "%d/%m/%Y à %H:%m") AS dateEntry';
	public static $LIMIT = 10;

	public function getBySchool($school, int $offset = 0, string $sortBy = null, $sortValue = null, $secondSortValue = null)
	{
		if (intval($school) > 0) {
			if (empty($sortBy)) {
				//all school entries
				$q = $this->sql('
				SELECT category, entry, DATE_FORMAT(dateEntry, "%d/%m/%Y à %H:%m") AS dateEntry 
				FROM ' . static::$TABLE_NAME . ' 
				WHERE idSchool = :idSchool 
				ORDER BY id DESC 
				LIMIT ' . static::$LIMIT . ' OFFSET :offset', 
				[':idSchool' => $school, ':offset' => $offset]);
			} else {
				switch ($sortBy) {
					case "category" :
						//all school entries of particular category
						$q = $this->sql(
						'SELECT category, entry, DATE_FORMAT(dateEntry, "%d/%m/%Y à %H:%m") AS dateEntry 
						FROM ' . static::$TABLE_NAME . ' 
						WHERE idSchool = :idSchool AND category = :category 
						ORDER BY id DESC 
						LIMIT ' . static::$LIMIT . ' OFFSET :offset', 
						[':idSchool' => $school, ':category' => $sortValue, ':offset' => $offset]);
					break;
					case "date" :
						//all school entries between two dates
						$q = $this->sql(
						'SELECT category, entry, DATE_FORMAT(dateEntry, "%d/%m/%Y à %H:%m") AS dateEntry 
						FROM ' . static::$TABLE_NAME . ' 
						WHERE idSchool = :idSchool AND (dateEntry >= :dateEntry AND dateEntry < :secondDateEntry) 
						ORDER BY id DESC 
						LIMIT ' . static::$LIMIT . ' OFFSET :offset', 
						[':idSchool' => $school, ':dateEntry' => $sortValue[0], ':secondDateEntry' => $sortValue[1], ':offset' => $offset]);
					break;
					case "categoryAndDate" :
						//all school entries of particular category and between two dates
						$q = $this->sql(
						'SELECT category, entry, DATE_FORMAT(dateEntry, "%d/%m/%Y à %H:%m") AS dateEntry 
						FROM ' . static::$TABLE_NAME . ' 
						WHERE idSchool = :idSchool AND category = :category AND (dateEntry >= :dateEntry AND dateEntry < :secondDateEntry) 
						ORDER BY id DESC 
						LIMIT ' . static::$LIMIT . ' OFFSET :offset', 
						[':idSchool' => $school, ':category' => $sortValue, ':dateEntry' => $secondSortValue[0], 
						':secondDateEntry' => $secondSortValue[1], ':offset' => $offset]);
					break;
				}
			}
		}

		$result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
		$q->closeCursor();

		return $result;
	}

	public function addEntry(HistoryEntry $entry)
	{
		$this->sql(
			'INSERT INTO ' . static::$TABLE_NAME . ' (idSchool, category, entry, dateEntry) 
			VALUE (:idSchool, :category, :entry, NOW())', 
			[':idSchool' => $entry->getIdSchool(), ':category' => $entry->getCategory(), ':entry' => $entry->getEntry()]);

		return $this;
	}
}
