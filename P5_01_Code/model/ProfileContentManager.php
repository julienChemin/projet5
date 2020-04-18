<?php

namespace Chemin\ArtSchool\Model;

use Chemin\ArtSchool\Model\Database;

class ProfileContentManager extends AbstractManager
{
	public static $OBJECT_TYPE = 'Chemin\ArtSchool\Model\ProfileContent';
	public static $TABLE_NAME = 'as_profile_content';
	public static $TABLE_PK = 'id';
	public static $TABLE_CHAMPS ='id, userId, tab, size, contentOrder, align, content';

	public function add(ProfileContent $profileContent)
	{
		$this->sql('
			INSERT INTO ' . static::$TABLE_NAME . ' (userId, tab, size, contentOrder, align, content) 
			VALUES (:userId, :tab, :size, :contentOrder, :align, :content)', 
			[':userId' => $profileContent->getUserId(), ':tab' => $profileContent->getTab(), ':size' => $profileContent->getSize(), 
			':contentOrder' => $profileContent->getContentOrder(), ':align' => $profileContent->getAlign(), ':content' => $profileContent->getContent()]);

		return $this;
	}

	public function getByUserId(int $userId)
	{
		$query = $this->sql('
			SELECT ' . static::$TABLE_CHAMPS . ' 
			FROM ' . static::$TABLE_NAME . ' 
			WHERE userId = :userId', 
			[':userId' => $userId]);

		
		$result = $query->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
		
		$query->closeCursor();

		return $result;
	}

	public function getContentForUpdate(int $userId, string $tab, int $blockOrderValue, int $newOrderValue)
	{
		if ($newOrderValue < $blockOrderValue) {
			$offset = ($this->getCount($userId, $tab) + 1) - intval($blockOrderValue);
			$limit = intval($blockOrderValue) - intval($newOrderValue);


			$query = $this->sql('
				SELECT ' . static::$TABLE_CHAMPS . ' 
				FROM ' . static::$TABLE_NAME . ' 
				WHERE userId = :userId AND tab = :tab 
				ORDER BY contentOrder DESC 
				LIMIT :limit OFFSET :offset', 
				[':userId' => $userId, ':tab' => $tab, ':offset' => $offset, ':limit' => $limit]);
		} else {
			$offset = intval($blockOrderValue) - 1;
			$limit = intval($newOrderValue) - intval($blockOrderValue);

			$query = $this->sql('
				SELECT ' . static::$TABLE_CHAMPS . ' 
				FROM ' . static::$TABLE_NAME . ' 
				WHERE userId = :userId AND tab = :tab 
				ORDER BY contentOrder  
				LIMIT :limit OFFSET :offset', 
				[':userId' => $userId, ':tab' => $tab, ':limit' => $limit, ':offset' => $offset]);
		}
		
		$result = $query->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
		
		$query->closeCursor();

		return $result;
	}

	public function getContentForAdd(int $userId, string $tab, int $contentOrder)
	{
		$limit = $this->getCount($userId, $tab) - ($contentOrder - 1);

		$query = $this->sql('
			SELECT ' . static::$TABLE_CHAMPS . ' 
			FROM ' . static::$TABLE_NAME . ' 
			WHERE userId = :userId AND tab = :tab 
			ORDER BY contentOrder DESC
			LIMIT :limit', 
			[':userId' => $userId, ':tab' => $tab, ':limit' => $limit]);

		
		$result = $query->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
		
		$query->closeCursor();

		return $result;
	}

	public function getContentForDelete(int $userId, string $tab, int $contentOrder)
	{
		$offset = $contentOrder - 1;
		$limit = $this->getCount($userId, $tab) - $offset;
		
		$query = $this->sql('
			SELECT ' . static::$TABLE_CHAMPS . ' 
			FROM ' . static::$TABLE_NAME . ' 
			WHERE userId = :userId AND tab = :tab 
			ORDER BY contentOrder 
			LIMIT :limit OFFSET :offset', 
			[':userId' => $userId, ':tab' => $tab, ':limit' => $limit, ':offset' => $offset]);

		
		$result = $query->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
		
		$query->closeCursor();

		return $result;
	}

	public function update(int $userId, int $blockOrderValue, ProfileContent $profileContent)
	{
		$this->sql('
			UPDATE ' . static::$TABLE_NAME . ' 
			SET size = :size, contentOrder = :newContentOrder, align = :align, content = :content 
			WHERE userId = :userId AND contentOrder = :contentOrder AND tab = :tab', 
			[':size' => $profileContent->getSize(), ':newContentOrder' => $profileContent->getContentOrder(), ':align' => $profileContent->getAlign(), 
			':content' => $profileContent->getContent(), ':userId' => $userId, ':contentOrder' => $blockOrderValue, ':tab' => $profileContent->getTab()]);

		return $this;
	}

	public function updateElem(ProfileContent $profileContent, string $elem, $value)
	{
		switch ($elem) {
			case 'contentOrder' :
				$this->sql('
					UPDATE ' . static::$TABLE_NAME . ' 
					SET contentOrder = :newContentOrder 
					WHERE userId = :userId AND contentOrder = :contentOrder AND tab = :tab', 
					[':newContentOrder' => $value, ':userId' => $profileContent->getUserId(), ':contentOrder' => $profileContent->getContentOrder(), ':tab' => $profileContent->getTab()]);

				return $this;
			break;
		}
	}

	public function deleteByUserId(int $userId, string $tab, int $blockOrderValue)
	{
		$this->sql(
			'DELETE FROM ' . static::$TABLE_NAME . '
			 WHERE userId = :userId AND contentOrder = :contentOrder AND tab = :tab',
			 [':userId' => $userId, ':contentOrder' => $blockOrderValue, ':tab' => $tab]);

		return $this;
	}

	public function getCount(int $userId, string $tab)
	{
		if (strlen($tab) > 0) {
			$query = $this->sql('
				SELECT COUNT(*) 
				FROM ' . static::$TABLE_NAME . ' 
				WHERE userId = :userId AND tab = :tab', 
				[':userId' => $userId, ':tab' => $tab]);

			
			$result = $query->fetch();
			
			$query->closeCursor();

			return intval($result[0]);
		}
	}
}
