<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\LikeManager;

class GroupedPostsManager extends LikeManager
{
    public static $OBJECT_TYPE = 'Chemin\ArtSchools\Model\GroupedPost';
    public static $TABLE_PK = 'id';

    public static $TABLE_NAME = 'as_grouped_post';

    public static $TABLE_CHAMPS ='id, idAuthor, idSchool, idGroup, filePath, urlVideo, fileType';
    

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PUBLIC FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    public function getGroupedPosts(int $idGroup)
    {
        if ($idGroup > 0) {
            $q = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE idGroup = :idGroup 
                ORDER BY id', 
                [':idGroup' => $idGroup]
            );

            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
            $q->closeCursor();
            return $result;
        } else {
            return [];
        }
    }

    public function uploadPost(array $POST, int $idSchool, $isSchoolPost = false)
    {
        if (defined('FRONTEND') && FRONTEND === true && $isSchoolPost) {
            // schoolPost from student 
            $POST['listAuthorizedGroups'] = 'none';
        } elseif ($POST['isPrivate']) {
            // private post
            if (!empty($POST['folder']) && $folder = $this->getOneById(intval($POST['folder']))) {
                if ($folder && ($_SESSION['grade'] === MODERATOR || $_SESSION['grade'] === ADMIN)) {
                    if ($POST['listAuthorizedGroups'] === null) {
                        $POST['listAuthorizedGroups'] = $folder->getAuthorizedGroups();
                    }
                }
            }
        } else {
            // public post
            $POST['listAuthorizedGroups'] = null;
        }

        if ($result = $this->uploadThumbnailPost($POST, $idSchool)) {
            $listTypeGroupedFile = explode(',', $POST['listTypeGroupedFile']);
            array_shift($listTypeGroupedFile);

            if (count($listTypeGroupedFile) < 1) {
                $PostsManager = new PostsManager();
                $PostsManager->deletePost($result['idGroup']);
                return false;
            }

            $everythingIsOk = true;
            for ($i = 0; $i < count($listTypeGroupedFile); $i++) {
                switch ($listTypeGroupedFile[$i]) {
                    case 'image':
                        if (!$this->uploadImageGroupedPost($idSchool, $result['idGroup'])) {
                            $everythingIsOk = false;
                        }
                    break;
    
                    case 'video':
                        if (!$this->uploadVideoGroupedPost($idSchool, $result['idGroup'], $POST['uploadFile' . $i])) {
                            $everythingIsOk = false;
                        }
                    break;
    
                    case 'compressed':
                        if (!$this->uploadOtherGroupedPost($idSchool, $result['idGroup'], $isSchoolPost)) {
                            $everythingIsOk = false;
                        }
                    break;
                }
            }

            if (!$everythingIsOk) {
                $PostsManager = new PostsManager();
                $PostsManager->deletePost($result['idGroup']);
                return false;
            }

            return $result['idGroup'];
        } else {
            return false;
        }
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PRIVATE FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/
    private function setPost(Post $Post)
    {
        $this->sql(
            'INSERT INTO as_post (idAuthor, idSchool ,title, filePath, urlVideo, description, isPrivate, authorizedGroups, postType, 
            fileType, onFolder, tags, datePublication) 
            VALUES(:idAuthor, :idSchool, :title, :filePath, :urlVideo, :description, :isPrivate, :authorizedGroups, :postType, :fileType, :onFolder, :tags, NOW())', 
            [':idAuthor' => $Post->getIdAuthor(), ':idSchool' => $Post->getIdSchool(), ':title' => $Post->getTitle(), ':filePath' => $Post->getFilePath(), ':urlVideo' => $Post->getUrlVideo(), 
            ':description' => $Post->getDescription(), ':isPrivate' => intval($Post->getIsPrivate()), ':authorizedGroups' => $Post->getAuthorizedGroups(), 
            ':postType' => $Post->getPostType(), ':fileType' => $Post->getFileType(), ':onFolder' => $Post->getOnFolder(), ':tags' => $Post->getTags()]
        );
        return $this;
    }

    private function setGroupedPost(GroupedPost $GroupedPost)
    {
        $this->sql(
            'INSERT INTO ' . static::$TABLE_NAME . ' (idAuthor, idSchool, idGroup, filePath, urlVideo, fileType) 
            VALUES(:idAuthor, :idSchool, :idGroup, :filePath, :urlVideo, :fileType)', 
            [':idAuthor' => $GroupedPost->getIdAuthor(), ':idSchool' => $GroupedPost->getIdSchool(), ':idGroup' => $GroupedPost->getIdGroup(), 
            ':filePath' => $GroupedPost->getFilePath(), ':urlVideo' => $GroupedPost->getUrlVideo(), ':fileType' => $GroupedPost->getFileType()]
        );

        return $this;
    }

    private function uploadThumbnailPost(array $POST, int $idSchool)
    {
        $arrAcceptedExtention = array("jpeg", "jpg", 'jfif', "png", "gif");
        require 'view/uploadThumbnailOfGrouped.php';

        if (!empty($finalPath)) {
            $this->setPost(
                new Post(
                    ['idAuthor' => $_SESSION['id'], 
                    'idSchool' => $idSchool,
                    'title' => $POST['title'], 
                    'filePath' => $finalPath, 
                    'description' => $POST['tinyMCEtextarea'], 
                    'isPrivate' => $POST['isPrivate'], 
                    'authorizedGroups' => $POST['listAuthorizedGroups'], 
                    'postType' => $POST['postType'], 
                    'fileType' => 'grouped', 
                    'onFolder' => $POST['folder'], 
                    'tags' => $POST['listTags']]
                )
            );

            return ['idGroup' => $this->getLastInsertId()];
        } else {
            return false;
        }
    }

    private function uploadGroupedPost(string $type)
    {
        switch ($type) {
            case 'image' :
                $arrAcceptedExtention = array("jpeg", "jpg", 'jfif', "png", "gif");
            break;

            case 'compressed' :
                $arrAcceptedExtention = array("zip", "rar", '7zip', '7z');
            break;
        }

        require 'view/uploadGrouped.php';
        return $finalPath;
    }

    private function uploadImageGroupedPost(int $idSchool, int $idGroup)
    {
        $finalPath = $this->uploadGroupedPost('image');
        if (!$finalPath) {
            return false;
        }

        $this->setGroupedPost(
            new GroupedPost(
                ['idAuthor' => $_SESSION['id'], 
                'idSchool' => $idSchool, 
                'idGroup' => $idGroup, 
                'filePath' => $finalPath, 
                'fileType' => 'image']
            )
        );

        return true;
    }

    private function uploadVideoGroupedPost(int $idSchool, int $idGroup, string $videoLink)
    {
        $filePath = null;

        $this->setGroupedPost(
            new GroupedPost(
                ['idAuthor' => $_SESSION['id'], 
                'idSchool' => $idSchool, 
                'idGroup' => $idGroup, 
                'filePath' => $filePath, 
                'urlVideo' => $videoLink, 
                'fileType' => 'video', ]
            )
        );
        return true;
    }

    private function uploadOtherGroupedPost(int $idSchool, int $idGroup, string $isSchoolPost)
    {
        if ($isSchoolPost) {
            $finalPath = $this->uploadGroupedPost('compressed');
            if (!$finalPath) {
                return false;
            }

            $this->setGroupedPost(
                new GroupedPost(
                    ['idAuthor' => $_SESSION['id'], 
                    'idSchool' => $idSchool, 
                    'idGroup' => $idGroup, 
                    'filePath' => $finalPath, 
                    'fileType' => 'compressed']
                )
            );
        } else {
            return false;
        }

        return true;
    }
}
