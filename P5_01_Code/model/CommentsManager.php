<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\AbstractManager;

class CommentsManager extends AbstractManager
{
    public static $OBJECT_TYPE = 'Chemin\ArtSchools\Model\Comment';

    public static $TABLE_NAME = 'as_comment';
    public static $TABLE_USER_NAME = 'as_user';

    public static $TABLE_CHAMPS = 'id, idPost, idAuthor, content, DATE_FORMAT(datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication';
    public static $TABLE_CHAMPS_WITH_USER = 'c.id, c.idPost, c.idAuthor, c.content, DATE_FORMAT(c.datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication, 
        u.school AS authorSchoolname, u.firstName AS firstNameAuthor, u.lastName AS lastNameAuthor, 
        u.profilePictureInfo AS profilePictureAuthor, u.isAdmin AS authorIsAdmin, u.isModerator AS authorIsModerator';

    public static $TABLE_PK = 'id';

    public function getFromPost(int $idPost, int $limit = 10, int $offset = 0, string $order = 'DESC')
    {
        if ($idPost > 0) {
            $comments = [];
            $q = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS_WITH_USER . ' 
                FROM ' . static::$TABLE_NAME . ' AS c 
                LEFT JOIN ' . static::$TABLE_USER_NAME . ' AS u 
                ON u.id = c.idAuthor 
                WHERE c.idPost = :idPost 
                ORDER BY c.id ' . $order . ' 
                LIMIT ' . $limit . ' OFFSET ' . $offset, 
                [':idPost' => $idPost]
            );
            while ($result = $q->fetch()) {
                $comments[] = new Comment($result);
            }
            $q->closeCursor();
            return $comments;
        } else {
            return false;
        }
    }

    public function setComment(array $POST, user $user)
    {
        if ($this->checkForScriptInsertion($POST)) {
            $this->sql(
                'INSERT INTO ' . static::$TABLE_NAME . ' (idPost, content, idAuthor, datePublication) 
				VALUES (:idPost, :content, :idAuthor, NOW())', 
                [':idPost' => $POST['idPost'], ':content' => trim($POST['commentContent']), ':idAuthor' => $user->getId()]
            );
            return $this->getLastInsertId();
        } else {
            return false;
        }
    }

    public function getCountComments(int $idPost = null)
    {
        if ($idPost) {
            $q = $this->sql(
                'SELECT COUNT(*) 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE idPost = :idPost', 
                [':idPost' => $idPost]
            );
            $result = $q->fetch();
            $q->closeCursor();

            return intval($result[0]);
        } else {
            return 0;
        }
    }

    public function toArray($elem)
    {
        //$elem must be a Comment (Object) or array of Comment (Object)
        if (is_array($elem)) {
            $result = [];
            foreach ($elem as $comment) {
                $result[] = $this->objectCommentToArray($comment);
            }
        } else if (is_object($elem)) {
            $result = $this->objectCommentToArray($elem);
        }
        return $result;
    }

    private function objectCommentToArray(Comment $comment)
    {
        $arr = [
            'id' => $comment->getId(), 
            'idPost' => $comment->getIdPost(), 
            'idAuthor' => $comment->getIdAuthor(), 
            'authorSchoolName' => $comment->getAuthorSchoolName(), 
            'firstNameAuthor' => $comment->getFirstNameAuthor(), 
            'lastNameAuthor' => $comment->getLastNameAuthor(), 
            'profilePictureAuthor' => $comment->getProfilePictureAuthor(), 
            'content' => $comment->getContent(), 
            'datePublication' => $comment->getDatePublication(), 
            'authorIsAdmin' => $comment->getAuthorIsAdmin(), 
            'authorIsModerator' => $comment->getAuthorIsModerator()
        ];
        return $arr;
    }
}
