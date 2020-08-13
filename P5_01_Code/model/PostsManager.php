<?php
namespace Chemin\ArtSchool\Model;
use Chemin\ArtSchool\Model\LikeManager;

class PostsManager extends LikeManager
{
    public static $OBJECT_TYPE = 'Chemin\ArtSchool\Model\Post';
    public static $TABLE_NAME = 'as_post';
    public static $TABLE_COMMENTS = 'as_comment';
    public static $TABLE_PK = 'id';
    public static $TABLE_CHAMPS ='id, idAuthor, school, title, filePath, urlVideo, description, DATE_FORMAT(datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication, isPrivate, authorizedGroups, postType, fileType, onFolder, tags, nbLike';
    public static $TABLE_CHAMPS_WITH_COMMENTS ='a.id, a.idAuthor, a.school, a.title, a.filePath, a.urlVideo, a.description, DATE_FORMAT(a.datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication, a.isPrivate, a.authorizedGroups, a.postType, a.fileType, a.onFolder, a.tags, a.nbLike, c.id AS idComment, c.idPost AS commentIdPost, c.idAuthor AS commentIdAuthor, c.NameAuthor AS commentNameAuthor, c.profilePictureAuthor AS commentProfilePictureAuthor, c.content AS commentContent, DATE_FORMAT(c.datePublication, "%d/%m/%Y à %H:%i.%s") AS commentDatePublication';

    public function getOneById(int $id)
    {
        if ($id > 0) {
            $q = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS_WITH_COMMENTS . ' 
                FROM ' . static::$TABLE_NAME . ' AS a 
                LEFT JOIN ' . static::$TABLE_COMMENTS . ' AS c 
                ON a.id = c.idPost 
                WHERE a.id = :id 
                ORDER BY c.datePublication', 
                [':id' => $id]
            );
            $result = $q->fetch();
            $post = new Post($result);
            $arrComments = [];
            if ($result['idComment'] !== null) {
                do {
                    $comment = new Comment();
                    $comment->setId($result['idComment'])->setIdPost($result['commentIdPost'])->setIdAuthor($result['commentIdAuthor'])->setNameAuthor($result['commentNameAuthor'])->setProfilePictureAuthor($result['commentProfilePictureAuthor'])->setContent($result['commentContent'])->setDatePublication($result['commentDatePublication']);
                    array_unshift($arrComments, $comment);
                } while ($result = $q->fetch());    
            }
            $post->setComments($arrComments);
            $q->closeCursor();
            return $post;
        }
    }

    public function getLastPosted(int $limit = null, int $offset = 0, string $schoolName = null)
    {
        !empty($schoolName) ? $clauseWhere = 'AND school = "' . $schoolName . '"' : $clauseWhere = '';
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
        }
    }

    public function getPostsBySchool(string $school, bool $withFolder = false, int $offset = 0, int $limit = null)
    {
        //get users posts affiliated to $school
        if (strlen($school) > 0) {
            if ($withFolder) {
                if (!empty($limit)) {
                    $q = $this->sql(
                        'SELECT ' . static::$TABLE_CHAMPS . ' 
                        FROM ' . static::$TABLE_NAME . ' 
                        WHERE school = :school AND postType = "userPost" AND onFolder IS NULL AND isPrivate = "0" 
                        ORDER BY id DESC 
                        LIMIT :limit OFFSET :offset', 
                        [':school' => $school, ':offset' => $offset, ':limit' => $limit]
                    );
                } else {
                    $q = $this->sql(
                        'SELECT ' . static::$TABLE_CHAMPS . ' 
                        FROM ' . static::$TABLE_NAME . ' 
                        WHERE school = :school AND postType = "userPost" AND onFolder IS NULL AND isPrivate = "0" 
                        ORDER BY id DESC', 
                        [':school' => $school]
                    );
                }
            } else {
                if (!empty($limit)) {
                    $q = $this->sql(
                        'SELECT ' . static::$TABLE_CHAMPS . ' 
                        FROM ' . static::$TABLE_NAME . ' 
                        WHERE school = :school AND postType = "userPost" AND fileType != "folder" AND isPrivate = "0" 
                        ORDER BY id DESC 
                        LIMIT :limit OFFSET :offset', 
                        [':school' => $school, ':offset' => $offset, ':limit' => $limit]
                    );
                } else {
                    $q = $this->sql(
                        'SELECT ' . static::$TABLE_CHAMPS . ' 
                        FROM ' . static::$TABLE_NAME . ' 
                        WHERE school = :school AND postType = "userPost" AND fileType != "folder" AND isPrivate = "0" 
                        ORDER BY id DESC', 
                        [':school' => $school]
                    );
                }
            }
            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
            
            $q->closeCursor();
            return $result;
        }
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

    public function getSchoolPosts(string $school)
    {
        if (strlen($school) > 0) {
            $q = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE school = :school AND (postType = "schoolPost" OR (postType = "userPost" AND onFolder != "null" AND tags IS NULL)) 
                ORDER BY id DESC', 
                [':school' => $school]
            );
            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);

            $q->closeCursor();
            return $result;
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
        }
    }

    public function getMostLikedPosts(int $limit = null, int $offset = 0, string $school = null)
    {
        if (!empty($school)) {
            //by school
            if (!empty($limit)) {
                $q = $this->sql(
                    'SELECT ' . static::$TABLE_CHAMPS . ' 
                    FROM ' . static::$TABLE_NAME . ' 
                    WHERE school = :school AND postType = "userPost" AND fileType != "folder" AND isPrivate = "0" 
                    ORDER BY nbLike DESC 
                    LIMIT :limit OFFSET :offset', 
                    [':school' => $school, ':limit' => $limit, ':offset' => $offset]
                );
            } else {
                $q = $this->sql(
                    'SELECT ' . static::$TABLE_CHAMPS . ' 
                    FROM ' . static::$TABLE_NAME . ' 
                    WHERE school = :school AND postType = "userPost" AND fileType != "folder" AND isPrivate = "0" 
                    ORDER BY nbLike DESC', 
                    [':school' => $school]
                );
            }
        } else {
            //all school
            if (!empty($limit)) {
                $q = $this->sql(
                    'SELECT ' . static::$TABLE_CHAMPS . ' 
                    FROM ' . static::$TABLE_NAME . ' 
                    WHERE postType = "userPost" AND fileType != "folder" AND isPrivate = "0" 
                    ORDER BY nbLike DESC 
                    LIMIT :limit OFFSET :offset', 
                    [':limit' => $limit, ':offset' => $offset]
                );
            } else {
                $q = $this->sql(
                    'SELECT ' . static::$TABLE_CHAMPS . ' 
                    FROM ' . static::$TABLE_NAME . ' 
                    WHERE postType = "userPost" AND fileType != "folder" AND isPrivate = "0" 
                    ORDER BY nbLike DESC'
                );
            }
        }
        $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
        $q->closeCursor();
        return $result;
    }

    public function advancedSearch(array $POST, int $limit = 12, int $offset = 0)
    {
        $clauseWhere = '';
        $clauseOrderBy = '';
        if (!empty($POST['pageToGo'])) {
            $offset = (intval($POST['pageToGo'])-1)*$limit;
        }
        $arrValue = [':offset' => $offset, ':limit' => $limit];
        $arrValueForCount = [];
        if ($POST['schoolFilter'] !== 'noSchoolFilter') {
            $clauseWhere .= 'AND school = :school ';
            $arrValue[':school'] = $POST['schoolFilter'];
            $arrValueForCount[':school'] = $POST['schoolFilter'];
        }
        if ($POST['sortBy'] === 'lastPosted') {
            $clauseOrderBy = 'ORDER BY id DESC ';
        } elseif ($POST['sortBy'] === 'firstPosted') {
            $clauseOrderBy = 'ORDER BY id ';
        } elseif ($POST['sortBy'] === 'mostLiked') {
            $clauseOrderBy = 'ORDER BY nbLike DESC ';
        }
        if (!empty($POST['listTags'])) {
            $listTags = explode(',', $POST['listTags']);
            $clauseWhereTag = '';
            for ($i=1; $i<count($listTags); $i++) {
                if ($i !== 1) {
                    $clauseWhereTag .= 'OR ';
                }
                $regex = "'(," . $listTags[$i] . ",.+)|(," . $listTags[$i] . "$)'";
                $clauseWhereTag .= 'tags REGEXP ' . $regex . ' ';
            }
            $clauseWhere .= 'AND (' . $clauseWhereTag . ') ';
        }

        $q = $this->sql(
            'SELECT ' . static::$TABLE_CHAMPS . ' 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE postType = "userPost" AND isPrivate = "0" AND tags != "null" ' . $clauseWhere . 
            $clauseOrderBy . ' 
            LIMIT :limit OFFSET :offset', 
            $arrValue
        );
        $result['posts'] = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
        $q->closeCursor();
        $q = $this->sql(
            'SELECT COUNT(*) 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE postType = "userPost" AND isPrivate = "0" AND tags != "null" ' . $clauseWhere . 
            $clauseOrderBy, 
            $arrValueForCount
        );
        $result['count'] = $q->fetch();
        $result['count'] = intval($result['count'][0]);
        $q->closeCursor();
        return $result;
    }

    public function set(Post $Post)
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

    public function update(Post $Post)
    {
        $this->sql(
            'UPDATE ' . static::$TABLE_NAME . ' 
            SET title = :title, filePath = :filePath, urlVideo = :urlVideo, description = :description, isPrivate = :isPrivate, 
            authorizedGroups = :authorizedGroups, postType = :postType, fileType = :fileType, onFolder = :onFolder, tags = :tags 
            WHERE id = :id', 
            [':title' => $Post->getTitle(), ':filePath' => $Post->getFilePath(), ':urlVideo' => $Post->getUrlVideo(), ':description' => $Post->getDescription(), 
            ':isPrivate' => intval($Post->getIsPrivate()), ':authorizedGroups' => $Post->getAuthorizedGroups(), ':postType' => $Post->getPostType(), 
            ':fileType' => $Post->getFileType(), ':onFolder' => $Post->getOnFolder(), ':tags' => $Post->getTags(), ':id' => $Post->getId()]
        );
        return $this;
    }

    public function deletePost(int $postId)
    {
        if ($postId > 0) {
            $post = $this->getOneById($postId);
            if (!empty($post->getFilePath())) {
                unlink($post->getFilePath());
            }
            $this->delete($post->getId());
        }
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
    }

    public function toArray($elem)
    {
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

    public function objectPostToArray(Post $post)
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
        'listAuthorizedGroups' => $post->getListAuthorizedGroups(), 
        'postType' => $post->getPostType(), 
        'fileType' => $post->getFileType(), 
        'onFolder' => $post->getOnFolder(),
        'listTags' => $post->getListTags()];
        return $arr;
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

    public function displayResultSearchByKeyWord(array $result, string $elem)
    {
        if (count($result) > 0 && strlen($elem) > 0) {
            switch ($elem) {
            case 'school' :
                echo '<div>';
                echo '<h2>Établissements</h2>';
                echo '</div>';

                echo '<div class="blockResult blockResultSchool fullWidth">';
                for($i=0; $i<count($result); $i++) {
                    if ($result[$i]->getName() !== NO_SCHOOL) {
                        echo '<div><a href="index.php?action=schoolProfile&school=' . $result[$i]->getName() . '">';
                        echo '<figure class="figureProfilePicture fullWidth">';
                        echo '<div><img src="' . $result[$i]->getLogo() . '"></div>';
                        echo '<figcaption>';
                        echo '<p>' . $result[$i]->getName() . '</p>';
                        echo '</figcaption>';
                        echo '</figure>';
                        echo '</a></div>';
                    }
                }
                echo '</div>';
                break;
            case 'user' :
                echo '<div>';
                echo '<h2>Utilisateurs</h2>';
                echo '</div>';

                echo '<div class="blockResult blockResultUser fullWidth">';
                for($i=0; $i<count($result); $i++) {
                    if ($result[$i]->getSchool() !== ALL_SCHOOL) {
                        echo '<div><a href="index.php?action=userProfile&userId=' . $result[$i]->getId() . '">';
                        echo '<figure class="figureProfilePicture fullWidth">';
                        echo '<div><img src="' . $result[$i]->getProfilePicture() . '"></div>';
                        echo '<figcaption>';
                        echo '<p>' . $result[$i]->getName() . '</p>';
                        echo '</figcaption>';
                        echo '</figure>';
                        echo '</a></div>';
                    }
                }
                echo '</div>';
                break;
            case 'post' :
                   echo '<div>';
                    echo '<h2>Publications</h2>';
                   echo '</div>';

                   echo '<div class="blockResult blockResultPost fullWidth">';
                for($i=0; $i<count($result); $i++) {
                    echo '<div><a href="index.php?action=post&id=' . $result[$i]->getId() . '">';
                    if (empty($result[$i]->getFilePath())) {
                        switch ($result[$i]->getFileType()) {
                        case 'image' :
                            $result[$i]->setFilePath('public/images/fileImage.png');
                            break;
                        case 'video' :
                            $result[$i]->setFilePath('public/images/defaultVideoThumbnail.png');
                            break;
                        }
                    } elseif ($result[$i]->getFileType() === 'video') {
                        echo '<img class="iconeVideo" src="public/images/defaultVideoThumbnail.png">';
                    }
                      echo '<figure class="figureProfilePicture fullWidth">';
                          echo '<figcaption>';
                              echo '<p>' . $result[$i]->getTitle() . '</p>';
                          echo '</figcaption>';
                          echo '<div><img src="' . $result[$i]->getFilePath() . '"></div>';
                      echo '</figure>';
                    echo '</a></div>';
                }
                echo '</div>';
                break;
            case 'tag' :
                echo '<div>';
                echo '<h2>Tags</h2>';
                echo '</div>';

                echo '<div class="blockResult blockResultTag fullWidth">';
                for($i=0; $i<count($result); $i++) {
                     echo '<div><a href="index.php?action=search&sortBy=tag&tag=' . $result[$i]['name'] . '">';
                      echo '<span class="tag">' . $result[$i]['name'] . '</span>';
                      echo '<span>- (' . $result[$i]['tagCount'] . ')</span>';
                     echo '</a></div>';
                }
                echo '</div>';
                break;
            }
        }
    }

    public function canUploadPost(array $arrPOST, TagsManager $TagsManager)
    {
        if (!empty($arrPOST['fileTypeValue']) && $this->checkForScriptInsertion([$arrPOST])) {
            //set folder, postType and privacy
            $arrPOST['uploadType'] === "private" ? $isPrivate = true : $isPrivate = false;
            if (!empty($arrPOST['folder'])) {
                $folder = $this->getOneById(intval($arrPOST['folder']));
                if ($folder->getPostType() === "schoolPost") {
                    //user post on school folder, authorizedGroups must be "none" so the publisher is the only one (with admin and moderator) who can see the post
                    if ($arrPOST['postType'] === 'schoolPost') {
                        $arrPOST['listTags'] = null;
                    }
                    //if folder is schoolPost, post is set as schoolPost
                    $arrPOST['postType'] = 'schoolPost';
                } else {
                    $arrPOST['postType'] = 'userPost';
                }
                if ($arrPOST['uploadType'] === 'public' && $folder->getIsPrivate()) {
                    //post public on private folder -> post become private
                    $arrPOST['uploadType'] = 'private';
                    $arrPOST['folder'] = intval($arrPOST['folder']);
                } elseif ($arrPOST['uploadType'] === 'private' && !$folder->getIsPrivate()) {
                    //post private on public folder -> don't post on folder
                    $arrPOST['folder'] = null;
                } else {
                    $arrPOST['folder'] = intval($arrPOST['folder']);
                }
            } else {
                $arrPOST['folder'] = null;
            }
            //check list tag
            if (!empty($arrPOST['listTags'])) {
                $listTags = explode(',', $arrPOST['listTags']);
                array_shift($listTags);
                if (!$TagsManager->tagsAreValide($listTags)) {
                    return false;
                }
            }
            //check title length
            if (!empty($arrPOST['title']) && strlen($arrPOST['title']) > 30) {
                return false;
            }
            //check privacy
            if ($arrPOST['uploadType'] === "private" && !empty($arrPOST['listTags'])) {
                return false;
            }
            //check folder
            if (!empty($arrPOST['folder']) && !$this->canPostOnFolder($this->getOneById(intval($arrPOST['folder'])))) {
                return false;
            }
            //check $_post
            switch ($arrPOST['fileTypeValue']) {
                case 'image':
                    if (empty($_FILES['uploadFile']) || (empty($arrPOST['listTags']) && $arrPOST['uploadType'] === 'public' && $arrPOST['postType'] === 'userPost'
                        && $arrPOST['isStudent'] === 'true')
                    ) {
                        return false;
                    }
                    break;
                case 'video':
                    if (empty($arrPOST['videoLink']) || (empty($arrPOST['listTags']) && $arrPOST['uploadType'] === 'public' && $arrPOST['postType'] === 'userPost'
                        && $arrPOST['isStudent'] === 'true')
                    ) {
                        return false;
                    }
                    break;
                case 'compressed':
                    if ($arrPOST['uploadType'] === 'public' || empty($_FILES['uploadFile']) || empty($arrPOST['title'])) {
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
        } else {
            return false;
        }
        return $arrPOST;
    }

    public function uploadPost(array $arrPOST, $schoolPost = false, $authorizedGroups = null)
    {
        $arrPOST['uploadType'] === "private" ? $isPrivate = true : $isPrivate = false;
        !$isPrivate ? $authorizedGroups = null : $authorizedGroups = $authorizedGroups;
        switch ($arrPOST['fileTypeValue']) {
        case 'image':
            $arrAcceptedExtention = array("jpeg", "jpg", "png", "gif");
            require 'view/upload.php';
            if (!empty($final_path)) {
                $this->set(
                    new Post(
                        ['idAuthor' => $_SESSION['id'], 
                        'school' => $_SESSION['school'], 
                        'title' => $arrPOST['title'], 
                        'filePath' => $final_path, 
                        'description' => $arrPOST['tinyMCEtextarea'], 
                        'isPrivate' => $isPrivate, 
                        'authorizedGroups' => $authorizedGroups, 
                        'postType' => $arrPOST['postType'], 
                        'fileType' => $arrPOST['fileTypeValue'], 
                        'onFolder' => $arrPOST['folder'], 
                        'tags' => $arrPOST['listTags']]
                    )
                );
                 return true;
            } else {
                return false;
            }
            break;
        case 'video':
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
                    'title' => $arrPOST['title'], 
                    'filePath' => $filePath, 
                    'urlVideo' => $arrPOST['videoLink'], 
                    'description' => $arrPOST['tinyMCEtextarea'], 
                    'isPrivate' => $isPrivate, 
                    'authorizedGroups' => $authorizedGroups, 
                    'postType' => $arrPOST['postType'], 
                    'fileType' => $arrPOST['fileTypeValue'], 
                    'onFolder' => $arrPOST['folder'], 
                    'tags' => $arrPOST['listTags']]
                )
            );
            return true;
            break;
        case 'compressed':
            if ($schoolPost) {
                $arrAcceptedExtention = array("zip", "rar");
                require 'view/upload.php';
                if (!empty($final_path)) {
                    $this->set(
                        new Post(
                            ['idAuthor' => $_SESSION['id'], 
                            'school' => $_SESSION['school'], 
                            'title' => $arrPOST['title'], 
                            'filePath' => $final_path, 
                            'description' => $arrPOST['tinyMCEtextarea'], 
                            'isPrivate' => $isPrivate, 
                            'authorizedGroups' => $authorizedGroups, 
                            'postType' => $arrPOST['postType'], 
                            'fileType' => $arrPOST['fileTypeValue'], 
                            'onFolder' => $arrPOST['folder']]
                        )
                    );
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
            break;
        case 'folder':
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
                    'title' => $arrPOST['title'], 
                    'filePath' => $filePath,  
                    'description' => $arrPOST['tinyMCEtextarea'], 
                    'isPrivate' => $isPrivate, 
                    'authorizedGroups' => $authorizedGroups, 
                    'postType' => $arrPOST['postType'], 
                    'fileType' => $arrPOST['fileTypeValue'], 
                    'onFolder' => $arrPOST['folder']]
                )
            );
            return true;
            break;
        }
    }

    public function canPostOnFolder(Post $post)
    {
        if (isset($_SESSION) && $post->getFileType() === 'folder') {
            if ($post->getIdAuthor() === intval($_SESSION['id'])) {
                //folder belong to user
                return true;
            } elseif ($post->getSchool() === $_SESSION['school'] && ($_SESSION['grade'] === MODERATOR || $_SESSION['grade'] === ADMIN)) {
                //admin and moderator can post on school folder
                return true;
            } elseif ($post->getSchool() === $_SESSION['school'] && $post->getIsPrivate() && ($post->getListAuthorizedGroups() === null || in_array($_SESSION['group'], $post->getListAuthorizedGroups()))) {
                //user on this group can post in this folder
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function sortForProfile($posts)
    {
        $arrSortedPosts = ['folder' => [], 'private' => [], 'public' => []];
        foreach ($posts as $post) {
            //sort post by onFolder, public and private
            $post = $this->toArray($post);
            if ($post['onFolder'] !== null) {
                $idFolder = $post['onFolder'];
                if ($post['isPrivate'] === '1') {
                    if (!empty($_SESSION) && ($post['school'] === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL)) {
                        if ($_SESSION['grade'] === MODERATOR || $_SESSION['grade'] === ADMIN || $_SESSION['id'] === $post['idAuthor'] || $post['listAuthorizedGroups'] === null || in_array($_SESSION['group'], $post['listAuthorizedGroups'])) {
                            if (!isset($arrSortedPosts['folder'][$idFolder])) {
                                   $arrSortedPosts['folder'][$idFolder] = [];
                            }
                            $arrSortedPosts['folder'][$idFolder][] = $post;
                        }
                    }
                } else {
                    if (!isset($arrSortedPosts['folder'][$idFolder])) {
                        $arrSortedPosts['folder'][$idFolder] = [];
                    }
                    $arrSortedPosts['folder'][$idFolder][] = $post;
                }
            } elseif ($post['isPrivate'] === '1') {
                if (!empty($_SESSION) && ($post['school'] === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL)) {
                    if ($_SESSION['grade'] === MODERATOR || $_SESSION['grade'] === ADMIN || $_SESSION['id'] === $post['idAuthor'] || $post['listAuthorizedGroups'] === null || in_array($_SESSION['group'], $post['listAuthorizedGroups'])) {
                        $arrSortedPosts['private'][] = $post;
                    }
                }
            } else {
                $arrSortedPosts['public'][] = $post;
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
            $asidePosts['withTag'] = $this->getTagsPostsForAside($post, $TagsManager);
        }
        return $asidePosts;
    }

    public function getTagsPostsForAside(Post $post, TagsManager $TagsManager, int $nbPosts = 6, int $nbTags = 3)
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

    public function getPostsForHome(SchoolManager $SchoolManager, TagsManager $TagsManager)
    {
        //return posts for home
        $homePosts = [];
        $homePosts['lastPosted'] = $this->getLastPosted(5);
        $homePosts['mostLiked'] = $this->getMostLikedPosts(5);
        //pick 4 most popular tag then pick 5 posts of them
        $mostPopularTags = $this->getElemOnArray($TagsManager->getMostPopularTags(5), -1, true, 4);
        $arrPostsByTags = [];
        foreach ($mostPopularTags as $tag) {
            $posts = $this->getPostsByTag($tag['name'], 100);
            $arrPostsByTags[$tag['name']] = $this->getElemOnArray($posts, -1, true, 5);
        }
        $homePosts['withTag'] = $arrPostsByTags;
        //pick 2 school random then pick 5 posts of them
        $noSchool = $SchoolManager->getSchoolByName(NO_SCHOOL);
        $allSchool = $SchoolManager->getSchoolByName(ALL_SCHOOL);
        $randomSchool = $this->getElemOnArray($allSchool, $noSchool->getId(), true, 2);
        $arrPostsBySchool = [];
        foreach ($randomSchool as $school) {
            $posts = $this->getPostsBySchool($school->getName(), false, 0, 100);
            $arrPostsBySchool[$school->getName()] = $this->getElemOnArray($posts, -1, true, 5);
        }
        $homePosts['bySchool'] = $arrPostsBySchool;
        return $homePosts;
    }

    public function getElemOnArray(array $elements, int $unwantedElem = -1, bool $random = true, int $qtt = 6)
    {
        //'unwantedElem' is the id of unwanted elem, this is a specific treatment for posts
        //can work for other elem if array $elements have one of this form : (array) $elements[(int) index][(int) 'id'] OR (array) $elements[(int) index][(int) object->getId()]
        //after doing this function, i discovered array.rand...whatever
        if (count($elements) > 0 && $qtt > 0) {
            $response = [];
            if ($random) {
                while (count($elements) > 0 && count($response) < $qtt) {
                    do {
                        $badNumber = false;
                        $randomNumber = rand(0, (count($elements) - 1));
                        if ($unwantedElem !== -1 && ((is_array($elements[$randomNumber]) && intval($elements[$randomNumber]['id']) === $unwantedElem) 
                            || (is_object($elements[$randomNumber]) && intval($elements[$randomNumber]->getId()) === $unwantedElem))
                        ) {
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
            } else {
                $i = 0;
                while (count($elements) > 0 && count($response) < $qtt) {
                    $i++;
                    if ($unwantedElem !== -1 && ((is_array($elements[$i]) && intval($elements[$i]['id']) === $unwantedElem) 
                        || (is_object($elements[$i]) && intval($elements[$i]->getId()) === $unwantedElem))
                    ) {
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
        } else {
            return [];
        }
    }
}
