<?php
namespace Chemin\ArtSchool\Model;
use Chemin\ArtSchool\Model\Database;

class TagsManager extends Database
{
	public static $TABLE_NAME = 'as_tags';

	public function getAll()
	{
		$q = $this->sql('SELECT name, quantity 
						FROM ' . static::$TABLE_NAME);
		$result = $q->fetchAll();	
		$q->closeCursor();
		return $result;
	}

	public function getOneByName(string $name)
	{
		$q = $this->sql('SELECT name, quantity 
						FROM ' . static::$TABLE_NAME . ' 
						WHERE name = :name', 
						[':name' => $name]);
		$result = $q->fetch();	
		$q->closeCursor();
		return $result;
	}

	public function set(string $name)
	{
		if (strlen($name) > 0) {
			$this->sql('INSERT INTO ' . static::$TABLE_NAME . ' (name) 
						VALUES(:name)', 
						[':name' => $name]);
		}
		return $this;
	}

	public function exists(string $name)
	{
		if (strlen($name) > 0) {
			$q = $this->sql('SELECT name 
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
		} else {return false;}
	}

	public function delete(string $name)
	{
		if ($this->exists($name)) {
			$this->sql('DELETE FROM ' . static::$TABLE_NAME . ' 
						WHERE name = :name', 
						[':name' => $name]);
		}
		return $this;
	}

	public function checkForNewTag(string $listTags)
	{
		if (!empty($listTags)) {
			$arrTags = explode(',', $listTags);
			for ($i=1; $i<count($arrTags); $i++) {
				if (!$this->exists($arrTags[$i])) {
					$this->set($arrTags[$i]);
				}
				$this->incrementQuantity($arrTags[$i]);
			}
		}
	}

	public function tagsAreValide(array $tags)
	{
		$regex = '/^[a-z0-9]+[a-z0-9 ]*[a-z0-9]+$/i';
		foreach ($tags as $tag) {
			if (!preg_match($regex, $tag)) {
				return false;
			}
		}
		return true;
	}

	public function incrementQuantity($name)
	{
		if ($this->exists($name)) {
			$tag = $this->getOneByName($name);
			$incrementedQuantity = intval($tag['quantity']) + 1;
			$this->sql('UPDATE ' . static::$TABLE_NAME . ' 
						SET quantity = :quantity
						WHERE name = :name', 
						[':quantity' => $incrementedQuantity, ':name' => $name]);
		}
		return $this;
	}

	public function decrementQuantity($name)
	{
		if ($this->exists($name)) {
			$tag = $this->getOneByName($name);
			$decrementedQuantity = intval($tag['quantity']) - 1;
			if ($decrementedQuantity <= 0) {
				$this->delete($name);
			} else {
				$this->sql('UPDATE ' . static::$TABLE_NAME . ' 
						SET quantity = :quantity
						WHERE name = :name', 
						[':quantity' => $decrementedQuantity, ':name' => $name]);
			}
		}
		return $this;
	}
}
