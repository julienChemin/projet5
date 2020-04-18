<?php

namespace Chemin\ArtSchool\Model;

use Chemin\ArtSchool\Model\Database;

class UserManager extends AbstractManager
{
	public static $OBJECT_TYPE = 'Chemin\ArtSchool\Model\User';
	public static $TABLE_NAME = 'as_users';
	public static $TABLE_PK = 'id';
	public static $TABLE_CHAMPS ='id, name, password, mail, school, temporaryPassword, beingReset, nbWarning, isBan, dateBan, isAdmin, isModerator, isActive, profileBannerInfo, profilePictureInfo, profileTextInfo';

	public function add(User $user)
	{
		$this->sql('
			INSERT INTO ' . static::$TABLE_NAME . ' (name, mail, school, password, isAdmin, isModerator) 
			VALUES (:name, :mail, :school, :password, :isAdmin, :isModerator)', 
			[':name' => $user->getName(), ':mail' => $user->getMail(), ':school' => $user->getSchool(), 
			':password' => $user->getPassword(), ':isAdmin' => intval($user->getIsAdmin()), ':isModerator' => intval($user->getIsModerator())]);

		return $this;
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

	public function getUsersBySchool(string $school, string $grade = null)
	{
		if (strlen($school) > 0) {
			if ($school === ALL_SCHOOL) {
				//every school
				if ($grade === 'admin') {
					//admins and moderator
					$q = $this->sql('
						SELECT ' . static::$TABLE_CHAMPS . ' 
						FROM ' . static::$TABLE_NAME . ' 
						WHERE isAdmin = true
							OR isModerator = true');
				} elseif ($grade === 'user') {
					//users except admins and moderators
					$q = $this->sql('
						SELECT ' . static::$TABLE_CHAMPS . ' 
						FROM ' . static::$TABLE_NAME . ' 
						WHERE isAdmin = false
							AND isModerator = false');
				} else {
					//all users
					$q = $this->sql('
						SELECT ' . static::$TABLE_CHAMPS . ' 
						FROM ' . static::$TABLE_NAME);
				}
			} else {
				//only school $school
				if ($grade === 'admin') {
					//admins and moderators of school $school
					$q = $this->sql('
						SELECT ' . static::$TABLE_CHAMPS . ' 
						FROM ' . static::$TABLE_NAME . ' 
						WHERE school = :school
							AND (isAdmin = true
							OR isModerator = true)',
						[':school' => $school]);
				} elseif ($grade === 'user') {
					//users of school $school except admins and moderators
					$q = $this->sql('
						SELECT ' . static::$TABLE_CHAMPS . ' 
						FROM ' . static::$TABLE_NAME . ' 
						WHERE school = :school
							AND isAdmin = false
							AND isModerator = false',
						[':school' => $school]);
				} else {
					//all users of school $school
					$q = $this->sql('
						SELECT ' . static::$TABLE_CHAMPS . ' 
						FROM ' . static::$TABLE_NAME . ' 
						WHERE school = :school',
						[':school' => $school]);
				}
			}
			$result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
			$q->closeCursor();
					
			return $result;
		}
	}

	public function updateById(int $id, string $elem, $value)
	{
		switch ($elem) {
			case 'grade' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET isAdmin = :isAdmin, isModerator = :isModerator 
					WHERE id = :id', 
					[':isAdmin' => intval($value['isAdmin']), ':isModerator' => intval($value['isModerator']), ':id' => $id]);
			break;
			case 'isActive' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET isActive = :isActive 
					WHERE id = :id', 
					[':isActive' => intval($value), ':id' => $id]);
			break;
			case 'school' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET school = :school 
					WHERE id = :id', 
					[':school' => $value, ':id' => $id]);
			break;
			case 'password' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET password = :password, beingReset = false 
					WHERE id = :id', 
					[':password' => $value, ':id' => $id]);
			break;
			case 'temporaryPassword' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET temporaryPassword = :temporaryPassword, beingReset = true 
					WHERE id = :id', 
					[':temporaryPassword' => $value, ':id' => $id]);
			break;
			case 'profileBannerInfo' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET profileBannerInfo = :profileBannerInfo 
					WHERE id = :id', 
					[':profileBannerInfo' => $value, ':id' => $id]);
			break;
			case 'profilePictureInfo' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET profilePictureInfo = :profilePictureInfo 
					WHERE id = :id', 
					[':profilePictureInfo' => $value, ':id' => $id]);
			break;
			case 'profileTextInfo' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET profileTextInfo = :profileTextInfo 
					WHERE id = :id', 
					[':profileTextInfo' => $value, ':id' => $id]);
			break;
		}
		return $this;
	}

	public function updateByName(string $name, string $elem, $value)
	{
		switch ($elem) {
			case 'grade' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET isAdmin = :isAdmin, isModerator = :isModerator 
					WHERE name = :name', 
					[':isAdmin' => intval($value['isAdmin']), ':isModerator' => intval($value['isModerator']), ':name' => $name]);
			break;
			case 'isActive' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET isActive = :isActive 
					WHERE name = :name', 
					[':isActive' => intval($value), ':name' => $name]);
			break;
			case 'school' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET school = :school 
					WHERE name = :name', 
					[':school' => $value, ':name' => $name]);
			break;
			case 'password' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET password = :password, beingReset = false 
					WHERE name = :name', 
					[':password' => $value, ':name' => $name]);
			break;
			case 'temporaryPassword' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET temporaryPassword = :temporaryPassword, beingReset = true 
					WHERE name = :name', 
					[':temporaryPassword' => $value, ':name' => $name]);
			break;
			case 'profileBannerInfo' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET profileBannerInfo = :profileBannerInfo 
					WHERE name = :name', 
					[':profileBannerInfo' => $value, ':name' => $name]);
			break;
			case 'profilePictureInfo' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET profilePictureInfo = :profilePictureInfo 
					WHERE name = :name', 
					[':profilePictureInfo' => $value, ':name' => $name]);
			break;
			case 'profileTextInfo' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET profileTextInfo = :profileTextInfo 
					WHERE name = :name', 
					[':profileTextInfo' => $value, ':name' => $name]);
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
}
