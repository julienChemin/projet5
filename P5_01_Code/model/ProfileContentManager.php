<?php

namespace Chemin\ArtSchool\Model;

use Chemin\ArtSchool\Model\Database;

class ProfileContentManager extends AbstractManager
{
	public static $OBJECT_TYPE = 'Chemin\ArtSchool\Model\ProfileContent';
	public static $TABLE_NAME = 'as_profile_content';
	public static $TABLE_PK = 'id';
	public static $TABLE_CHAMPS ='id, userId, schoolId, tab, size, contentOrder, align, content';

	public function add(ProfileContent $profileContent)
	{
		$this->sql('INSERT INTO ' . static::$TABLE_NAME . ' (userId, schoolId, tab, size, contentOrder, align, content) 
					VALUES (:userId, :schoolId, :tab, :size, :contentOrder, :align, :content)', 
					[':userId' => $profileContent->getUserId(), ':schoolId' => $profileContent->getSchoolId(), ':tab' => $profileContent->getTab(), ':size' => $profileContent->getSize(), 
					':contentOrder' => $profileContent->getContentOrder(), ':align' => $profileContent->getAlign(), ':content' => $profileContent->getContent()]);
		return $this;
	}

	public function getByProfileId(int $profileId, bool $schoolProfile = false)
	{
		if ($schoolProfile) {
			$query = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
								FROM ' . static::$TABLE_NAME . ' 
								WHERE schoolId = :schoolId', 
								[':schoolId' => $profileId]);
		} else {
			$query = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
								FROM ' . static::$TABLE_NAME . ' 
								WHERE userId = :userId', 
								[':userId' => $profileId]);
		}		
		$result = $query->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);		
		$query->closeCursor();
		return $result;
	}

	public function getContentForUpdate(int $profileId, string $tab, int $blockOrderValue, int $newOrderValue, bool $schoolProfile = false)
	{
		if ($schoolProfile) {
			if ($newOrderValue < $blockOrderValue) {
				$offset = ($this->getCount($profileId, $tab, true) + 1) - intval($blockOrderValue);
				$limit = intval($blockOrderValue) - intval($newOrderValue);
				$query = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
									FROM ' . static::$TABLE_NAME . ' 
									WHERE schoolId = :schoolId AND tab = :tab 
									ORDER BY contentOrder DESC 
									LIMIT :limit OFFSET :offset', 
									[':schoolId' => $profileId, ':tab' => $tab, ':offset' => $offset, ':limit' => $limit]);
			} else {
				$offset = intval($blockOrderValue) - 1;
				$limit = intval($newOrderValue) - intval($blockOrderValue);
				$query = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
									FROM ' . static::$TABLE_NAME . ' 
									WHERE schoolId = :schoolId AND tab = :tab 
									ORDER BY contentOrder 
									LIMIT :limit OFFSET :offset', 
									[':schoolId' => $profileId, ':tab' => $tab, ':limit' => $limit, ':offset' => $offset]);
			}
		} else {
			if ($newOrderValue < $blockOrderValue) {
				$offset = ($this->getCount($profileId, $tab, true) + 1) - intval($blockOrderValue);
				$limit = intval($blockOrderValue) - intval($newOrderValue);
				$query = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
									FROM ' . static::$TABLE_NAME . ' 
									WHERE userId = :userId AND tab = :tab 
									ORDER BY contentOrder DESC 
									LIMIT :limit OFFSET :offset', 
									[':userId' => $profileId, ':tab' => $tab, ':offset' => $offset, ':limit' => $limit]);
			} else {
				$offset = intval($blockOrderValue) - 1;
				$limit = intval($newOrderValue) - intval($blockOrderValue);
				$query = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
									FROM ' . static::$TABLE_NAME . ' 
									WHERE userId = :userId AND tab = :tab 
									ORDER BY contentOrder 
									LIMIT :limit OFFSET :offset', 
									[':userId' => $profileId, ':tab' => $tab, ':limit' => $limit, ':offset' => $offset]);
			}
		}		
		$result = $query->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);		
		$query->closeCursor();
		return $result;
	}

	public function getContentForAdd(int $profileId, string $tab, int $contentOrder, bool $schoolProfile = false)
	{
		$limit = $this->getCount($profileId, $tab, true) - ($contentOrder - 1);
		if ($schoolProfile) {
			$query = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
								FROM ' . static::$TABLE_NAME . ' 
								WHERE schoolId = :schoolId AND tab = :tab 
								ORDER BY contentOrder DESC 
								LIMIT :limit', 
								[':schoolId' => $profileId, ':tab' => $tab, ':limit' => $limit]);
		} else {
			$query = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
								FROM ' . static::$TABLE_NAME . ' 
								WHERE userId = :userId AND tab = :tab 
								ORDER BY contentOrder DESC 
								LIMIT :limit', 
								[':userId' => $profileId, ':tab' => $tab, ':limit' => $limit]);
		}
		$result = $query->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
		$query->closeCursor();
		return $result;
	}

	public function getContentForDelete(int $profileId, string $tab, int $contentOrder, bool $schoolProfile = false)
	{
		$offset = $contentOrder - 1;
		$limit = $this->getCount($profileId, $tab, true) - $offset;
		if ($schoolProfile) {
			$query = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
								FROM ' . static::$TABLE_NAME . ' 
								WHERE schoolId = :schoolId AND tab = :tab 
								ORDER BY contentOrder 
								LIMIT :limit OFFSET :offset', 
								[':schoolId' => $profileId, ':tab' => $tab, ':limit' => $limit, ':offset' => $offset]);
		} else {
			$query = $this->sql('SELECT ' . static::$TABLE_CHAMPS . ' 
								FROM ' . static::$TABLE_NAME . ' 
								WHERE userId = :userId AND tab = :tab 
								ORDER BY contentOrder 
								LIMIT :limit OFFSET :offset', 
								[':userId' => $profileId, ':tab' => $tab, ':limit' => $limit, ':offset' => $offset]);
		}
		$result = $query->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
		$query->closeCursor();
		return $result;
	}

	public function update(int $blockOrderValue, ProfileContent $profileContent, bool $schoolProfile = false)
	{
		if ($schoolProfile) {
			$this->sql('UPDATE ' . static::$TABLE_NAME . ' 
						SET size = :size, contentOrder = :newContentOrder, align = :align, content = :content 
						WHERE schoolId = :schoolId AND contentOrder = :contentOrder AND tab = :tab', 
						[':size' => $profileContent->getSize(), ':newContentOrder' => $profileContent->getContentOrder(), ':align' => $profileContent->getAlign(), 
						':content' => $profileContent->getContent(), ':schoolId' => $profileContent->getSchoolId(), ':contentOrder' => $blockOrderValue, ':tab' => $profileContent->getTab()]);
		} else {
			$this->sql('UPDATE ' . static::$TABLE_NAME . ' 
						SET size = :size, contentOrder = :newContentOrder, align = :align, content = :content 
						WHERE userId = :userId AND contentOrder = :contentOrder AND tab = :tab', 
						[':size' => $profileContent->getSize(), ':newContentOrder' => $profileContent->getContentOrder(), ':align' => $profileContent->getAlign(), 
						':content' => $profileContent->getContent(), ':userId' => $profileContent->getUserId(), ':contentOrder' => $blockOrderValue, ':tab' => $profileContent->getTab()]);
		}
		return $this;
	}

	public function updateElem(ProfileContent $profileContent, string $elem, $value, bool $schoolProfile = false)
	{
		if ($schoolProfile) {
			switch ($elem) {
				case 'contentOrder' :
					$this->sql('UPDATE ' . static::$TABLE_NAME . ' 
								SET contentOrder = :newContentOrder 
								WHERE schoolId = :schoolId AND contentOrder = :contentOrder AND tab = :tab', 
								[':newContentOrder' => $value, ':schoolId' => $profileContent->getSchoolId(), ':contentOrder' => $profileContent->getContentOrder(), 
								':tab' => $profileContent->getTab()]);
				break;
			}
		} else {
			switch ($elem) {
				case 'contentOrder' :
					$this->sql('UPDATE ' . static::$TABLE_NAME . ' 
								SET contentOrder = :newContentOrder 
								WHERE userId = :userId AND contentOrder = :contentOrder AND tab = :tab', 
								[':newContentOrder' => $value, ':userId' => $profileContent->getUserId(), ':contentOrder' => $profileContent->getContentOrder(), 
								':tab' => $profileContent->getTab()]);
				break;
			}
		}
		return $this;
	}

	public function deleteByProfileId(int $profileId, string $tab, int $blockOrderValue, bool $schoolProfile = false)
	{
		if ($schoolProfile) {
			$this->sql('DELETE FROM ' . static::$TABLE_NAME . ' 
						WHERE schoolId = :schoolId AND contentOrder = :contentOrder AND tab = :tab', 
						[':schoolId' => $profileId, ':contentOrder' => $blockOrderValue, ':tab' => $tab]);
		} else {
			$this->sql('DELETE FROM ' . static::$TABLE_NAME . ' 
						WHERE userId = :userId AND contentOrder = :contentOrder AND tab = :tab', 
						[':userId' => $profileId, ':contentOrder' => $blockOrderValue, ':tab' => $tab]);
		}
		return $this;
	}

	public function getCount(int $profileId, string $tab, bool $schoolProfile = false)
	{
		if ($schoolProfile) {
			$query = $this->sql('SELECT COUNT(*) 
								FROM ' . static::$TABLE_NAME . ' 
								WHERE schoolId = :schoolId AND tab = :tab', 
								[':schoolId' => $profileId, ':tab' => $tab]);
		} else {
			$query = $this->sql('SELECT COUNT(*) 
								FROM ' . static::$TABLE_NAME . ' 
								WHERE userId = :userId AND tab = :tab', 
								[':userId' => $profileId, ':tab' => $tab]);
		}		
		$result = $query->fetch();		
		$query->closeCursor();
		return intval($result[0]);
	}

	public function updateProfileContent($arrPOST)
	{
		if (!empty($arrPOST['deleteBlock'])) {
			//delete content
			$this->deleteByProfileId($_SESSION['id'], $arrPOST['type'], $arrPOST['deleteBlock']);
			$order = intval($arrPOST['deleteBlock']);
			$contentToUpdate = $this->getContentForDelete($_SESSION['id'], $arrPOST['type'], $arrPOST['deleteBlock']);
			foreach ($contentToUpdate as $content) {
				$newOrderContent = intval($content->getContentOrder())-1;
				$this->updateElem($content, 'contentOrder', $newOrderContent);
			}
		} else {
			if ($this->checkForScriptInsertion([$arrPOST['tinyMCEtextarea']])) {
				//add new content
				if ($arrPOST['blockOrderValue'] === 'new') {
					if ($arrPOST['newOrderValue'] === 'last') {
						//new content go to last place
						$order = $this->getCount($_SESSION['id'], $arrPOST['type']) + 1;
					} else {
						//new content go to "newOrderValue" place
						$order = intval($arrPOST['newOrderValue']);
						$contentToUpdate = $this->getContentForAdd($_SESSION['id'], $arrPOST['type'], $order);
						foreach ($contentToUpdate as $content) {
							$newOrderContent = intval($content->getContentOrder())+1;
							$this->updateElem($content, 'contentOrder', $newOrderContent);
						}
					}
					$this->add(new ProfileContent(['userId' => $_SESSION['id'], 
													'tab' => $arrPOST['type'], 
													'size' => $arrPOST['sizeValue'], 
													'contentOrder' => $order, 
													'align' => $arrPOST['alignValue'], 
													'content' => $arrPOST['tinyMCEtextarea']]));
				} else {
					//edit content
					if ($arrPOST['blockOrderValue'] === $arrPOST['newOrderValue']) {
						//content keep his place number
						$order = intval($arrPOST['newOrderValue']);
						$this->update($arrPOST['blockOrderValue'], new ProfileContent(['userId' => $_SESSION['id'], 
																						'tab' => $arrPOST['type'], 
																						'size' => $arrPOST['sizeValue'], 
																						'contentOrder' => $order, 
																						'align' => $arrPOST['alignValue'], 
																						'content' => $arrPOST['tinyMCEtextarea']]));
					} else {
						//content change place number
						$this->deleteByProfileId($_SESSION['id'], $arrPOST['type'], $arrPOST['blockOrderValue']);
						$order = intval($arrPOST['newOrderValue']);
						$contentToUpdate = $this->getContentForUpdate($_SESSION['id'], $arrPOST['type'], $arrPOST['blockOrderValue'], $arrPOST['newOrderValue']);
						if ($arrPOST['newOrderValue'] < $arrPOST['blockOrderValue']) {
							foreach ($contentToUpdate as $content) {
								$newOrderContent = intval($content->getContentOrder())+1;
								$this->updateElem($content, 'contentOrder', $newOrderContent);
							}
						} else {
							foreach ($contentToUpdate as $content) {
								$newOrderContent = intval($content->getContentOrder())-1;
								$this->updateElem($content, 'contentOrder', $newOrderContent);
							}
						}
						$this->add(new ProfileContent(['userId' => $_SESSION['id'], 
														'tab' => $arrPOST['type'], 
														'size' => $arrPOST['sizeValue'], 
														'contentOrder' => $order, 
														'align' => $arrPOST['alignValue'], 
														'content' => $arrPOST['tinyMCEtextarea']]));
					}
				}
			}
		}
	}
}
