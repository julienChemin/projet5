<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\LikeManager;

class PostsManager extends LikeManager
{
    public static $OBJECT_TYPE = 'Chemin\ArtSchools\Model\GroupedPost';
    public static $TABLE_PK = 'id';

    public static $TABLE_NAME = 'as_grouped_post';

    public static $TABLE_CHAMPS ='id, idAuthor, idSchool, mainPost, title, filePath, urlVideo, description, fileType';
    

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PUBLIC FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    public function getPostsByAuthor(int $idAuthor, int $offset = 0, int $limit = null)
    {
        if ($idAuthor > 0) {
            if (!empty($limit)) {
                $q = $this->sql(
                    'SELECT ' . static::$TABLE_CHAMPS . ' 
                    FROM ' . static::$TABLE_NAME . ' 
                    WHERE idAuthor = :idAuthor AND postType = "userPost" AND isPrivate = "0" 
                    ORDER BY id DESC 
                    LIMIT :limit OFFSET :offset', 
                    [':idAuthor' => $idAuthor, ':offset' => $offset, ':limit' => $limit]
                );
            } else {
                $q = $this->sql(
                    'SELECT ' . static::$TABLE_CHAMPS . ' 
                    FROM ' . static::$TABLE_NAME . ' 
                    WHERE idAuthor = :idAuthor AND postType = "userPost" AND isPrivate = "0" 
                    ORDER BY id DESC', 
                    [':idAuthor' => $idAuthor]
                );
            }
            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
            $q->closeCursor();
            return $result;
        } else {
            return [];
        }
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PRIVATE FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/
    private function set(Post $Post)
    {
        $this->sql(
            'INSERT INTO ' . static::$TABLE_NAME . ' (idAuthor, idSchool ,title, filePath, urlVideo, description, isPrivate, authorizedGroups, postType, 
            fileType, onFolder, tags, datePublication) 
            VALUES(:idAuthor, :idSchool, :title, :filePath, :urlVideo, :description, :isPrivate, :authorizedGroups, :postType, :fileType, :onFolder, :tags, NOW())', 
            [':idAuthor' => $Post->getIdAuthor(), ':idSchool' => $Post->getIdSchool(), ':title' => $Post->getTitle(), ':filePath' => $Post->getFilePath(), ':urlVideo' => $Post->getUrlVideo(), 
            ':description' => $Post->getDescription(), ':isPrivate' => intval($Post->getIsPrivate()), ':authorizedGroups' => $Post->getAuthorizedGroups(), 
            ':postType' => $Post->getPostType(), ':fileType' => $Post->getFileType(), ':onFolder' => $Post->getOnFolder(), ':tags' => $Post->getTags()]
        );
        return $this;
    }
}
