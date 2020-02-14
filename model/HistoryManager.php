<?php

namespace Chemin\ArtSchool\Model;

use Chemin\ArtSchool\Model\Database;

class HistoryManager extends Database
{
	public static $OBJECT_TYPE = 'Chemin\ArtSchool\Model\HistoryEntry';
	public static $TABLE_NAME = 'as_history';
	public static $TABLE_PK = 'id';
	public static $TABLE_CHAMPS ='id, idSchool, category, entry, DATE_FORMAT(dateEntry, "%d/%m/%Y à %H:%m.%s") AS dateEntry';

	public function getBySchool($id, string $sortBy = null, $sortValue = null, $secondSortValue = null)//second sort value need te be the date
	{
		if ($id === ALL_SCHOOL) {
			if (empty($sortBy)) {
				//all entries
				$q = $this->sql(
				'SELECT ' . static::$TABLE_CHAMPS . ' 
				FROM ' . static::$TABLE_NAME . ' 
				ORDER BY id DESC');
			} else {
				switch ($sortBy) {
					case "category" :
						//all entries of particular category
						$q = $this->sql(
						'SELECT ' . static::$TABLE_CHAMPS . ' 
						FROM ' . static::$TABLE_NAME . ' 
						WHERE category = :category 
						ORDER BY id DESC', 
						[':category' => $sortValue]);
					break;
					case "date" :
						//all entries between two dates
						$q = $this->sql(
						'SELECT ' . static::$TABLE_CHAMPS . ' 
						FROM ' . static::$TABLE_NAME . ' 
						WHERE dateEntry BETWEEN :dateEntry AND :secondDateEntry 
						ORDER BY id DESC', 
						[':dateEntry' => $sortValue[0], ':secondDateEntry' => $sortValue[1]]);
					break;
					case "categoryAndDate" :
						//all entries of particular category and between two dates
						$q = $this->sql(
						'SELECT ' . static::$TABLE_CHAMPS . ' 
						FROM ' . static::$TABLE_NAME . ' 
						WHERE category = :category AND dateEntry BETWEEN :dateEntry AND :secondDateEntry 
						ORDER BY id DESC', 
						[':category' => $sortValue, ':dateEntry' => $secondSortValue[0], ':secondDateEntry' => $secondSortValue[1]]);
					break;
				}
			}
		} elseif (is_int($id) && $id > 0) {
			if (empty($sortBy)) {
				//all school entries
				$q = $this->sql('
				SELECT ' . static::$TABLE_CHAMPS . ' 
				FROM ' . static::$TABLE_NAME . ' 
				WHERE idSchool = :idSchool 
				ORDER BY id DESC', 
				[':idSchool' => $id]);
			} else {
				switch ($sortBy) {
					case "category" :
						//all school entries of particular category
						$q = $this->sql(
						'SELECT ' . static::$TABLE_CHAMPS . ' 
						FROM ' . static::$TABLE_NAME . ' 
						WHERE idSchool = :idSchool AND category = :category 
						ORDER BY id DESC', 
						[':idSchool' => $id, ':category' => $sortValue]);
					break;
					case "date" :
						//all school entries between two dates
						$q = $this->sql(
						'SELECT ' . static::$TABLE_CHAMPS . ' 
						FROM ' . static::$TABLE_NAME . ' 
						WHERE idSchool = :idSchool AND dateEntry BETWEEN :dateEntry AND :secondDateEntry 
						ORDER BY id DESC', 
						[':idSchool' => $id, ':dateEntry' => $sortValue[0], ':secondDateEntry' => $sortValue[1]]);
					break;
					case "categoryAndDate" :
						//all school entries of particular category and between two dates
						$q = $this->sql(
						'SELECT ' . static::$TABLE_CHAMPS . ' 
						FROM ' . static::$TABLE_NAME . ' 
						WHERE idSchool = :idSchool AND category = :category AND dateEntry BETWEEN :dateEntry AND :secondDateEntry 
						ORDER BY id DESC', 
						[':idSchool' => $id, ':category' => $sortValue, ':dateEntry' => $secondSortValue[0], ':secondDateEntry' => $secondSortValue[1]]);
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
