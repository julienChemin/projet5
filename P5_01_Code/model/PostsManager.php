<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\LikeManager;

class PostsManager extends LikeManager
{
    public static $OBJECT_TYPE = 'Chemin\ArtSchools\Model\Post';
    public static $TABLE_PK = 'id';

    public static $TABLE_NAME = 'as_post';
    public static $TABLE_COMMENTS = 'as_comment';

    public static $TABLE_CHAMPS ='id, idAuthor, school, title, filePath, urlVideo, description, 
        DATE_FORMAT(datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication, isPrivate, 
        authorizedGroups, postType, fileType, onFolder, tags, nbLike';
    public static $TABLE_CHAMPS_WITH_COMMENTS ='a.id, a.idAuthor, a.school, a.title, a.filePath, a.urlVideo, 
        a.description, DATE_FORMAT(a.datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication, a.isPrivate, 
        a.authorizedGroups, a.postType, a.fileType, a.onFolder, a.tags, a.nbLike, 
        c.id AS idComment, c.idPost AS commentIdPost, c.idAuthor AS commentIdAuthor, c.NameAuthor AS commentNameAuthor, 
        c.profilePictureAuthor AS commentProfilePictureAuthor, c.content AS commentContent, 
        DATE_FORMAT(c.datePublication, "%d/%m/%Y à %H:%i.%s") AS commentDatePublication';
    

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

    public function getPostsBySchool(string $school, bool $withFolder = false, int $offset = 0, int $limit = null)
    {
        //get users posts affiliated to $school
        if (strlen($school) > 0) {
            if ($withFolder) {
                $q = $this->getPostsBySchoolWithFolder($school, $offset, $limit);
            } else {
                $q = $this->getPostsBySchoolWithoutFolder($school, $offset, $limit);
            }
            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
            $q->closeCursor();
            return $result;
        } else {
            return [];
        }
    }

    public function getSchoolPosts(string $school)
    {
        if (strlen($school) > 0) {
            $q = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE school = :school AND postType = "schoolPost" 
                ORDER BY id DESC', 
                [':school' => $school]
            );
            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
            $q->closeCursor();
            return $result;
        } else {
            return [];
        }
    }

    public function getPostsByTag(string $tag, int $limit = null, int $offset = 0)
    {
        $TagsManager = new TagsManager();
        if ($TagsManager->exists($tag)) {
            $regex = "'(," . $tag . ",.+)|(," . $tag . "$)'";
            if (!empty($limit)) {
                $q = $this->sql(
                    'SELECT ' . static::$TABLE_CHAMPS . ' 
                    FROM ' . static::$TABLE_NAME . ' 
                    WHERE tags REGEXP ' . $regex . ' 
                    ORDER BY id DESC 
                    LIMIT :limit OFFSET :offset', 
                    [':limit' => $limit, ':offset' => $offset]
                );
            } else {
                $q = $this->sql(
                    'SELECT ' . static::$TABLE_CHAMPS . ' 
                    FROM ' . static::$TABLE_NAME . ' 
                    WHERE tags REGEXP ' . $regex . ' 
                    ORDER BY id DESC'
                );
            }
            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
            $q->closeCursor();
            return $result;
        } else {
            return [];
        }
    }

    public function getPostsOnFolder(int $idFolder)
    {
        if ($idFolder > 0) {
            $q = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE onFolder = :idFolder', 
                [':idFolder' => $idFolder]
            );
            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
            $q->closeCursor();
            return $result;
        } else {
            return [];
        }
    }

    public function getMostLikedPosts(int $limit = null, int $offset = 0, string $school = null)
    {
        if (!empty($school)) {
            //by school
            $q = $this->getMostLikedPostsBySchool($limit, $offset, $school);
        } else {
            //all school
            $q = $this->getMostLikedPostsAllSchool($limit, $offset);
        }
        $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
        $q->closeCursor();
        return $result;
    }

    public function getLastPosted(int $limit = null, int $offset = 0, string $schoolName = null)
    {
        !empty($schoolName) ? $clauseWhere = 'AND school = "' . $schoolName . ' "' : $clauseWhere = '';
        if (!empty($limit)) {
            $q = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE postType = "userPost" AND isPrivate = "0" AND tags != "null" ' . $clauseWhere . ' 
                ORDER BY id DESC 
                LIMIT :limit OFFSET :offset', 
                [':offset' => $offset, ':limit' => $limit]
            );
        } else {
            $q = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE postType = "userPost" AND isPrivate = "0" AND tags != "null" ' . $clauseWhere . '
                ORDER BY id DESC'
            );
        }
        $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
        $q->closeCursor();
        return $result;
    }

    public function getCountReferencedPosts(string $schoolName = null)
    {
        !empty($schoolName) ? $clauseWhere = 'AND school = "' . $schoolName . '"' : $clauseWhere = '';
        $q = $this->sql(
            'SELECT COUNT(*) 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE postType = "userPost" AND isPrivate = "0" AND tags != "null" ' . $clauseWhere
        );
        $result = $q->fetch();
        return intval($result[0]);
    }

    public function getCountPostsBySchool(string $school, bool $withFolder = false)
    {
        if (strlen($school) > 0) {
            if ($withFolder) {
                $q = $this->sql(
                    'SELECT COUNT(*) 
                    FROM ' . static::$TABLE_NAME . ' 
                    WHERE school = :school AND postType = "userPost" AND onFolder IS NULL AND isPrivate = "0"', 
                    [':school' => $school]
                );
            } else {
                $q = $this->sql(
                    'SELECT COUNT(*) 
                    FROM ' . static::$TABLE_NAME . ' 
                    WHERE school = :school AND postType = "userPost" AND fileType != "folder" AND isPrivate = "0"', 
                    [':school' => $school]
                );
            }
            $result = $q->fetch();
            $q->closeCursor();
            return intval($result[0]);
        }
    }

    public function getCountPostsByTag(string $tag = null)
    {
        $TagsManager = new TagsManager();
        if ($tag !== null && $TagsManager->exists($tag)) {
            $regex = "'(," . $tag . ",.+)|(," . $tag . "$)'";
            $q = $this->sql(
                'SELECT COUNT(*) 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE tags REGEXP ' . $regex . ' 
                ORDER BY id DESC'
            );
            $result = $q->fetch();
            $q->closeCursor();
            return intval($result[0]);
        } else {
            return 0;
        }
    }

    public function deletePost(int $postId)
    {
        $post = $this->getOneById($postId);
        // delete file (img or compressed)
        if (!empty($post->getFilePath())) {
            $this->deleteFile($post->getFilePath());
        }
        // search for img on description
        if (!empty($post->getDescription())) {
            $filePaths = $this->extractFilePath($this->checkForImgEntries($post->getDescription()));
            if (count($filePaths) > 0) {
                foreach($filePaths as $filePath) {
                    $this->deleteFile($filePath);
                }
            }
        }
        $this->delete($post->getId());
        return $this;
    }

    public function deleteFolder(int $idFolder)
    {
        if ($this->exists($idFolder)) {
            $postsOnFolder = $this->getPostsOnFolder($idFolder);
            if (count($postsOnFolder) > 0) {
                foreach ($postsOnFolder as $post) {
                    if ($post->getFileType() === 'folder') {
                        $this->deleteFolder($post->getId());
                    } else {
                        $this->deletePost($post->getId());
                    }
                }
            }
            $this->deletePost($idFolder);
        }
        return $this;
    }

    public function toArray($elem)
    {
        //$elem must be a Post (Object) or array of Post (Object)
        if (is_array($elem)) {
            $result = [];
            foreach ($elem as $post) {
                $result[] = $this->objectPostToArray($post);
            }
        } elseif (is_object($elem)) {
            $result = $this->objectPostToArray($elem);
        }
        return $result;
    }

    public function searchForKeyWord($word)
    {
        $result = [];
        $regex = "'.*" . $word . ".*'";
        $q = $this->sql(
            'SELECT ' . static::$TABLE_CHAMPS . ' 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE postType = "userPost" AND tags != "null" AND isPrivate = "0" AND title REGEXP ' . $regex
        );
        $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
        return $result;
    }

    public function advancedSearch(array $POST, int $limit = 12, int $offset = 0)
    {
        // return posts depending of the search arguments
        $values = $this->getValuesForAdvancedSearch($POST, $limit, $offset);
        $result['posts'] = $this->getPostsForAdvancedSearch($values['clauseWhere'], $values['clauseOrderBy'], $values['arrValue']);
        $result['count'] = $this->getPostsCountForAdvancedsearch($values['clauseWhere'], $values['clauseOrderBy'], $values['arrValueForCount']);
        return $result;
    }

    public function canPostOnFolder(Post $post, User $user)
    {
        if (!empty($_SESSION['pseudo']) && $post->getFileType() === 'folder') {
            if ($post->getIdAuthor() === $user->getId()) {
                // folder belong to user
                return true;
            } elseif ($post->getPostType() === "schoolPost" && $post->getSchool() === $user->getSchool() && ($user->getIsModerator() || $user->getIsAdmin())) {
                // school folder -> admin and moderator can post 
                return true;
            } elseif ($post->getPostType() === "schoolPost" && $post->getSchool() === $user->getSchool() && $user->getIsActive() && $post->getIsPrivate() 
            && ($post->getAuthorizedGroups() !== 'none' && (empty($post->getListAuthorizedGroups()) || in_array($user->getSchoolGroup(), $post->getListAuthorizedGroups())))) {
                // private school folder -> authorized user can post
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getListAuthorizedGroups(array $schoolList = null, $folderList = null, bool $onPrivateFolder = false)
    {
        $response = [];
        if (!$onPrivateFolder) {
            $response['all'] = 'Tous les groupes';
            if (!empty($schoolList)) {
                foreach ($schoolList as $group) {
                    $response[$group] = $group;
                }
            }
        } else {
            if ($schoolList === null) {
                $response['all'] = 'Tous les groupes';
            } else {
                if ($folderList === null) {
                    $response['all'] = 'Tous les groupes';
                    foreach ($schoolList as $group) {
                        $response[$group] = $group;
                    }
                } elseif ($folderList !== 'none') {
                    foreach ($folderList as $group) {
                        $response[$group] = $group;
                    }
                }
            }
        }
        return $response;
    }

    public function canUploadPost(string $type, User $user, array $arrPOST, TagsManager $TagsManager)
    {
        if (!empty($arrPOST['fileTypeValue']) && $this->checkForScriptInsertion([$arrPOST])) {
            // check folder value and if user can post on it
            $folder = null;
            $OK = false;
            if (empty($arrPOST['folder'])) {
                $OK = true;
            } elseif (!empty($arrPOST['folder']) && $folder = $this->getOneById(intval($arrPOST['folder']))){
                if ($this->canPostOnFolder($folder, $user)) {
                    $OK = true;
                }
            }
            // check previous result and some other value depending on fileType
            if ($OK && $response = $this->checkValueDependingOnFileType($arrPOST, $type, $user, $folder, $TagsManager)) {
                // user can upload post -> 'setupContent' move uploaded img from 'temp' to 'dl' folder and update filePaths on post description
                $response['tinyMCEtextarea'] = $this->moveImgAndUpdateContent($response['tinyMCEtextarea'], 'public/images/dl');
                if ($response['tinyMCEtextarea'] !== false) {
                    return $response;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function uploadPost(array $POST, $isSchoolPost = false)
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
        switch ($POST['fileTypeValue']) {
            case 'image':
                return $this->uploadImagePost($POST);
                break;
            case 'video':
                return $this->uploadVideoPost($POST);
                break;
            case 'compressed':
                return $this->uploadOtherPost($POST, $isSchoolPost);
                break;
            case 'folder':
                return $this->uploadFolder($POST);
                break;
        }
    }

    public function sortForProfile($posts)
    {
        $arrSortedPosts = ['folder' => [], 'private' => [], 'public' => []];
        foreach ($posts as $post) {
            //sort post by onFolder, public and private
            if ($post->getOnFolder() !== null) {
                $idFolder = $post->getOnFolder();
                if ($post->getIsPrivate()) {
                    // private post on folder
                    if ($this->userCanSeePrivatePost($post)) {
                        if (!isset($arrSortedPosts['folder'][$idFolder])) {
                            $arrSortedPosts['folder'][$idFolder] = [];
                        }
                        $arrSortedPosts['folder'][$idFolder][] = $this->toArray($post);
                    }
                } else {
                    // public post on folder
                    if (!isset($arrSortedPosts['folder'][$idFolder])) {
                        $arrSortedPosts['folder'][$idFolder] = [];
                    }
                    $arrSortedPosts['folder'][$idFolder][] = $this->toArray($post);
                }
            } elseif ($post->getIsPrivate()) {
                // private post
                if ($this->userCanSeePrivatePost($post)) {
                    $arrSortedPosts['private'][] = $this->toArray($post);
                }
            } else {
                // public post
                $arrSortedPosts['public'][] = $this->toArray($post);
            }
        }
        return $arrSortedPosts;
    }

    public function getAsidePosts(Post $post, TagsManager $TagsManager)
    {
        //return posts to display on post view and folder view as aside
        $asidePosts = [];
        $post->getPostType() === 'userPost' ? $posts = $this->sortForProfile($this->getPostsByAuthor($post->getIdAuthor())) : $posts = $this->sortForProfile($this->getSchoolPosts($post->getSchool()));
        $postedByUser = array_merge($posts['public'], $posts['private']);
        foreach ($posts['folder'] as $arr) {
            $postedByUser = array_merge($postedByUser, $arr);
        }

        $asidePosts['postType'] = $post->getPostType();
        $asidePosts['lastPosted'] = $this->getLastPosted(6);
        if (!empty($posts['folder'][$post->getOnFolder()])) {
            $asidePosts['onFolder'] = $this->getElemOnArray($posts['folder'][$post->getOnFolder()], $post->getId());
        }
        $asidePosts['public'] = $this->getElemOnArray($postedByUser, $post->getId());
        if (!empty($post->getTags())) {
            $asidePosts['withTag'] = $this->getPostsByRandomTagsForAside($post, $TagsManager);
        }
        return $asidePosts;
    }

    public function getPostsForHome(SchoolManager $SchoolManager, TagsManager $TagsManager, int $qtt = 6)
    {
        //return posts for home
        $homePosts = [];
        $homePosts['lastPosted'] = $this->getLastPosted($qtt);
        $homePosts['mostLiked'] = $this->getMostLikedPosts($qtt);
        //pick 4 most popular tag then pick '$qtt' posts of them
        $mostPopularTags = $this->getElemOnArray($TagsManager->getMostPopularTags($qtt), -1, true, 4);
        $arrPostsByTags = [];
        foreach ($mostPopularTags as $tag) {
            $posts = $this->getPostsByTag($tag['name'], 100);
            $arrPostsByTags[$tag['name']] = $this->getElemOnArray($posts, -1, true, $qtt);
        }
        $homePosts['withTag'] = $arrPostsByTags;
        //pick 2 school random then pick '$qtt' posts of them
        $noSchool = $SchoolManager->getSchoolByName(NO_SCHOOL);
        $allSchool = $SchoolManager->getSchoolByName(ALL_SCHOOL);
        $randomSchool = $this->getElemOnArray($allSchool, $noSchool->getId(), true, 2);
        $arrPostsBySchool = [];
        foreach ($randomSchool as $school) {
            $posts = $this->getPostsBySchool($school->getName(), false, 0, 100);
            $arrPostsBySchool[$school->getName()] = $this->getElemOnArray($posts, -1, true, $qtt);
        }
        $homePosts['bySchool'] = $arrPostsBySchool;
        return $homePosts;
    }

    public function userCanSeePost($user, Post $post)
    {
        if (!empty($post)) {
            if (!$post->getIsPrivate()) {
                //public post
                return true;
            } elseif (!empty($user)) {
                //private post
                return $this->userCanSeePrivatePost($post, $user);
            }
        } else {
            return false;
        }
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PRIVATE FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/
    private function set(Post $Post)
    {
        $this->sql(
            'INSERT INTO ' . static::$TABLE_NAME . ' (idAuthor, school ,title, filePath, urlVideo, description, isPrivate, authorizedGroups, postType, 
            fileType, onFolder, tags, datePublication) 
            VALUES(:idAuthor, :school, :title, :filePath, :urlVideo, :description, :isPrivate, :authorizedGroups, :postType, :fileType, :onFolder, :tags, NOW())', 
            [':idAuthor' => $Post->getIdAuthor(), ':school' => $Post->getSchool(), ':title' => $Post->getTitle(), ':filePath' => $Post->getFilePath(), ':urlVideo' => $Post->getUrlVideo(), 
            ':description' => $Post->getDescription(), ':isPrivate' => intval($Post->getIsPrivate()), ':authorizedGroups' => $Post->getAuthorizedGroups(), 
            ':postType' => $Post->getPostType(), ':fileType' => $Post->getFileType(), ':onFolder' => $Post->getOnFolder(), ':tags' => $Post->getTags()]
        );
        return $this;
    }

    private function objectPostToArray(Post $post)
    {
        $arr = ['id' => $post->getId(),
        'idAuthor' => $post->getIdAuthor(), 
        'school' => $post->getSchool(), 
        'title' => $post->getTitle(), 
        'filePath' => $post->getFilePath(), 
        'urlVideo' => $post->getUrlVideo(), 
        'description' => $post->getDescription(), 
        'datePublication' => $post->getDatePublication(), 
        'isPrivate' => $post->getIsPrivate(), 
        'authorizedGroups' => $post->getAuthorizedGroups(), 
        'listAuthorizedGroups' => $post->getListAuthorizedGroups(), 
        'postType' => $post->getPostType(), 
        'fileType' => $post->getFileType(), 
        'onFolder' => $post->getOnFolder(),
        'listTags' => $post->getListTags()];
        return $arr;
    }

    private function getElemOnArray(array $elements, int $unwantedElem = -1, bool $random = true, int $qtt = 6)
    {
        //'unwantedElem' is the id of unwanted elem, this is a specific treatment for posts
        //can work for other elem if array $elements have one of this form : (array) $elements[(int) index][(int) 'id'] OR (array) $elements[(int) index][(int) object->getId()]
        //after doing this function, i discovered array.rand...whatever
        if (count($elements) > 0 && $qtt > 0) {
            if ($random) {
                return $this->getRandomElemOnArray($elements, $unwantedElem, $qtt);
            } else {
                
                return $this->getNonRandomElemOnArray($elements, $unwantedElem, $qtt);
            }
        } else {
            return [];
        }
    }

    private function getRandomElemOnArray(array $elements, int $unwantedElem, int $qtt)
    {
        $response = [];
        while (count($elements) > 0 && count($response) < $qtt) {
            do {
                $badNumber = false;
                $randomNumber = rand(0, (count($elements) - 1));
                if ($unwantedElem !== -1 && ((is_array($elements[$randomNumber]) && intval($elements[$randomNumber]['id']) === $unwantedElem) 
                || (is_object($elements[$randomNumber]) && intval($elements[$randomNumber]->getId()) === $unwantedElem))) {
                    $badNumber = true;
                    unset($elements[$randomNumber]);
                    $elements = $this->arrayWithoutEmptyEntries($elements);
                }
            } while ($badNumber && count($elements) > 0);
            if (count($elements) > 0) {
                $response[] = $elements[$randomNumber];
                unset($elements[$randomNumber]);
                $elements = $this->arrayWithoutEmptyEntries($elements);
            }
        }
        return $response;
    }
    
    private function getNonRandomElemOnArray(array $elements, int $unwantedElem, int $qtt)
    {
        $response = [];
        $i = 0;
        while (count($elements) > 0 && count($response) < $qtt) {
            $i++;
            if ($unwantedElem !== -1 && ((is_array($elements[$i]) && intval($elements[$i]['id']) === $unwantedElem) 
            || (is_object($elements[$i]) && intval($elements[$i]->getId()) === $unwantedElem))) {
                unset($elements[$i]);
                $elements = $this->arrayWithoutEmptyEntries($elements);
            } else {
                $response[] = $elements[$i];
                unset($elements[$i]);
                $elements = $this->arrayWithoutEmptyEntries($elements);
            }
        }
        return $response;
    }

    private function getPostsBySchoolWithFolder(string $school, int $offset, int $limit)
    {
        if (!empty($limit)) {
            return $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE school = :school AND postType = "userPost" AND onFolder IS NULL AND isPrivate = "0" AND tags != "null" 
                ORDER BY id DESC 
                LIMIT :limit OFFSET :offset', 
                [':school' => $school, ':offset' => $offset, ':limit' => $limit]
            );
        } else {
            return $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE school = :school AND postType = "userPost" AND onFolder IS NULL AND isPrivate = "0" AND tags != "null" 
                ORDER BY id DESC', 
                [':school' => $school]
            );
        }
    }

    private function getPostsBySchoolWithoutFolder(string $school, int $offset, int $limit)
    {
        if (!empty($limit)) {
            return $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE school = :school AND postType = "userPost" AND fileType != "folder" AND isPrivate = "0" AND tags != "null" 
                ORDER BY id DESC 
                LIMIT :limit OFFSET :offset', 
                [':school' => $school, ':offset' => $offset, ':limit' => $limit]
            );
        } else {
            return $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE school = :school AND postType = "userPost" AND fileType != "folder" AND isPrivate = "0" AND tags != "null" 
                ORDER BY id DESC', 
                [':school' => $school]
            );
        }
    }

    private function getMostLikedPostsBySchool(int $limit, int $offset, string $school)
    {
        if (!empty($limit)) {
            return $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE school = :school AND postType = "userPost" AND fileType != "folder" AND isPrivate = "0" AND tags != "null" 
                ORDER BY nbLike DESC 
                LIMIT :limit OFFSET :offset', 
                [':school' => $school, ':limit' => $limit, ':offset' => $offset]
            );
        } else {
            return $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE school = :school AND postType = "userPost" AND fileType != "folder" AND isPrivate = "0" AND tags != "null" 
                ORDER BY nbLike DESC', 
                [':school' => $school]
            );
        }
    }

    private function getMostLikedPostsAllSchool(int $limit, int $offset)
    {
        if (!empty($limit)) {
            return $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE postType = "userPost" AND fileType != "folder" AND isPrivate = "0" AND tags != "null" 
                ORDER BY nbLike DESC 
                LIMIT :limit OFFSET :offset', 
                [':limit' => $limit, ':offset' => $offset]
            );
        } else {
            return $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE postType = "userPost" AND fileType != "folder" AND isPrivate = "0" AND tags != "null" 
                ORDER BY nbLike DESC'
            );
        }
    }

    //////////////////////
    // advanced search //
    ////////////////////
    private function getPostsForAdvancedSearch(string $clauseWhere = '', string $clauseOrderBy = '', array $arrValue = [])
    {
        $q = $this->sql(
            'SELECT ' . static::$TABLE_CHAMPS . ' 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE postType = "userPost" AND isPrivate = "0" AND tags != "null" ' . $clauseWhere . 
            $clauseOrderBy . ' 
            LIMIT :limit OFFSET :offset', 
            $arrValue
        );
        $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
        $q->closeCursor();
        return $result;
    }

    private function getPostsCountForAdvancedsearch(string $clauseWhere, string $clauseOrderBy, array $arrValue)
    {
        $q = $this->sql(
            'SELECT COUNT(*) 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE postType = "userPost" AND isPrivate = "0" AND tags != "null" ' . $clauseWhere . 
            $clauseOrderBy, 
            $arrValue
        );
        $result = $q->fetch();
        $q->closeCursor();
        return intval($result[0]);
    }

    private function getValuesForAdvancedSearch(array $POST, int $limit, int $offset)
    {
        //return different clause depending of the POST value (only for advanced search)
        if (!empty($POST['pageToGo'])) {
            $offset = (intval($POST['pageToGo'])-1)*$limit;
        }
        $clauseWhere = $this->getClauseWhereForAdvancedSearch($POST);
        $clauseOrderBy = $this->getClauseOrderByForAdvancedSearch($POST);
        $arrValue = [':offset' => $offset, ':limit' => $limit];
        $arrValueForCount = [];
        if ($POST['schoolFilter'] !== 'noSchoolFilter') {
            $arrValue[':school'] = $POST['schoolFilter'];
            $arrValueForCount[':school'] = $POST['schoolFilter'];
        }
        return ['clauseWhere' => $clauseWhere, 'clauseOrderBy' => $clauseOrderBy, 'arrValue' => $arrValue, 'arrValueForCount' => $arrValueForCount];
    }

    private function getClauseWhereForAdvancedSearch(array $POST)
    {
        $clauseWhere = '';
        if ($POST['schoolFilter'] !== 'noSchoolFilter') {
            $clauseWhere .= 'AND school = :school ';
        }

        if (!empty($POST['listTags'])) {
            $listTags = explode(',', $POST['listTags']);
            $clauseWhereTag = '';
            for ($i=1; $i<count($listTags); $i++) {
                if ($i !== 1) {
                    $clauseWhereTag .= 'AND ';
                }
                $regex = "'(," . $listTags[$i] . ",.+)|(," . $listTags[$i] . "$)'";
                $clauseWhereTag .= 'tags REGEXP ' . $regex . ' ';
            }
            $clauseWhere .= 'AND (' . $clauseWhereTag . ') ';
        }
        return $clauseWhere;
    }

    private function getClauseOrderByForAdvancedSearch(array $POST)
    {
        $clauseOrderBy = '';
        if ($POST['sortBy'] === 'lastPosted') {
            $clauseOrderBy = 'ORDER BY id DESC ';
        } elseif ($POST['sortBy'] === 'firstPosted') {
            $clauseOrderBy = 'ORDER BY id ';
        } elseif ($POST['sortBy'] === 'mostLiked') {
            $clauseOrderBy = 'ORDER BY nbLike DESC ';
        }
        return $clauseOrderBy;
    }

    //////////////////
    // upload Post //
    ////////////////
    private function checkValueDependingOnFileType(array $arrPOST, string $type, User $user, $folder, TagsManager $TagsManager)
    {
        // check values depending on fileType of uploaded file
        switch ($arrPOST['fileTypeValue']) {
            case 'image':
                if (empty($_FILES['uploadFile'])) {
                    return false;
                }
                break;
            case 'video':
                if (empty($arrPOST['videoLink'])) {
                    return false;
                }
                break;
            case 'compressed':
                if (empty($_FILES['uploadFile']) || empty($arrPOST['title'])) {
                    return false;
                }
                break;
            case 'folder':
                if (empty($arrPOST['title'])) {
                    return false;
                }
                break;
            default :
                return false;
        }
        // check list tag
        if (!empty($arrPOST['listTags'])) {
            $listTags = explode(',', $arrPOST['listTags']);
            array_shift($listTags);
            if (!$TagsManager->tagsAreValide($listTags)) {
                return false;
            }
        }
        // check title length
        if (!empty($arrPOST['title']) && strlen($arrPOST['title']) > 30) {
            return false;
        }
        // more check depending of the upload type
        switch ($type) {
            case 'referenced' :
                return $this->canUploadReferencedPost($user, $arrPOST, $folder);
            break;
            case 'unreferenced' :
                return $this->canUploadUnreferencedPost($user, $arrPOST, $folder);
            break;
            case 'private' :
                return $this->canUploadPrivatePost($user, $arrPOST, $folder);
            break;
            case 'onSchoolProfile' :
                return $this->canUploadOnSchoolProfile($user, $arrPOST, $folder);
            break;
            default :
                return false;
        }
    }

    private function canUploadReferencedPost(User $user, array $arrPOST, $folder = null)
    {
        $arrPOST['isPrivate'] = false;
        $arrPOST['postType'] = 'userPost';
        if ($user->getIsActive() && $_SESSION['grade'] !== ADMIN && $_SESSION['grade'] !== MODERATOR && $_SESSION['school'] !== NO_SCHOOL 
        && ((!empty($arrPOST['listTags']) && $arrPOST['fileTypeValue'] !== 'folder') || (empty($arrPOST['listTags']) && $arrPOST['fileTypeValue'] === 'folder'))) {
            if (!empty($folder) && $folder->getPostType() === "userPost") {
                // referenced post on folder
                $arrPOST['folder'] = intval($arrPOST['folder']);
                return $arrPOST;
            } else {
                // referenced post
                $arrPOST['folder'] = null;
                return $arrPOST;
            }
        } else {
            return false;
        }
    }

    private function canUploadUnreferencedPost(User $user, array $arrPOST, $folder = null)
    {
        $arrPOST['isPrivate'] = false;
        $arrPOST['listTags'] = null;
        $arrPOST['postType'] = 'userPost';
        if (!$user->getIsActive() || $user->getSchool() === NO_SCHOOL || $_SESSION['grade'] === ADMIN || $_SESSION['grade'] === MODERATOR) {
            if (!empty($folder) && $folder->getPostType() === "userPost") {
                // unreferenced post on folder
                $arrPOST['folder'] = intval($arrPOST['folder']);
            } else {
                // unreferenced post
                $arrPOST['folder'] = null;
            }
            return $arrPOST;
        } else {
            return false;
        }
    }

    private function canUploadPrivatePost(User $user, array $arrPOST, $folder = null)
    {
        if ($user->getIsActive()) {
            $arrPOST['isPrivate'] = true;
            $arrPOST['listTags'] = null;
            $arrPOST['postType'] = 'schoolPost';
            if (defined('FRONTEND') && FRONTEND === true) {
                // frontside, user post on school folder
                if (!empty($folder) && $folder->getPostType() === "schoolPost") {
                    $arrPOST['folder'] = intval($arrPOST['folder']);
                    return $arrPOST;
                } else {
                    return false;
                }
            } else {
                // backside, private school post
                if (!empty($folder) && $folder->getPostType() === "schoolPost") {
                    $arrPOST['folder'] = intval($arrPOST['folder']);
                } else {
                    $arrPOST['folder'] = null;
                }
                return $arrPOST;
            }
        } else {
            return false;
        }
    }

    private function canUploadOnSchoolProfile(User $user, array $arrPOST, $folder = null)
    {
        if ($user->getIsActive() && ($user->getIsAdmin() || $user->getIsModerator())) {
            $arrPOST['isPrivate'] = false;
            $arrPOST['listTags'] = null;
            $arrPOST['postType'] = 'schoolPost';
            if (!empty($folder) && $folder->getPostType() === "schoolPost") {
                $arrPOST['folder'] = intval($arrPOST['folder']);
            } else {
                $arrPOST['folder'] = null;
            }
            return $arrPOST;
        } else {
            return false;
        }
    }

    private function uploadImagePost(array $POST)
    {
        $arrAcceptedExtention = array("jpeg", "jpg", "png", "gif");
        require 'view/upload.php';
        if (!empty($final_path)) {
            $this->set(
                new Post(
                    ['idAuthor' => $_SESSION['id'], 
                    'school' => $_SESSION['school'], 
                    'title' => $POST['title'], 
                    'filePath' => $final_path, 
                    'description' => $POST['tinyMCEtextarea'], 
                    'isPrivate' => $POST['isPrivate'], 
                    'authorizedGroups' => $POST['listAuthorizedGroups'], 
                    'postType' => $POST['postType'], 
                    'fileType' => $POST['fileTypeValue'], 
                    'onFolder' => $POST['folder'], 
                    'tags' => $POST['listTags']]
                )
            );
            return true;
        } else {
            return false;
        }
    }

    private function uploadVideoPost(array $POST)
    {
        $filePath = null;
        if ($_FILES['uploadFile']['error'] === 0) {
            $arrAcceptedExtention = array("jpeg", "jpg", "png", "gif");
            require 'view/upload.php';
            if (!empty($final_path)) {
                $filePath = $final_path;
            } else {
                return false;
            }
        }
        $this->set(
            new Post(
                ['idAuthor' => $_SESSION['id'], 
                'school' => $_SESSION['school'], 
                'title' => $POST['title'], 
                'filePath' => $filePath, 
                'urlVideo' => $POST['videoLink'], 
                'description' => $POST['tinyMCEtextarea'], 
                'isPrivate' => $POST['isPrivate'], 
                'authorizedGroups' => $POST['listAuthorizedGroups'], 
                'postType' => $POST['postType'], 
                'fileType' => $POST['fileTypeValue'], 
                'onFolder' => $POST['folder'], 
                'tags' => $POST['listTags']]
            )
        );
        return true;
    }

    private function uploadOtherPost(array $POST, string $isSchoolPost)
    {
        if ($isSchoolPost) {
            $arrAcceptedExtention = array("zip", "rar");
            require 'view/upload.php';
            if (!empty($final_path)) {
                $this->set(
                    new Post(
                        ['idAuthor' => $_SESSION['id'], 
                        'school' => $_SESSION['school'], 
                        'title' => $POST['title'], 
                        'filePath' => $final_path, 
                        'description' => $POST['tinyMCEtextarea'], 
                        'isPrivate' => $POST['isPrivate'], 
                        'authorizedGroups' => $POST['listAuthorizedGroups'], 
                        'postType' => $POST['postType'], 
                        'fileType' => $POST['fileTypeValue'], 
                        'onFolder' => $POST['folder']]
                    )
                );
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function uploadFolder(array $POST)
    {
        $filePath = null;
        if ($_FILES['uploadFile']['error'] === 0) {
            $arrAcceptedExtention = array("jpeg", "jpg", "png", "gif");
            require 'view/upload.php';
            if (!empty($final_path)) {
                $filePath = $final_path;
            } else {
                return false;
            }
        }
        $this->set(
            new Post(
                ['idAuthor' => $_SESSION['id'], 
                'school' => $_SESSION['school'], 
                'title' => $POST['title'], 
                'filePath' => $filePath,  
                'description' => $POST['tinyMCEtextarea'], 
                'isPrivate' => $POST['isPrivate'], 
                'authorizedGroups' => $POST['listAuthorizedGroups'], 
                'postType' => $POST['postType'], 
                'fileType' => $POST['fileTypeValue'], 
                'onFolder' => $POST['folder']]
            )
        );
        return true;
    }

    private function userCanSeePrivatePost(Post $post, User $user = null)
    {
        // private post can be consulted by webmaster / admin / moderator / author / user on listAuthorizedGroups 
        // (or all user in same school if listAuthorizedGroups is empty)
        if ($user === null) {
            //user who visiting the website
            $UserManager = new UserManager();
            if (empty($_SESSION)) {
                return false;
            } elseif (!$user = $UserManager->getOneById(intval($_SESSION['id']))) {
                return false;
            }
        }
        if (($post->getSchool() === $user->getSchool() && ($user->getIsActive() || $user->getIsModerator() || $user->getIsAdmin())) 
        || $user->getSchool() === ALL_SCHOOL) {
            //user is in the school where the post get publish or user is webmaster
            if ($user->getIsModerator() || $user->getIsAdmin() || $user->getId() === $post->getIdAuthor()) {
                //user is admin ,moderator, or author of this post
                return true;
            } elseif ($post->getAuthorizedGroups() !== 'none' && (empty($post->getListAuthorizedGroups()) || in_array($user->getSchoolGroup(), $post->getListAuthorizedGroups()))) {
                //all school group are allowed or user is in a group which is allowed
                return true;
            } elseif($post->getAuthorizedGroups() === 'none' && $post->getOnFolder() !== null) {
                $folder = $this->getOneById($post->getOnFolder());
                if ($folder && $this->userCanSeePrivatePost($folder, $user)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function getPostsByRandomTagsForAside(Post $post, TagsManager $TagsManager, int $nbPosts = 6, int $nbTags = 3)
    {
        //this function is only use on 'getAsidePosts' function
        //return array with 3 most popular tags of the post, with 6 random posts for each
        if (!empty($post) && !empty($TagsManager)) {
            $tagsPostsForAside = [];
            $mostPopularTags = $TagsManager->getMostPopularTags($nbTags, 0, $post->getListTags());
            foreach ($mostPopularTags as $tag) {
                $posts = $this->getPostsByTag($tag['name'], 100);
                $tagsPostsForAside[$tag['name']] = $this->getElemOnArray($posts, $post->getId());
            }
            return $tagsPostsForAside;
        } else {
            return [];
        }
    }
}
