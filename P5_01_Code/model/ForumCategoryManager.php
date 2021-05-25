<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\ForumTopicManager;

class ForumCategoryManager extends ForumTopicManager
{
    public static $CATEGORY_OBJECT_TYPE = 'Chemin\ArtSchools\Model\ForumCategory';

    public static $CATEGORY_TABLE_NAME = 'as_forum_category';

    public static $CATEGORY_TABLE_CHAMPS = 'id, idSchool, name, description, authorizedGroupsToSee, authorizedGroupsToPost, categoryOrder';

    public function getCategory(int $idCategory = 0, $user = null, bool $withTopic = true)
    {
        if ($idCategory > 0 && $user) {
            if ($withTopic) {
                return $this->getCategoryWithTopic($idCategory, $user);
            } else {
                return $this->getCategoryWithoutTopic($idCategory);
            }
        } else {
            return false;
        }
    }

    public function getCategories(int $idSchool, $user, bool $withTopic = true, bool $pinnedTopics = true, bool $nonePinnedTopics = true)
    {
        if ($idSchool > 0) {
            if ($withTopic) {
                return $this->getCategoriesWithTopic($idSchool, $user, $pinnedTopics, $nonePinnedTopics);
            } else {
                return $this->getCategoriesWithoutTopic($idSchool, $user);
            }
        } else {
            return false;
        }
    }

    public function setCategory(int $idSchool, string $name, string $description = "", $authorizedGroupsToSee = "none", $authorizedGroupsToPost = "all")
    {
        $categoryOrder = $this->getCountCategories($idSchool) + 1;
        
        $this->sql(
            'INSERT INTO ' . static::$CATEGORY_TABLE_NAME . ' (idSchool, name, description, authorizedGroupsToSee, authorizedGroupsToPost, categoryOrder) 
            VALUES (:idSchool, :name, :description, :authorizedGroupsToSee, :authorizedGroupsToPost, :categoryOrder)', 
            [
                ':idSchool' => $idSchool, ':name' => $name, ':description' => trim($description), 
                ':authorizedGroupsToSee' => $authorizedGroupsToSee, ':authorizedGroupsToPost' => $authorizedGroupsToPost, ':categoryOrder' => $categoryOrder
            ]
        );
        return $this->getLastInsertId();
    }

    public function updateCategory(int $idCategory, string $title, string $content = "")
    {
        if (!empty($idCategory) && $idCategory > 0 && !empty($title)) {
            $this->sql(
                'UPDATE ' . static::$CATEGORY_TABLE_NAME . ' 
                SET name = :name, description = :description 
                WHERE id = :idCategory', 
                [':idCategory' => $idCategory, ':name' => $title, ':description' => $content]
            );
        }
    }

    public function changCategoryOrder(string $direction, int $idSchool, int $currentOrder)
    {
        switch ($direction) {
            case 'up':
                return $this->categoryOrderUp($idSchool, $currentOrder);
            break;

            case 'down':
                return $this->categoryOrderDown($idSchool, $currentOrder);
            break;

            default:
                return 'false';
        }
    }

    public function deleteCategory(array $categoryInfo)
    {
        // delete topics on this category
        if (count($categoryInfo['pinnedTopics']) > 0) {
            foreach ($categoryInfo['pinnedTopics'] as $topic) {
                $this->deleteTopic($this->getTopic($topic->getId()));
            }
        }

        if (count($categoryInfo['nonePinnedTopics']) > 0) {
            foreach ($categoryInfo['nonePinnedTopics'] as $topic) {
                $this->deleteTopic($this->getTopic($topic->getId()));
            }
        }

        // delete category
        $this->sql(
            'DELETE FROM ' . static::$CATEGORY_TABLE_NAME . ' 
            WHERE ' . static::$TABLE_PK . ' = :id', 
            [':id' => $categoryInfo['category']->getId()]
        );

        //re-order category
        $categoriesAbove = $this->getCategoryAboveOrderX($categoryInfo['category']->getIdSchool(), $categoryInfo['category']->getCategoryOrder());

        if (!empty($categoriesAbove) && count($categoriesAbove) > 0) {
            foreach ($categoriesAbove as $category) {
                $this->categoryOrderDecrement($category->getId(), $category->getCategoryOrder());
            }
        }
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PRIVATE FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    private function getCategoryWithoutTopic(int $idCategory)
    {
        $q = $this->sql(
            'SELECT ' . static::$CATEGORY_TABLE_CHAMPS . ' 
            FROM ' . static::$CATEGORY_TABLE_NAME . ' 
            WHERE id = :id', 
            [':id' => $idCategory]
        );

        $result = $q->fetchObject(static::$CATEGORY_OBJECT_TYPE);
        $q->closeCursor();

        return $result;
    }

    private function getCategoryWithTopic(int $idCategory, $user)
    {
        $result = ['category' => null, 'pinnedTopics' => null, 'nonePinnedTopics' => null];

        $result['category'] = $this->getCategoryWithoutTopic($idCategory);
        $result['pinnedTopics'] = $this->getTopics($idCategory, $user, true);
        $result['nonePinnedTopics'] = $this->getTopics($idCategory, $user, false);

        return $result;
    }

    private function getCategoriesWithoutTopic(int $idSchool, $user)
    {
        $clauseGroup = $this->getClauseAuthorizedGroupsToSee($user);

        $q = $this->sql(
            'SELECT ' . static::$CATEGORY_TABLE_CHAMPS . ' 
            FROM ' . static::$CATEGORY_TABLE_NAME . ' 
            WHERE idSchool = :idSchool' . $clauseGroup . ' 
            ORDER BY categoryOrder', 
            [':idSchool' => $idSchool]
        );

        $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$CATEGORY_OBJECT_TYPE);
        $q->closeCursor();

        return $result;
    }

    private function getCategoriesWithTopic(int $idSchool, $user, bool $pinnedTopics = true, bool $nonePinnedTopics = true)
    {
        $result = ['categories' => null, 'pinnedTopics' => [], 'nonePinnedTopics' => []];

        $result['categories'] = $this->getCategoriesWithoutTopic($idSchool, $user);

        if ($result['categories'] && count($result['categories']) > 0) {
            foreach ($result['categories'] as $category) {
                if ($category) {
                    if ($pinnedTopics) {
                        $result['pinnedTopics'][$category->getName()] = $this->getTopics($category->getId(), $user, true);
                    }

                    if ($nonePinnedTopics) {
                        $result['nonePinnedTopics'][$category->getName()] = $this->getTopics($category->getId(), $user, false);
                    }
                }
            }
        }

        return $result;
    }

    private function getCategoryAboveOrderX(int $idSchool, int $order)
    {
        if (!empty($idSchool) && $idSchool > 0 && !empty($order) && $order > 0) {
            $q = $this->sql(
                'SELECT ' . static::$CATEGORY_TABLE_CHAMPS . ' 
                FROM ' . static::$CATEGORY_TABLE_NAME . ' 
                WHERE idSchool = :idSchool AND categoryOrder > :categoryOrder
                ORDER BY categoryOrder', 
                [':idSchool' => $idSchool, ':categoryOrder' => $order]
            );
    
            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$CATEGORY_OBJECT_TYPE);
            $q->closeCursor();
    
            return $result;
        }
    }

    private function getCountCategories(int $idSchool = null)
    {
        if ($idSchool) {
            $q = $this->sql(
                'SELECT COUNT(*) 
                FROM ' . static::$CATEGORY_TABLE_NAME . ' 
                WHERE idSchool = :idSchool', 
                [':idSchool' => $idSchool]
            );
            $result = $q->fetch();
            $q->closeCursor();

            return intval($result[0]);
        } else {
            return 0;
        }
    }

    private function categoryOrderUp(int $idSchool = null, int $order = null)
    {
        if ($idSchool && $order && $order < $this->getCountCategories($idSchool)) {
            $this->sql(
                'UPDATE ' . static::$CATEGORY_TABLE_NAME . ' 
                SET categoryOrder = 0 
                WHERE idSchool = :idSchool AND categoryOrder = :categoryOrder', 
                [':idSchool' => $idSchool, ':categoryOrder' => $order]
            );

            $this->sql(
                'UPDATE ' . static::$CATEGORY_TABLE_NAME . ' 
                SET categoryOrder = :newCategoryOrder 
                WHERE idSchool = :idSchool AND categoryOrder = :categoryOrder', 
                [':idSchool' => $idSchool, ':categoryOrder' => $order+1, ':newCategoryOrder' => $order]
            );

            $this->sql(
                'UPDATE ' . static::$CATEGORY_TABLE_NAME . ' 
                SET categoryOrder = :newCategoryOrder 
                WHERE idSchool = :idSchool AND categoryOrder = 0', 
                [':idSchool' => $idSchool, ':newCategoryOrder' => $order+1]
            );

            return 'true';
        } else {
            return 'false';
        }
    }

    private function categoryOrderDown(int $idSchool = null, int $order = null)
    {
        if ($idSchool && $order && $order > 1) {
            $this->sql(
                'UPDATE ' . static::$CATEGORY_TABLE_NAME . ' 
                SET categoryOrder = 0 
                WHERE idSchool = :idSchool AND categoryOrder = :categoryOrder', 
                [':idSchool' => $idSchool, ':categoryOrder' => $order]
            );

            $this->sql(
                'UPDATE ' . static::$CATEGORY_TABLE_NAME . ' 
                SET categoryOrder = :newCategoryOrder 
                WHERE idSchool = :idSchool AND categoryOrder = :categoryOrder', 
                [':idSchool' => $idSchool, ':categoryOrder' => $order-1, ':newCategoryOrder' => $order]
            );

            $this->sql(
                'UPDATE ' . static::$CATEGORY_TABLE_NAME . ' 
                SET categoryOrder = :newCategoryOrder 
                WHERE idSchool = :idSchool AND categoryOrder = 0', 
                [':idSchool' => $idSchool, ':newCategoryOrder' => $order-1]
            );

            return 'true';
        } else {
            return 'false';
        }
    }

    private function categoryOrderDecrement(int $idCategory = null, int $order = null)
    {
        if ($idCategory && $idCategory > 0 && $order && $order > 0) {
            $this->sql(
                'UPDATE ' . static::$CATEGORY_TABLE_NAME . ' 
                SET categoryOrder = :order 
                WHERE id = :idCategory', 
                [':idCategory' => $idCategory, ':order' => $order-1]
            );
        }
    }
}
