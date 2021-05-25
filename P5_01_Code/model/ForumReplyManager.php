<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\AbstractManager;

class ForumReplyManager extends AbstractManager
{
    public static $REPLY_OBJECT_TYPE = 'Chemin\ArtSchools\Model\ForumReply';
    public static $REPLY_TABLE_NAME = 'as_forum_reply';
    public static $REPLY_TABLE_CHAMPS = 'r.id, r.idSchool, r.idTopic, r.idAuthor, u.profilePictureInfo AS profilePictureInfoAuthor, 
        u.pseudo AS pseudoAuthor, u.firstName AS firstNameAuthor, u.lastName AS lastNameAuthor, r.content, DATE_FORMAT(r.datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication';
    public static $TABLE_PK = 'id';

    public function getReply(int $idReply)
    {
        if ($idReply > 0) {
            $q = $this->sql(
                'SELECT ' . static::$REPLY_TABLE_CHAMPS . ' 
                FROM ' . static::$REPLY_TABLE_NAME . ' AS r 
                LEFT JOIN as_user AS u 
                ON u.id = r.idAuthor 
                WHERE id = :id', 
                [':id' => $idReply]
            );
            $result = $q->fetchObject(static::$REPLY_OBJECT_TYPE);
            $q->closeCursor();

            return $result;
        } else {
            return false;
        }
    }

    public function getReplies(int $idTopic)
    {
        if ($idTopic > 0) {
            $q = $this->sql(
                'SELECT ' . static::$REPLY_TABLE_CHAMPS . ' 
                FROM ' . static::$REPLY_TABLE_NAME . ' AS r 
                LEFT JOIN as_user AS u 
                ON u.id = r.idAuthor 
                WHERE idTopic = :idTopic', 
                [':idTopic' => $idTopic]
            );
            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$REPLY_OBJECT_TYPE);
            $q->closeCursor();

            return $result;
        } else {
            return false;
        }
    }

    public function setReply(array $POST, user $user)
    {
        if ($this->checkForScriptInsertion($POST)) {
            $this->sql(
                'INSERT INTO ' . static::$REPLY_TABLE_NAME . ' (idSchool, idTopic, idAuthor, content, datePublication) 
				VALUES (:idSchool, :idTopic, :idAuthor, :content, NOW())', 
                [':idSchool' => $POST['idSchool'], ':idTopic' => $POST['idTopic'], ':idAuthor' => $user->getId(), ':content' => trim($POST['content'])]
            );
            return true;
        } else {
            return false;
        }
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
}
