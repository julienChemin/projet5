<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\ForumReplyManager;

class ForumTopicManager extends ForumReplyManager
{
    public static $TOPIC_OBJECT_TYPE = 'Chemin\ArtSchools\Model\ForumTopic';

    public static $TOPIC_TABLE_NAME = 'as_forum_topic';

    public static $TOPIC_TABLE_CHAMPS = 't.id, t.idSchool, t.idCategory, t.idAuthor, u.pseudo AS authorName, t.title, t.content, 
        DATE_FORMAT(t.datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication, t.authorizedGroupsToSee, t.authorizedGroupsToPost, t.isPinned, t.pinOrder';

    public function getTopic(int $idTopic = 0, bool $withReply = true)
    {
        if ($idTopic > 0) {
            if ($withReply) {
                return $this->getTopicWithReply($idTopic);
            } else {
                return $this->getTopicWithoutReply($idTopic);
            }
        } else {
            return false;
        }
    }

    public function getTopics(int $idCategory, $user, bool $pinned = false)
    {
        if ($idCategory > 0 && $user) {
            if ($pinned) {
                return $this->getPinnedTopics($idCategory, $user);
            } else {
                return $this->getNonePinnedTopics($idCategory, $user);
            }
        } else {
            return false;
        }
    }

    public function setTopic(array $POST, user $user, School $school, ForumCategory $category)
    {
        $authorizedToSee = $this->getAuthorizedGroupsFromFormForTopic($category, 'see', $POST['authorizedGroupsToSee'], $POST['listAuthorizedGroupsToSee']);
        $authorizedToPost = $this->getAuthorizedGroupsFromFormForTopic($category, 'post', $POST['authorizedGroupsToPost'], $POST['listAuthorizedGroupsToPost']);

        $this->sql(
            'INSERT INTO ' . static::$TOPIC_TABLE_NAME . ' (idSchool, idCategory, idAuthor, title, content, datePublication, authorizedGroupsToSee, authorizedGroupsToPost) 
            VALUES (:idSchool, :idCategory, :idAuthor, :title, :content, NOW(), :authorizedGroupsToSee, :authorizedGroupsToPost)', 
            [
                ':idSchool' => $school->getId(), ':idCategory' => $category->getId(), ':idAuthor' => $user->getId(), ':title' => trim($POST['title']), 
                ':content' => trim($POST['tinyMCEtextarea']), ':authorizedGroupsToSee' => $authorizedToSee, ':authorizedGroupsToPost' => $authorizedToPost
            ]
        );
    }

    public function deleteTopic(array $topicInfo)
    {
        // Check for img in topic content
        if (!empty($topicInfo['topic']->getContent())) {
            $filePaths = $this->extractFilePath($this->checkForImgEntries($topicInfo['topic']->getContent()));
            if (count($filePaths) > 0) {
                foreach($filePaths as $filePath) {
                    $this->deleteFile($filePath);
                }
            }
        }

        //  delete replies
        if (count($topicInfo['replies']) > 0) {
            foreach ($topicInfo['replies'] as $reply) {
                $this->deleteReply($reply);
            }
        }

        // delete topic
        $this->sql(
            'DELETE FROM ' . static::$TOPIC_TABLE_NAME . ' 
            WHERE ' . static::$TABLE_PK . ' = :id', 
            [':id' => $topicInfo['topic']->getId()]
        );
    }

    public function getAuthorizedGroupsFromFormForCategory(string $inputValue, string $listGroup)
    {
        //set list groups before creating a new category
        switch ($inputValue) {
            case 'all':
                return null;
            break;

            case 'none':
                return "none";
            break;

            default:
                if (!empty(trim($listGroup))) {
                    return $listGroup;
                } else {
                    return null;
                }
        }
    }

    public function getAuthorizedGroupsFromFormForTopic(ForumCategory $category, string $authorizationType, string $inputValue, string $listGroup)
    {
        //set list groups before creating a new topic
        switch ($inputValue) {
            case 'all':
                if ($authorizationType == 'see') {
                    return $category->getAuthorizedGroupsToSee();
                } else {
                    return $category->getAuthorizedGroupsToPost();
                }
            break;

            case 'none':
                return "none";
            break;

            default:
                if (!empty(trim($listGroup))) {
                    return $listGroup;
                } else {
                    if ($authorizationType == 'see') {
                        return $category->getAuthorizedGroupsToSee();
                    } else {
                        return $category->getAuthorizedGroupsToPost();
                    }
                }
        }
    }

    public function getAuthorizedGroupsForNewTopic(ForumCategory $category, string $type, $listSchoolGroup = null)
    {
        //get list groups that can be use to create a new topic
        if ($type === 'see') {
            $authorizedGroups = $category->getListAuthorizedGroupsToSee();
        } else {
            $authorizedGroups = $category->getListAuthorizedGroupsToPost();
        }
        
        switch ($authorizedGroups) {
            case null:
                return $listSchoolGroup;
            break;

            case 'none':
                return null;
            break;

            default:
                return $authorizedGroups;
        }
    }

    public function userCanCreateTopic(User $user, $authorizedGroupsToPost)
    {
        if ($user->getIsAdmin() || $user->getIsModerator()) {
            return true;
        } else if (!$authorizedGroupsToPost) {
            return true;
        } else if ($user->getSchoolGroup() !== null && strpos($authorizedGroupsToPost, $user->getSchoolGroup())) {
            return true;
        } else {
            return false;
        }
    }

    public function canAccessForumElem($user, string $authorizedGroups = null)
    {
        if (!$authorizedGroups) {
            return true;
        } else if ($authorizedGroups === 'none') {
            if ($user->getIsAdmin() || $user->getIsModerator()) {
                return true;
            } else {
                return false;
            }
        } else {
            if (strpos($authorizedGroups, $user->getSchoolGroup()) !== false) {
                return true;
            } else {
                return false;
            }
        }
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PRIVATE FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    protected function getClauseAuthorizedGroupsToSee($user)
    {
        if ($user->getIsAdmin() || $user->getIsModerator()) {
            return "";
        } else if ($user->getSchoolGroup() !== null) {
            $group = $user->getSchoolGroup();
            return " AND (authorizedGroupsToSee LIKE '%" . $group . "%' OR ISNULL(authorizedGroupsToSee))";
        } else {
            return " AND ISNULL(authorizedGroupsToSee)";
        }
    }

    private function getTopicWithoutReply(int $idTopic)
    {
        $q = $this->sql(
            'SELECT ' . static::$TOPIC_TABLE_CHAMPS . ' 
            FROM ' . static::$TOPIC_TABLE_NAME . ' AS t 
            LEFT JOIN as_user AS u 
            ON u.id = t.idAuthor 
            WHERE t.id = :id', 
            [':id' => $idTopic]
        );

        $result = $q->fetchObject(static::$TOPIC_OBJECT_TYPE);
        $q->closeCursor();

        return $result;
    }

    private function getTopicWithReply(int $idTopic)
    {
        $result = ['topic' => null, 'replies' => null];

        $result['topic'] = $this->getTopicWithoutReply($idTopic);
        $result['replies'] = $this->getReplies($idTopic);

        return $result;
    }

    private function getPinnedTopics(int $idCategory, $user)
    {
        $clauseGroup = $this->getClauseAuthorizedGroupsToSee($user);

        $q = $this->sql(
            'SELECT ' . static::$TOPIC_TABLE_CHAMPS . ' 
            FROM ' . static::$TOPIC_TABLE_NAME . ' AS t 
            LEFT JOIN as_user AS u 
            ON u.id = t.idAuthor 
            WHERE t.idCategory = :idCategory AND t.isPinned = true' . $clauseGroup . ' 
            ORDER BY pinOrder', 
            [':idCategory' => $idCategory]
        );

        $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$TOPIC_OBJECT_TYPE);
        $q->closeCursor();

        return $result;
    }

    private function getNonePinnedTopics(int $idCategory, $user)
    {
        $clauseGroup = $this->getClauseAuthorizedGroupsToSee($user);
        
        $q = $this->sql(
            'SELECT ' . static::$TOPIC_TABLE_CHAMPS . ' 
            FROM ' . static::$TOPIC_TABLE_NAME . ' AS t 
            LEFT JOIN as_user AS u 
            ON u.id = t.idAuthor 
            WHERE t.idCategory = :idCategory AND t.isPinned = false' . $clauseGroup . ' 
            ORDER BY id DESC', 
            [':idCategory' => $idCategory]
        );

        $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$TOPIC_OBJECT_TYPE);
        $q->closeCursor();

        return $result;
    }
}
