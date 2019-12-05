<?php

namespace Chemin\ArtSchool\Model;

use Chemin\ArtSchool\Model\Database;

class UserManager extends AbstractManager
{
	public static $OBJECT_TYPE = 'Chemin\ArtSchool\Model\User';
	public static $TABLE_NAME = 'as_users';
	public static $TABLE_PK = 'id';
	public static $TABLE_CHAMPS ='id, name, password, mail, school, temporaryPassword, beingReset, nbWarning, isBan, dateBan, isAdmin, isModerator';

	public function add(User $user)
	{
		$this->sql('
			INSERT INTO ' . static::$TABLE_NAME . ' (name, mail, school, password, isAdmin, isModerator) 
			VALUES (:name, :mail, :school, :password, :isAdmin, :isModerator)', 
			[':name' => $user->getName(), ':mail' => $user->getMail(), ':school' => $user->getSchool(), 
			':password' => $user->getPassword(), ':isAdmin' => intval($user->getIsAdmin()), ':isModerator' => intval($user->getIsModerator())]);
	}

	public function getUserByName(string $name)
	{
		$name = htmlspecialchars($name);

		if (strlen($name) > 0) {
			$q = $this->sql('
				SELECT ' . static::$TABLE_CHAMPS . ' 
				FROM ' . static::$TABLE_NAME . ' 
				WHERE name = :name',
				[':name' => $name]);

			$result = $q->fetchObject(static::$OBJECT_TYPE);
			$q->closeCursor();

			return $result;
		}
	}

	public function getUserByMail(string $adress)
	{
		$mail = htmlspecialchars($adress);
		if (strlen($mail) > 0) {
			$q = $this->sql('
				SELECT ' . static::$TABLE_CHAMPS . ' 
				FROM ' . static::$TABLE_NAME . ' 
				WHERE mail = :mail',
				[':mail' => $mail]);

			$result = $q->fetchObject(static::$OBJECT_TYPE);
			$q->closeCursor();
			
			return $result;
		}
	}

	public function getUserBySchool(string $school)
	{
		if (strlen($school) > 0) {
			$q = $this->sql('
				SELECT ' . static::$TABLE_CHAMPS . ' 
				FROM ' . static::$TABLE_NAME . ' 
				WHERE school = :school',
				[':school' => $school]);

			$result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
			$q->closeCursor();
			
			return $result;
		}
	}

	public function setIsAdminByElem(string $elem, $elemValue, bool $isAdmin)
	{
		switch ($elem) {
			case 'id' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET isAdmin = :isAdmin 
					WHERE id = :id',
					[':isAdmin' => intval($isAdmin), ':id' => $elemValue]);

				return $this;
			break;
			case 'name' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET isAdmin = :isAdmin 
					WHERE name = :name',
					[':isAdmin' => intval($isAdmin), ':name' => $elemValue]);

				return $this;
			break;
		}
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

	public function mailExists(string $adress)
	{
		$mail = htmlspecialchars($adress);
		if (strlen($mail) > 0) {
			$q = $this->sql('
				SELECT name
				FROM ' . static::$TABLE_NAME . ' 
				WHERE mail = :mail',
				[':mail' => $mail]);

			if ($q->fetch()) {
				$q->closeCursor();
				return true;
			} else {
				$q->closeCursor();
				return false;
			}
		}
	}

	public function checkPassword(User $user, string $password)
	{
		if (strlen($password) > 0) {
			return password_verify($password, $user->getPassword());
		}
	}

	public function setPassword(string $password, int $id)
	{
		if (strlen($password) > 0 && $id > 0) {
			$this->sql('
				UPDATE ' . static::$TABLE_NAME . ' 
				SET password = :password, beingReset = false 
				WHERE id = :id',
				[':password' => $password, ':id' => $id]);

			return $this;
		}
	}

	public function setTemporaryPassword(string $temporaryPassword, int $id)
	{
		if (strlen($temporaryPassword) > 0 && $id > 0) {
			$this->sql('
				UPDATE ' . static::$TABLE_NAME . ' 
				SET temporaryPassword = :temporaryPassword, beingReset = true 
				WHERE id = :id',
				[':temporaryPassword' => $temporaryPassword, ':id' => $id]);

			return $this;
		}
	}
}
