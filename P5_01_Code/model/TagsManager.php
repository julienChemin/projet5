<?php

namespace Chemin\ArtSchool\Model;

use Chemin\ArtSchool\Model\Database;

class TagsManager extends Database
{
	public static $TABLE_NAME = 'as_tags';

	public function getAll()
	{
		$q = $this -> sql(
			'SELECT name 
			FROM ' . static::$TABLE_NAME);

		$result = $q->fetchAll();
			
		$q->closeCursor();

		return $result;
	}

	public function set(string $name)
	{
		if (strlen($name) > 0) {
			$this -> sql(
				'INSERT INTO ' . static::$TABLE_NAME . ' (name) 
				VALUES(:name)',
				[':name' => $name]);
		}

		return $this;
	}

	public function exists(string $name)
	{
		if (strlen($name) > 0) {
			$q = $this -> sql(
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
		} else {
			return false;
		}
	}

	public function delete(string $name)
	{
		if ($this->exists($name)) {
			$this->sql(
				'DELETE FROM ' . static::$TABLE_NAME . '
				 WHERE name = :name',
				 [':name' => $name]);
		}
		return $this;
	}
}
