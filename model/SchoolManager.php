<?php

namespace Chemin\ArtSchool\Model;

class SchoolManager extends AbstractManager
{
	public static $OBJECT_TYPE = 'Chemin\ArtSchool\Model\School';
	public static $TABLE_NAME = 'as_school';
	public static $TABLE_PK = 'id';
	public static $TABLE_CHAMPS ='id, idAdmin, name, nameAdmin, code, nbEleve, DATE_FORMAT(dateInscription, "%d/%m/%Y") AS dateInscription, DATE_FORMAT(dateDeadline, "%d/%m/%Y") AS dateDeadline, logo';

	public function getAll()
	{
		$q = $this->sql(
			'SELECT ' . static::$TABLE_CHAMPS . ' 
			FROM ' . static::$TABLE_NAME . ' 
			ORDER BY id');

		$result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
		$q->closeCursor();

		return $result;
	}

	public function add(School $school)
	{
		$this->sql(
			'INSERT INTO ' . static::$TABLE_NAME . ' (idAdmin, name, nameAdmin, code, nbEleve, dateInscription, dateDeadline, logo) 
			VALUE (:idAdmin, :name, :nameAdmin, :code, :nbEleve, NOW(), :dateDeadline, :logo)', 
			[':idAdmin' => $school->getIdAdmin(), ':name' => $school->getName(), ':nameAdmin' => $school->getNameAdmin(), 
			':code' => $school->getCode(), ':nbEleve' => $school->getNbEleve(), ':dateDeadline' => $school->getDateDeadline(), 
			':logo' => $school->getLogo()]);
	}

	public function update(int $id, string $elem, $value)
	{
		switch ($elem) {
			case 'idAdmin' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET idAdmin = :idAdmin 
					WHERE id = :id', 
					[':idAdmin' => $value, ':id' => $id]);
				break;
			case 'name' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET name = :name 
					WHERE id = :id', 
					[':name' => $value, ':id' => $id]);
				break;
			case 'nameAdmin' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET nameAdmin = :nameAdmin 
					WHERE id = :id', 
					[':nameAdmin' => $value, ':id' => $id]);
				break;
			case 'code' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET code = :code 
					WHERE id = :id', 
					[':code' => $value, ':id' => $id]);
				break;
			case 'nbEleve' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET nbEleve = :nbEleve 
					WHERE id = :id', 
					[':nbEleve' => $value, ':id' => $id]);
				break;
			case 'logo' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET logo = :logo 
					WHERE id = :id', 
					[':logo' => $value, ':id' => $id]);
			break;
			case 'dateDeadline' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET dateDeadline = :dateDeadline 
					WHERE id = :id', 
					[':dateDeadline' => $value, ':id' => $id]);
			break;
		}
		return $this;
	}

	public function nameExists(string $name)
	{
		if (strlen($name) > 0) {
			$q = $this->sql(
				'SELECT name 
				FROM ' . static::$TABLE_NAME . ' 
				WHERE name = :name', 
				[':name' => $name]);

			if ($q->fetch()) {
				$q->closeCursor();
				return true;
			} else {
				$q->closeCursor();
				return false;
			}
		}
	}
}
