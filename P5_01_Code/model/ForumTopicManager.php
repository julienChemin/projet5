<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\ForumReplyManager;

class ForumTopicManager extends ForumReplyManager
{
    public static $TOPIC_OBJECT_TYPE = 'Chemin\ArtSchools\Model\ForumTopic';

    public static $TOPIC_TABLE_NAME = 'as_forum_topic';

    public static $TOPIC_TABLE_CHAMPS = 't.id, t.idSchool, t.idCategory, t.idAuthor, u.pseudo AS authorName, t.title, t.content, 
        DATE_FORMAT(t.datePublication, "%d/%m/%Y à %H:%i.%s") AS datePublication, t.authorizedGroupsToSee, t.authorizedGroupsToPost, t.isPinned, t.pinOrder, t.isClose';

    public function getTopic(int $idTopic = 0, bool $withReply = true, int $nbElemByPage = 10, int $offset = 0)
    {
        if ($idTopic > 0) {
            if ($withReply) {
                return $this->getTopicWithReply($idTopic, $nbElemByPage, $offset);
            } else {
                return $this->getTopicWithoutReply($idTopic);
            }
        } else {
            return false;
        }
    }

    public function getTopics(int $idCategory, $user, bool $pinned = false, $amountTopic = 0, $offset = 0)
    {
        if ($idCategory > 0 && $user) {
            if ($pinned) {
                return $this->getPinnedTopics($idCategory, $user);
            } else {
                return $this->getNonePinnedTopics($idCategory, $user, $amountTopic, $offset);
            }
        } else {
            return false;
        }
    }

    public function setTopic(array $POST, $content, user $user, School $school, ForumCategory $category)
    {
        if (empty($POST['authorizedGroupsToSee']) || empty($POST['authorizedGroupsToPost'])) {
            $authorizedToSee = $category->getAuthorizedGroupsToSee();
            $authorizedToPost = $category->getAuthorizedGroupsToPost();
        } else {
            $authorizedToSee = $this->getAuthorizedGroupsFromFormForTopic($category, 'see', $POST['authorizedGroupsToSee'], $POST['listAuthorizedGroupsToSee']);
            $authorizedToPost = $this->getAuthorizedGroupsFromFormForTopic($category, 'post', $POST['authorizedGroupsToPost'], $POST['listAuthorizedGroupsToPost']);
        }

        $this->sql(
            'INSERT INTO ' . static::$TOPIC_TABLE_NAME . ' (idSchool, idCategory, idAuthor, title, content, datePublication, authorizedGroupsToSee, authorizedGroupsToPost) 
            VALUES (:idSchool, :idCategory, :idAuthor, :title, :content, NOW(), :authorizedGroupsToSee, :authorizedGroupsToPost)', 
            [
                ':idSchool' => $school->getId(), ':idCategory' => $category->getId(), ':idAuthor' => $user->getId(), ':title' => trim($POST['title']), 
                ':content' => $content, ':authorizedGroupsToSee' => $authorizedToSee, ':authorizedGroupsToPost' => $authorizedToPost
            ]
        );
    }

    public function updateTopic(array $POST, $content, ForumCategory $category, $idTopic)
    {
        $authorizedToSee = $this->getAuthorizedGroupsFromFormForTopic($category, 'see', $POST['authorizedGroupsToSee'], $POST['listAuthorizedGroupsToSee']);
        $authorizedToPost = $this->getAuthorizedGroupsFromFormForTopic($category, 'post', $POST['authorizedGroupsToPost'], $POST['listAuthorizedGroupsToPost']);

        $this->sql(
            'UPDATE ' . static::$TOPIC_TABLE_NAME . ' 
            SET title = :title, content = :content, authorizedGroupsToSee = :authorizedGroupsToSee, authorizedGroupsToPost = :authorizedGroupsToPost 
            WHERE id = :idTopic', 
            [
                ':idTopic' => $idTopic, ':title' => trim($POST['title']), ':content' => $content, 
                ':authorizedGroupsToSee' => $authorizedToSee, ':authorizedGroupsToPost' => $authorizedToPost
            ]
        );
    }

    public function changTopicOrder(string $direction, int $idCategory, int $currentOrder)
    {
        switch ($direction) {
            case 'up':
                return $this->topicOrderUp($idCategory, $currentOrder);
            break;

            case 'down':
                return $this->topicOrderDown($idCategory, $currentOrder);
            break;

            default:
                return 'false';
        }
    }

    public function toggleIsPinned(ForumTopic $topic)
    {
        if ($this->isPinned($topic->getId())) {
            $this->unpinTopic($topic);
        } else {
            $this->pinTopic($topic);
        }

        return 'true';
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

        // re-order pinned topic
        if ($topicInfo['topic']->getIsPinned()) {
            $this->reorderPinnedTopic($topicInfo['topic']);
        }
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

    public function getAuthorizedGroupsForNewTopic(Object $forumElem, string $type, $listSchoolGroup = null)
    {
        //get list groups that can be use to create a new topic
        if ($type === 'see') {
            $authorizedGroups = $forumElem->getListAuthorizedGroupsToSee();
        } else {
            $authorizedGroups = $forumElem->getListAuthorizedGroupsToPost();
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

    public function canAccessForumElem(User $user, $authorizedGroups)
    {
        if ($user->getIsAdmin() || $user->getIsModerator()) {
            return true;
        } else if (!$authorizedGroups) {
            return true;
        } else if ($user->getSchoolGroup() !== null && strpos($authorizedGroups, $user->getSchoolGroup()) !== false) {
            return true;
        } else {
            return false;
        }
    }

    public function toggleIsClose(ForumTopic $topic)
    {
        $newValue = $topic->getIsClose() ? 0 : 1;

        $this->sql(
            'UPDATE ' . static::$TOPIC_TABLE_NAME . ' 
            SET isClose = :newValue 
            WHERE id = :idTopic', 
            [
                ':idTopic' => $topic->getId(), ':newValue' => $newValue
            ]
        );   
    }

    public function getCountNonePinnedTopic(int $idCategory = null)
    {
        if ($idCategory) {
            $q = $this->sql(
                'SELECT COUNT(*) 
                FROM ' . static::$TOPIC_TABLE_NAME . ' 
                WHERE idCategory = :idCategory AND isPinned = 0', 
                [':idCategory' => $idCategory]
            );
            $result = $q->fetch();
            $q->closeCursor();

            return intval($result[0]);
        } else {
            return 0;
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

    protected function getClauseLimit($amountElem = 0, $offset = 0)
    {
        if ($amountElem > 0) {
            return " LIMIT " . $amountElem . " OFFSET " . $offset;
        } else {
            return "";
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

    private function getTopicWithReply(int $idTopic, int $nbElemByPage = 10, int $offset = 0)
    {
        $result = ['topic' => null, 'replies' => null];

        $result['topic'] = $this->getTopicWithoutReply($idTopic);
        $result['replies'] = $this->getReplies($idTopic, $this->getClauseLimit($nbElemByPage, $offset));

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

    private function getNonePinnedTopics(int $idCategory, $user, $amountTopic = 0, $offset = 0)
    {
        $clauseGroup = $this->getClauseAuthorizedGroupsToSee($user);
        $clauseLimit = $this->getClauseLimit($amountTopic, $offset);
        
        $q = $this->sql(
            'SELECT ' . static::$TOPIC_TABLE_CHAMPS . ' 
            FROM ' . static::$TOPIC_TABLE_NAME . ' AS t 
            LEFT JOIN as_user AS u 
            ON u.id = t.idAuthor 
            WHERE t.idCategory = :idCategory AND t.isPinned = false' . $clauseGroup . ' 
            ORDER BY id DESC' 
            . $clauseLimit, 
            [':idCategory' => $idCategory]
        );

        $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$TOPIC_OBJECT_TYPE);
        $q->closeCursor();

        return $result;
    }

    private function getTopicAboveOrderX(int $idCategory, int $order)
    {
        if (!empty($idCategory) && $idCategory > 0 && !empty($order) && $order > 0) {
            $q = $this->sql(
                'SELECT id, pinOrder  
                FROM ' . static::$TOPIC_TABLE_NAME . ' 
                WHERE idCategory = :idCategory AND pinOrder > :pinOrder
                ORDER BY pinOrder', 
                [':idCategory' => $idCategory, ':pinOrder' => $order]
            );
    
            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$TOPIC_OBJECT_TYPE);
            $q->closeCursor();
    
            return $result;
        }
    }

    private function getCountPinnedTopic(int $idCategory = null)
    {
        if ($idCategory) {
            $q = $this->sql(
                'SELECT COUNT(*) 
                FROM ' . static::$TOPIC_TABLE_NAME . ' 
                WHERE idCategory = :idCategory AND isPinned = 1', 
                [':idCategory' => $idCategory]
            );
            
            $result = $q->fetch();
            $q->closeCursor();

            return intval($result[0]);
        } else {
            return 0;
        }
    }

    private function topicOrderUp(int $idCategory = null, int $order = null)
    {
        if ($idCategory && $order && $order < $this->getCountPinnedTopic($idCategory)) {
            $this->sql(
                'UPDATE ' . static::$TOPIC_TABLE_NAME . ' 
                SET pinOrder = 0 
                WHERE idCategory = :idCategory AND pinOrder = :pinOrder', 
                [':idCategory' => $idCategory, ':pinOrder' => $order]
            );

            $this->sql(
                'UPDATE ' . static::$TOPIC_TABLE_NAME . ' 
                SET pinOrder = :newCategoryOrder 
                WHERE idCategory = :idCategory AND pinOrder = :pinOrder', 
                [':idCategory' => $idCategory, ':pinOrder' => $order+1, ':newCategoryOrder' => $order]
            );

            $this->sql(
                'UPDATE ' . static::$TOPIC_TABLE_NAME . ' 
                SET pinOrder = :newCategoryOrder 
                WHERE idCategory = :idCategory AND pinOrder = 0', 
                [':idCategory' => $idCategory, ':newCategoryOrder' => $order+1]
            );

            return 'true';
        } else {
            return 'false';
        }
    }

    private function topicOrderDown(int $idCategory = null, int $order = null)
    {
        if ($idCategory && $order && $order > 1) {
            $this->sql(
                'UPDATE ' . static::$TOPIC_TABLE_NAME . ' 
                SET pinOrder = 0 
                WHERE idCategory = :idCategory AND pinOrder = :pinOrder', 
                [':idCategory' => $idCategory, ':pinOrder' => $order]
            );

            $this->sql(
                'UPDATE ' . static::$TOPIC_TABLE_NAME . ' 
                SET pinOrder = :newCategoryOrder 
                WHERE idCategory = :idCategory AND pinOrder = :pinOrder', 
                [':idCategory' => $idCategory, ':pinOrder' => $order-1, ':newCategoryOrder' => $order]
            );

            $this->sql(
                'UPDATE ' . static::$TOPIC_TABLE_NAME . ' 
                SET pinOrder = :newCategoryOrder 
                WHERE idCategory = :idCategory AND pinOrder = 0', 
                [':idCategory' => $idCategory, ':newCategoryOrder' => $order-1]
            );

            return 'true';
        } else {
            return 'false';
        }
    }

    private function topicOrderDecrement(int $idTopic = null, int $order = null)
    {
        if ($this->isPinned($idTopic) && $order && $order > 1) {
            $this->sql(
                'UPDATE ' . static::$TOPIC_TABLE_NAME . ' 
                SET pinOrder = :order 
                WHERE id = :idTopic', 
                [':idTopic' => $idTopic, ':order' => $order-1]
            );
        }
    }

    private function reorderPinnedTopic(ForumTopic $removedTopic)
    {
        $topicAbove = $this->getTopicAboveOrderX($removedTopic->getIdCategory(), $removedTopic->getPinOrder());

        if (!empty($topicAbove) && count($topicAbove) > 0) {
            foreach ($topicAbove as $topic) {
                $this->topicOrderDecrement($topic->getId(), $topic->getPinOrder());
            }
        }
    }

    private function isPinned(int $idTopic = null)
    {
        if ($idTopic && $idTopic > 0) {
            $q = $this->sql(
                'SELECT * 
                FROM ' . static::$TOPIC_TABLE_NAME . ' 
                WHERE id = :idTopic AND isPinned = 1', 
                [':idTopic' => $idTopic]
            );

            $result = $q->fetch();
            $q->closeCursor();

            if ($result) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function pinTopic(ForumTopic $topic)
    {
        $order = $this->getCountPinnedTopic($topic->getIdCategory()) + 1;

        $this->sql(
            'UPDATE ' . static::$TOPIC_TABLE_NAME . ' 
            SET pinOrder = :order, isPinned = 1 
            WHERE id = :idTopic', 
            [':idTopic' => $topic->getId(), ':order' => $order]
        );
    }

    public function unpinTopic(ForumTopic $topic)
    {
        $this->sql(
            'UPDATE ' . static::$TOPIC_TABLE_NAME . ' 
            SET pinOrder = null, isPinned = 0 
            WHERE id = :idTopic', 
            [':idTopic' => $topic->getId()]
        );

        $this->reorderPinnedTopic($topic);
    }
}
