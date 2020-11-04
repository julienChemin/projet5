<?php

namespace Chemin\ArtSchools\Model;

use Chemin\ArtSchools\Model\Database;

class ProfileContentManager extends AbstractManager
{
    public static $OBJECT_TYPE = 'Chemin\ArtSchools\Model\ProfileContent';
    public static $TABLE_NAME = 'as_profile_content';
    public static $TABLE_PK = 'id';
    public static $TABLE_CHAMPS ='id, userId, schoolId, tab, size, contentOrder, align, content';

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PUBLIC FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    public function getByProfileId(int $profileId, bool $schoolProfile = false)
    {
        if ($schoolProfile) {
            $query = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE schoolId = :schoolId', 
                [':schoolId' => $profileId]
            );
        } else {
            $query = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE userId = :userId', 
                [':userId' => $profileId]
            );
        }        
        $result = $query->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);        
        $query->closeCursor();
        return $result;
    }

    public function updateProfileContent(array $arrPOST, bool $schoolProfile = false, int $schoolId = 0)
    {
        if ($schoolProfile) {
            if ($schoolId > 0) {
                $idProfile = $schoolId;
            } else {
                $this->incorrectInformation();
            }
        } else {
            $idProfile = $_SESSION['id'];
        }

        if (!empty($arrPOST['deleteBlock'])) {
            //delete content
            $this->deleteProfileContent($idProfile, $arrPOST['type'], $arrPOST['idProfileContent'], $arrPOST['deleteBlock'], $schoolProfile);
        } else {
            if ($this->checkForScriptInsertion([$arrPOST['tinyMCEtextarea']])) {
                if ($arrPOST['blockOrderValue'] === 'new') {
                    //add new content
                    $this->addNewProfileContent($arrPOST['newOrderValue'], $idProfile, $arrPOST['type'], $arrPOST['sizeValue'], 
                        $arrPOST['alignValue'], $arrPOST['tinyMCEtextarea'], $schoolProfile);
                } else {
                    //edit content
                    if ($arrPOST['blockOrderValue'] === $arrPOST['newOrderValue']) {
                        //content keep his place number
                        $this->updateWithoutChangingPlace($arrPOST['blockOrderValue'], intval($arrPOST['newOrderValue']), $idProfile, 
                        $arrPOST['idProfileContent'], $arrPOST['type'], $arrPOST['sizeValue'], $arrPOST['alignValue'], $arrPOST['tinyMCEtextarea'], $schoolProfile);
                    } else {
                        //content change place number
                        $this->updateAndChangePlace($arrPOST['blockOrderValue'], intval($arrPOST['newOrderValue']), $idProfile, 
                        $arrPOST['idProfileContent'], $arrPOST['type'], $arrPOST['sizeValue'], $arrPOST['alignValue'], $arrPOST['tinyMCEtextarea'], $schoolProfile);
                    }
                }
            } else {
                $this->incorrectInformation();
            }
        }
    }

    public function deleteUnusedImg()
    {
        $unusedImg = $this->getUnusedImg();
        if (count($unusedImg) > 0) {
            foreach ($unusedImg as $img) {
                $this->deleteFile($img['filePath']);
            }
            $this->sql(
                'DELETE FROM as_profile_content_img 
                WHERE toDelete = 1'
            );
        }
        return count($unusedImg);
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PRIVATE FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    /*------------------------------ Profile content ------------------------------*/

    private function add(ProfileContent $profileContent)
    {
        $this->sql(
            'INSERT INTO ' . static::$TABLE_NAME . ' (userId, schoolId, tab, size, contentOrder, align, content) 
			VALUES (:userId, :schoolId, :tab, :size, :contentOrder, :align, :content)', 
            [':userId' => $profileContent->getUserId(), ':schoolId' => $profileContent->getSchoolId(), ':tab' => $profileContent->getTab(), ':size' => $profileContent->getSize(), 
            ':contentOrder' => $profileContent->getContentOrder(), ':align' => $profileContent->getAlign(), ':content' => $profileContent->getContent()]
        );
        return $this;
    }

    private function addNewProfileContent(string $newOrderValue, int $idProfile, string $type, string $size, string $alignVal, string $content, bool $schoolProfile = false)
    {
        if ($newOrderValue === 'last') {
            //new content go to last place
            $order = $this->getCount($idProfile, $type, $schoolProfile) + 1;
        } else {
            //new content go to "newOrderValue" place
            $order = intval($newOrderValue);
            $contentToUpdate = $this->getContentForAdd($idProfile, $type, $order, $schoolProfile);
            $this->incrDecrOrder($contentToUpdate, 'increment', $schoolProfile);
        }
        $schoolProfile ? $ownerId = 'schoolId' : $ownerId = 'userId';
        $this->add(new ProfileContent(
            [$ownerId => $idProfile, 
            'tab' => $type, 
            'size' => $size, 
            'contentOrder' => $order, 
            'align' => $alignVal, 
            'content' => $content]
        ));
        //create imgEntries for each images uploaded on the new profileContent
        $idProfileContent = $this->getLastInsertId();
        $imgOnContent = $this->checkForImgEntries($content);
        if (count($imgOnContent) > 0) {
            foreach ($imgOnContent as $filePath) {
                $this->setImgEntry($idProfileContent, $filePath);
            }
        }
        return $this;
    }

    private function getContentForUpdate(int $profileId, string $tab, int $blockOrderValue, int $newOrderValue, bool $schoolProfile = false)
    {
        //return all profilContent that we need to move for editing the order value of a profilContent
        $arrayValues[':tab'] = $tab;
        if ($schoolProfile) {
            $clauseWhere = 'schoolId = :id';
        } else {
            $clauseWhere = 'userId = :id';
        }
        $arrayValues[':id'] = $profileId;

        if ($newOrderValue < $blockOrderValue) {
            $arrayValues[':offset'] = ($this->getCount($profileId, $tab, $schoolProfile) + 1) - intval($blockOrderValue);
            $arrayValues[':limit'] = intval($blockOrderValue) - intval($newOrderValue);
            $clauseOrderBy = 'contentOrder DESC';
        } else {
            $arrayValues[':offset'] = intval($blockOrderValue) - 1;
            $arrayValues[':limit'] = intval($newOrderValue) - intval($blockOrderValue);
            $clauseOrderBy = 'contentOrder';
        }
        
        $query = $this->sql(
            'SELECT ' . static::$TABLE_CHAMPS . ' 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE tab = :tab AND ' . $clauseWhere . ' 
            ORDER BY ' . $clauseOrderBy . ' 
            LIMIT :limit OFFSET :offset', 
            $arrayValues
        );
        $result = $query->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);        
        $query->closeCursor();
        return $result;
    }

    private function getContentForAdd(int $profileId, string $tab, int $contentOrder, bool $schoolProfile = false)
    {
        //return all profilContent from number $contentOrder to last one, to insert the new profilContent
        $limit = $this->getCount($profileId, $tab, $schoolProfile) - ($contentOrder - 1);
        if ($schoolProfile) {
            $query = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE schoolId = :schoolId AND tab = :tab 
                ORDER BY contentOrder DESC 
                LIMIT :limit', 
                [':schoolId' => $profileId, ':tab' => $tab, ':limit' => $limit]
            );
        } else {
            $query = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE userId = :userId AND tab = :tab 
                ORDER BY contentOrder DESC 
                LIMIT :limit', 
                [':userId' => $profileId, ':tab' => $tab, ':limit' => $limit]
            );
        }
        $result = $query->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
        $query->closeCursor();
        return $result;
    }

    private function getContentForDelete(int $profileId, string $tab, int $contentOrder, bool $schoolProfile = false)
    {
        //return all profilContent from number $contentOrder to last one to move back them, to delete a profilContent
        $offset = $contentOrder - 1;
        $limit = $this->getCount($profileId, $tab, $schoolProfile) - $offset;
        if ($schoolProfile) {
            $query = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE schoolId = :schoolId AND tab = :tab 
                ORDER BY contentOrder 
                LIMIT :limit OFFSET :offset', 
                [':schoolId' => $profileId, ':tab' => $tab, ':limit' => $limit, ':offset' => $offset]
            );
        } else {
            $query = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE userId = :userId AND tab = :tab 
                ORDER BY contentOrder 
                LIMIT :limit OFFSET :offset', 
                [':userId' => $profileId, ':tab' => $tab, ':limit' => $limit, ':offset' => $offset]
            );
        }
        $result = $query->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
        $query->closeCursor();
        return $result;
    }

    private function deleteProfileContent(int $idProfile, string $type, int $idBlockToDelete, int $orderBlockToDelete, bool $schoolProfile = false)
    {
        $this->deleteAllImgEntries($idBlockToDelete)
            ->deleteByProfileId($idProfile, $type, $orderBlockToDelete, $schoolProfile);
        $contentToUpdate = $this->getContentForDelete($idProfile, $type, $orderBlockToDelete, $schoolProfile);
        foreach ($contentToUpdate as $content) {
            $newOrderContent = intval($content->getContentOrder())-1;
            $this->updateElem($content, 'contentOrder', $newOrderContent, $schoolProfile);
        }
        return $this;
    }

    private function deleteByProfileId(int $profileId, string $tab, int $blockOrderValue, bool $schoolProfile = false)
    {
        if ($schoolProfile) {
            $this->sql(
                'DELETE FROM ' . static::$TABLE_NAME . ' 
                WHERE schoolId = :schoolId AND contentOrder = :contentOrder AND tab = :tab', 
                [':schoolId' => $profileId, ':contentOrder' => $blockOrderValue, ':tab' => $tab]
            );
        } else {
            $this->sql(
                'DELETE FROM ' . static::$TABLE_NAME . ' 
                WHERE userId = :userId AND contentOrder = :contentOrder AND tab = :tab', 
                [':userId' => $profileId, ':contentOrder' => $blockOrderValue, ':tab' => $tab]
            );
        }
        return $this;
    }

    private function update(int $blockOrderValue, ProfileContent $profileContent, bool $schoolProfile = false)
    {
        if ($schoolProfile) {
            $this->sql(
                'UPDATE ' . static::$TABLE_NAME . ' 
                SET size = :size, contentOrder = :newContentOrder, align = :align, content = :content 
                WHERE schoolId = :schoolId AND contentOrder = :contentOrder AND tab = :tab', 
                [':size' => $profileContent->getSize(), ':newContentOrder' => $profileContent->getContentOrder(), ':align' => $profileContent->getAlign(), 
                ':content' => $profileContent->getContent(), ':schoolId' => $profileContent->getSchoolId(), ':contentOrder' => $blockOrderValue, ':tab' => $profileContent->getTab()]
            );
        } else {
            $this->sql(
                'UPDATE ' . static::$TABLE_NAME . ' 
                SET size = :size, contentOrder = :newContentOrder, align = :align, content = :content 
                WHERE userId = :userId AND contentOrder = :contentOrder AND tab = :tab', 
                [':size' => $profileContent->getSize(), ':newContentOrder' => $profileContent->getContentOrder(), ':align' => $profileContent->getAlign(), 
                ':content' => $profileContent->getContent(), ':userId' => $profileContent->getUserId(), ':contentOrder' => $blockOrderValue, ':tab' => $profileContent->getTab()]
            );
        }
        return $this;
    }

    private function updateElem(ProfileContent $profileContent, string $elem, $value, bool $schoolProfile = false)
    {
        if ($schoolProfile) {
            switch ($elem) {
            case 'contentOrder' :
                $this->sql(
                    'UPDATE ' . static::$TABLE_NAME . ' 
                    SET contentOrder = :newContentOrder 
                    WHERE schoolId = :schoolId AND contentOrder = :contentOrder AND tab = :tab', 
                    [':newContentOrder' => $value, ':schoolId' => $profileContent->getSchoolId(), ':contentOrder' => $profileContent->getContentOrder(), 
                    ':tab' => $profileContent->getTab()]
                );
                break;
            }
        } else {
            switch ($elem) {
            case 'contentOrder' :
                $this->sql(
                    'UPDATE ' . static::$TABLE_NAME . ' 
                    SET contentOrder = :newContentOrder 
                    WHERE userId = :userId AND contentOrder = :contentOrder AND tab = :tab', 
                    [':newContentOrder' => $value, ':userId' => $profileContent->getUserId(), ':contentOrder' => $profileContent->getContentOrder(), 
                    ':tab' => $profileContent->getTab()]
                );
                break;
            }
        }
        return $this;
    }

    private function updateWithoutChangingPlace(int $blockOrderValue, int $newOrderValue, int $idProfile, int $idProfileContent, string $type, string $size, string $align, string $content, bool $schoolProfile = false)
    {
        $schoolProfile ? $ownerId = 'schoolId' : $ownerId = 'userId';
        $this->update($blockOrderValue, new ProfileContent(
            [$ownerId => $idProfile, 
            'tab' => $type, 
            'size' => $size, 
            'contentOrder' => $newOrderValue, 
            'align' => $align, 
            'content' => $content]
        ), $schoolProfile);
        $newImgEntries = $this->checkForImgEntries($content);
        $this->updateImgEntries($idProfileContent, $newImgEntries);
    }

    private function updateAndChangePlace(int $blockOrderValue, int $newOrderValue, int $idProfile, int $idProfileContent, string $type, string $size, string $align, string $content, bool $schoolProfile = false)
    {
        //delete concerned profileContent
        $this->deleteByProfileId($idProfile, $type, $blockOrderValue, $schoolProfile);
        //moving each profileContent that need to
        $contentToUpdate = $this->getContentForUpdate($idProfile, $type, $blockOrderValue, $newOrderValue, $schoolProfile);
        if ($newOrderValue < $blockOrderValue) {
            $this->incrDecrOrder($contentToUpdate, 'increment', $schoolProfile);
        } else {
            $this->incrDecrOrder($contentToUpdate, 'decrement', $schoolProfile);
        }
        $schoolProfile ? $ownerId = 'schoolId' : $ownerId = 'userId';
        $this->add(new ProfileContent(
            [$ownerId => $idProfile, 
            'tab' => $type, 
            'size' => $size, 
            'contentOrder' => $newOrderValue, 
            'align' => $align, 
            'content' => $content]
        ));
        //checking previous and actual imgEntries to delete those who missing, and create those who don't exist yet
        //also, editing the field 'idProfileContent' for each img entries cause we delete and re-create the profileContent
        $newIdProfileContent = $this->getLastInsertId();
        $oldImgEntries = $this->getImgEntries($idProfileContent);
        $this->editIdProfileContent($oldImgEntries, $idProfileContent, $newIdProfileContent);
        $newImgEntries = $this->checkForImgEntries($content);
        $this->updateImgEntries($newIdProfileContent, $newImgEntries);
    }

    private function updateImgEntries(int $idProfileContent, array $newImgEntries)
    {
        //delete unused images and set entries for new images
        if ($idProfileContent > 0) {
            $oldImgEntries = $this->getImgEntries($idProfileContent);
            if (count($newImgEntries) > 0 && count($oldImgEntries) > 0) {
                //check if old entries stay on updated profileContent
                $this->checkImgEntriesOnUpdatedProfileContent($idProfileContent, $oldImgEntries, $newImgEntries);
                //then set new img entries
                if (count($newImgEntries) > 0) {
                    foreach ($newImgEntries as $entry) {
                        $this->setImgEntry($idProfileContent, $entry);
                    }
                }
            } elseif (count($newImgEntries) > 0) {
                //only new imgEntry, set them
                foreach ($newImgEntries as $entry) {
                    $this->setImgEntry($idProfileContent, $entry);
                }
            } elseif (count($oldImgEntries) > 0) {
                //no new imgEntry, delete the old one
                foreach ($oldImgEntries as $entry) {
                    $this->deleteImgEntry($idProfileContent, $entry['filePath']);
                }
            }
        }
    }

    private function getCount(int $profileId, string $tab, bool $schoolProfile = false)
    {
        if ($schoolProfile) {
            $query = $this->sql(
                'SELECT COUNT(*) 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE schoolId = :schoolId AND tab = :tab', 
                [':schoolId' => $profileId, ':tab' => $tab]
            );
        } else {
            $query = $this->sql(
                'SELECT COUNT(*) 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE userId = :userId AND tab = :tab', 
                [':userId' => $profileId, ':tab' => $tab]
            );
        }        
        $result = $query->fetch();        
        $query->closeCursor();
        return intval($result[0]);
    }

    private function editIdProfileContent(array $imgEntries, int $oldId, int $newId)
    {
        if (count($imgEntries) > 0 && $oldId > 0 && $newId > 0) {
            $this->sql(
                'UPDATE as_profile_content_img 
                SET idProfileContent = :newId 
                WHERE idProfileContent = :oldId', 
                [':newId' => $newId, ':oldId' => $oldId]
            );
        }
        return $this;
    }

    private function incrDecrOrder(array $contentToUpdate, string $operation, bool $schoolProfile = false, int $value = 1)
    {
        if (count($contentToUpdate) > 0) {
            foreach ($contentToUpdate as $content) {
                if ($operation === 'increment') {
                    $newOrderContent = intval($content->getContentOrder()) + $value;
                } elseif ($operation === 'decrement') {
                    $newOrderContent = intval($content->getContentOrder()) - $value;
                }
                $this->updateElem($content, 'contentOrder', $newOrderContent, $schoolProfile);
            }
        }
        return $this;
    }

    /*------------------------------ image entry ------------------------------*/

    private function getImgEntries(int $idProfileContent)
    {
        if ($idProfileContent > 0) {
            $q = $this->sql(
                'SELECT * 
                FROM as_profile_content_img 
                WHERE idProfileContent = :idProfileContent', 
                [':idProfileContent' => $idProfileContent]
            );
            return $q->fetchAll();
        }
    }

    private function getUnusedImg()
    {
        $q = $this->sql(
            'SELECT * 
            FROM as_profile_content_img 
            WHERE toDelete = 1'
        );
        return $q->fetchAll();
    }

    private function setImgEntry(int $idProfileContent, string $filePath)
    {
        if ($idProfileContent > 0 && strlen($filePath) > 0) {
            $this->sql(
                'INSERT INTO as_profile_content_img (idProfileContent, filePath) 
				VALUES(:idProfileContent, :filePath)', 
                [':idProfileContent' => $idProfileContent, ':filePath' => $filePath]
            );
        }
        return $this;
    }

    private function deleteImgEntry(int $idProfileContent, string $filePathInBdd)
    {
        if (strpos($filePathInBdd, 'http://') === 0 || strpos($filePathInBdd, 'https://') === 0) {
            $url = explode('/', $filePathInBdd);
            $filePath = $url[count($url) - 4] . '/' . $url[count($url) - 3] . '/' . $url[count($url) - 2] . '/' . $url[count($url) - 1];
        } else {
            $filePath = $filePathInBdd;
        }
        if ($idProfileContent > 0 && file_exists($filePath)) {
            $this->deleteFile($filePath);
            $this->sql(
                'DELETE FROM as_profile_content_img 
                WHERE idProfileContent = :idProfileContent AND filePath = :filePath', 
                [':idProfileContent' => $idProfileContent, ':filePath' => $filePathInBdd]
            );
        }
        return $this;
    }

    private function deleteAllImgEntries(int $idProfileContent)
    {
        $imgEntries = $this->getImgEntries($idProfileContent);
        if (count($imgEntries) > 0) {
            foreach ($imgEntries as $entry) {
                $this->deleteImgEntry($idProfileContent, $entry['filePath']);
            }
        }
        return $this;
    }    

    private function checkImgEntriesOnUpdatedProfileContent(int $idProfileContent, array $oldImgEntries, array $newImgEntries)
    {
        //check if old entries stay on updated profileContent
        for ($i = 0; $i < count($oldImgEntries); $i++) {
            $finded = false;
            for ($j = 0; $j < count($newImgEntries); $j++) {
                if ($oldImgEntries[$i]['filePath'] === $newImgEntries[$j]) {
                    // $oldImgEntries[$i] still on updated content, set bool 'toDelete' of oldEntries[$i] to false
                    // also delete the new uploaded img because is the same
                    if ($oldImgEntries[$i]['toDelete']) {
                           $this->unsetToDelete($oldImgEntries[$i]['id']);
                    }
                    unset($newImgEntries[$j]);
                    $finded = true;
                }
            }
            if (!$finded) {
                $this->deleteImgEntry($idProfileContent, $oldImgEntries[$i]['filePath']);
            }
        }
    }

    private function checkForImgEntries(string $content)
    {
        if (strlen($content) > 0) {
            $regex = '/src=\"(.+)\"/U';
            preg_match_all($regex, $content, $matches, PREG_OFFSET_CAPTURE);
            $imgEntries = [];
            if (!empty($matches[1])) {
                foreach ($matches[1] as $filePath) {
                    $imgEntries[] = $filePath[0];
                }
            }
            return($imgEntries);
        }
    }

    private function imgEntryExists(int $idProfileContent, string $filePath)
    {
        if ($idProfileContent > 0 && strlen($filePath) > 0) {
            $q = $this->sql(
                'SELECT * 
                FROM as_profile_content_img 
                WHERE idProfileContent = :idProfileContent AND filePath = :filePath', 
                [':idProfileContent' => $idProfileContent, ':filePath' => $filePath]
            );
            if ($result = $q->fetch()) {
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

    private function unsetToDelete(int $idEntry)
    {
        if ($idEntry > 0) {
            $this->sql(
                'UPDATE as_profile_content_img 
                SET toDelete = :toDelete 
                WHERE id = :idEntry', 
                [':toDelete' => 0, ':idEntry' => $idEntry]
            );
        }
        return $this;
    }
}
