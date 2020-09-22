<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\Database;

class HistoryManager extends Database
{
    public static $OBJECT_TYPE = 'Chemin\ArtSchools\Model\HistoryEntry';
    public static $TABLE_NAME = 'as_history';
    public static $TABLE_PK = 'id';
    public static $TABLE_CHAMPS ='id, idSchool, category, entry, DATE_FORMAT(dateEntry, "%d/%m/%Y à %H:%m") AS dateEntry';
    public static $LIMIT = 10;

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PUBLIC FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    public function getBySchool($school, int $offset = 0, string $sortBy = null, $sortValue = null, $secondSortValue = null)
    {
        if (intval($school) > 0) {
            if (empty($sortBy)) {
                //all school entries
                $q = $this->getAll($school, $offset);
            } else {
                switch ($sortBy) {
                    case "category" :
                        //all school entries of particular category
                        $q = $this->getByCategory($school, $offset, $sortValue);
                        break;
                    case "date" :
                        //all school entries between two dates
                        $q = $this->getByDate($school, $offset, $sortValue[0], $sortValue[1]);
                        break;
                    case "categoryAndDate" :
                        //all school entries of particular category and between two dates
                        $q = $this->getByCategoryAndDate($school, $offset, $sortValue, $secondSortValue[0], $secondSortValue[1]);
                        break;
                }
            }
            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
            $q->closeCursor();
            return $result;
        } else {
            return [];
        }
    }

    public function addEntry(HistoryEntry $entry)
    {
        $this->sql(
            'INSERT INTO ' . static::$TABLE_NAME . ' (idSchool, category, entry, dateEntry) 
			VALUE (:idSchool, :category, :entry, NOW())', 
            [':idSchool' => $entry->getIdSchool(), ':category' => $entry->getCategory(), ':entry' => $entry->getEntry()]
        );
        return $this;
    }

    public function getSchoolHistory($GET)
    {
        isset($GET['offset']) ? $offset = intval($GET['offset']) : $offset = 0;
        if (!empty($GET['sortBy']) && !empty($GET['sortValue'])) {
            if (!empty($GET['thirdSortValue'])) {
                //sort by category and date
                $entries = $this->getBySchool(
                    $GET['school'], 
                    $offset, 
                    $GET['sortBy'], 
                    $GET['sortValue'], 
                    [$GET['secondSortValue'], $GET['thirdSortValue']]
                );
            } elseif (!empty($GET['secondSortValue'])) {
                //sort by date
                $entries = $this->getBySchool(
                    $GET['school'], 
                    $offset, 
                    $GET['sortBy'], 
                    [$GET['sortValue'], $GET['secondSortValue']]
                );
            } else {
                //sort by category
                $entries = $this->getBySchool(
                    $GET['school'], 
                    $offset, 
                    $GET['sortBy'], 
                    $GET['sortValue']
                );
            }
        } else {
            //no sorting
            $entries = $this->getBySchool($GET['school'], $offset);
        }
        $arrEntries = [];
        for ($i=0; $i<count($entries); $i++) {
            $arrEntries[$i][] = $entries[$i]->getDateEntry();
            $arrEntries[$i][] = $entries[$i]->getEntry();
        }
        return $arrEntries;
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PRIVATE FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    private function getAll($school, $offset)
    {
        return $this->sql(
            'SELECT category, entry, DATE_FORMAT(dateEntry, "%d/%m/%Y à %H:%m") AS dateEntry 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE idSchool = :idSchool 
            ORDER BY id DESC 
            LIMIT ' . static::$LIMIT . ' OFFSET :offset', 
            [':idSchool' => $school, ':offset' => $offset]
        );
    }

    private function getByCategory($school, $offset, $sortValue)
    {
        return $this->sql(
            'SELECT category, entry, DATE_FORMAT(dateEntry, "%d/%m/%Y à %H:%m") AS dateEntry 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE idSchool = :idSchool AND category = :category 
            ORDER BY id DESC 
            LIMIT ' . static::$LIMIT . ' OFFSET :offset', 
            [':idSchool' => $school, ':category' => $sortValue, ':offset' => $offset]
        );
    }

    private function getByDate($school, $offset, $sortValue, $secondSortValue)
    {
        return $this->sql(
            'SELECT category, entry, DATE_FORMAT(dateEntry, "%d/%m/%Y à %H:%m") AS dateEntry 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE idSchool = :idSchool AND (dateEntry >= :dateEntry AND dateEntry < :secondDateEntry) 
            ORDER BY id DESC 
            LIMIT ' . static::$LIMIT . ' OFFSET :offset', 
            [':idSchool' => $school, ':dateEntry' => $sortValue, ':secondDateEntry' => $secondSortValue, ':offset' => $offset]
        );
    }

    private function getByCategoryAndDate($school, $offset, $sortValue, $secondSortValue, $thirdSortValue)
    {
        return $this->sql(
            'SELECT category, entry, DATE_FORMAT(dateEntry, "%d/%m/%Y à %H:%m") AS dateEntry 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE idSchool = :idSchool AND category = :category AND (dateEntry >= :dateEntry AND dateEntry < :secondDateEntry) 
            ORDER BY id DESC 
            LIMIT ' . static::$LIMIT . ' OFFSET :offset', 
            [':idSchool' => $school, ':category' => $sortValue, ':dateEntry' => $secondSortValue, 
            ':secondDateEntry' => $thirdSortValue, ':offset' => $offset]
        );
    }
}
