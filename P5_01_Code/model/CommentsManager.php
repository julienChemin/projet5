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

    public function getFromPost(int $idPost, string $order = 'DESC')
    {
        if ($idPost > 0) {
            $comments = [];
            $q = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS_WITH_USER . ' 
                FROM ' . static::$TABLE_NAME . ' AS c 
                LEFT JOIN ' . static::$TABLE_USER_NAME . ' AS u 
                ON u.id = c.idAuthor 
                WHERE c.idPost = :idPost 
                ORDER BY c.datePublication ' . $order, 
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
            return true;
        } else {
            return false;
        }
    }
}
