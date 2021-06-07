<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\AbstractManager;

class ForumReplyManager extends AbstractManager
{
    public static $REPLY_OBJECT_TYPE = 'Chemin\ArtSchools\Model\ForumReply';
    public static $REPLY_TABLE_NAME = 'as_forum_reply';
    public static $REPLY_TABLE_CHAMPS = 'r.id, r.idSchool, r.idTopic, r.idAuthor, u.profilePictureInfo AS profilePictureInfoAuthor, 
        u.pseudo AS pseudoAuthor, u.firstName AS firstNameAuthor, u.lastName AS lastNameAuthor, 
        u.isAdmin AS authorIsAdmin, u.isModerator AS authorIsModerator, r.content, DATE_FORMAT(r.datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication';
    public static $TABLE_PK = 'id';

    public function getReply(int $idReply)
    {
        if ($idReply > 0) {
            $q = $this->sql(
                'SELECT ' . static::$REPLY_TABLE_CHAMPS . ' 
                FROM ' . static::$REPLY_TABLE_NAME . ' AS r 
                LEFT JOIN as_user AS u 
                ON u.id = r.idAuthor 
                WHERE r.id = :id', 
                [':id' => $idReply]
            );
            $result = $q->fetchObject(static::$REPLY_OBJECT_TYPE);
            $q->closeCursor();

            return $result;
        } else {
            return false;
        }
    }

    public function getReplies(int $idTopic, $clauseLimit = "")
    {
        if ($idTopic > 0) {
            $q = $this->sql(
                'SELECT ' . static::$REPLY_TABLE_CHAMPS . ' 
                FROM ' . static::$REPLY_TABLE_NAME . ' AS r 
                LEFT JOIN as_user AS u 
                ON u.id = r.idAuthor 
                WHERE r.idTopic = :idTopic' 
                . $clauseLimit, 
                [':idTopic' => $idTopic]
            );
            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$REPLY_OBJECT_TYPE);
            $q->closeCursor();

            return $result;
        } else {
            return false;
        }
    }

    public function setReply(string $content, user $user, School $school, ForumTopic $topic)
    {
        $this->sql(
            'INSERT INTO ' . static::$REPLY_TABLE_NAME . ' (idSchool, idTopic, idAuthor, content, datePublication) 
            VALUES (:idSchool, :idTopic, :idAuthor, :content, NOW())', 
            [
                ':idSchool' => $school->getId(), ':idTopic' => $topic->getId(), 
                ':idAuthor' => $user->getId(), ':content' => $content
            ]
        );
    }

    public function updateReply($content, $idReply)
    {
        $this->sql(
            'UPDATE ' . static::$REPLY_TABLE_NAME . ' 
            SET content = :content 
            WHERE id = :idReply', 
            [
                ':idReply' => $idReply, ':content' => $content
            ]
        );
    }

    public function deleteReply(object $reply)
    {
        // search for img on reply content
        if ($reply && !empty($reply->getContent())) {
            $filePaths = $this->extractFilePath($this->checkForImgEntries($reply->getContent()));
            if (count($filePaths) > 0) {
                foreach($filePaths as $filePath) {
                    $this->deleteFile($filePath);
                }
            }
        }

        // delete reply
        $this->sql(
            'DELETE FROM ' . static::$REPLY_TABLE_NAME . ' 
            WHERE ' . static::$TABLE_PK . ' = :id', 
            [':id' => $reply->getId()]
        );
    }

    public function getCountReply(int $idTopic = null)
    {
        if ($idTopic) {
            $q = $this->sql(
                'SELECT COUNT(*) 
                FROM ' . static::$REPLY_TABLE_NAME . ' 
                WHERE idTopic = :idTopic', 
                [':idTopic' => $idTopic]
            );
            
            $result = $q->fetch();
            $q->closeCursor();

            return intval($result[0]);
        } else {
            return 0;
        }
    }

    public function checkUpdatedElemContent($oldImgEntries = [], $newImgEntries = [])
    {
        for ($i = 0; $i < count($oldImgEntries); $i++) {
            if (!in_array($oldImgEntries[$i], $newImgEntries)) {
                $this->deleteFile($oldImgEntries[$i]);
            }
        }
    }
}
