<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\ForumTopicManager;

class ForumCategoryManager extends ForumTopicManager
{
    public static $CATEGORY_OBJECT_TYPE = 'Chemin\ArtSchools\Model\ForumCategory';

    public static $CATEGORY_TABLE_NAME = 'as_forum_category';

    public static $CATEGORY_TABLE_CHAMPS = 'id, idSchool, name, description, authorizedGroupsToSee, authorizedGroupsToPost, categoryOrder';

    public function getCategory(int $idCategory = 0, $user = null, bool $withTopic = true, $amountTopic = 0, $offset = 0)
    {
        if ($idCategory > 0 && $user) {
            if ($withTopic) {
                return $this->getCategoryWithTopic($idCategory, $user, $amountTopic, $offset);
            } else {
                return $this->getCategoryWithoutTopic($idCategory);
            }
        } else {
            return false;
        }
    }

    public function getCategories(int $idSchool, $user, bool $withTopic = true, bool $pinnedTopics = true, bool $nonePinnedTopics = true, $amountTopic = 0, $offset = 0)
    {
        if ($idSchool > 0) {
            if ($withTopic) {
                return $this->getCategoriesWithTopic($idSchool, $user, $pinnedTopics, $nonePinnedTopics, $amountTopic, $offset);
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

    public function updateCategory(int $idCategory, string $title, string $content = "", $authorizedGroupsToSee, $authorizedGroupsToPost)
    {
        if (!empty($idCategory) && $idCategory > 0 && !empty($title)) {
            $this->sql(
                'UPDATE ' . static::$CATEGORY_TABLE_NAME . ' 
                SET name = :name, description = :description, authorizedGroupsToSee = :authorizedGroupsToSee, authorizedGroupsToPost = :authorizedGroupsToPost 
                WHERE id = :idCategory', 
                [':idCategory' => $idCategory, ':name' => $title, ':description' => $content, 
                ':authorizedGroupsToSee' => $authorizedGroupsToSee, ':authorizedGroupsToPost' => $authorizedGroupsToPost]
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

    public function deleteCategory(array $categoryInfo, int $idSchool)
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

    public function setupForumForNewSchool(User $user, School $school)
    {
        $idNewCategory = $this->setCategory($school->getId(), "Bienvenue sur votre forum !", "", "none", "none");
        $category = $this->getCategory($idNewCategory, $user, false);

        $this->setTopic(
            [
                'title' => 'Cliquez moi pour commencer', 
                'authorizedGroupsToSee' => null, 
                'listAuthorizedGroupsToSee' => null, 
                'authorizedGroupsToPost' => null, 
                'listAuthorizedGroupsToPost' => null
            ], 
            '<p>Pour commencer avec le forum, il vous suffit d\'aller dans "gérer le forum" et de créer les catégories dont vous avez besoin. 
            Vous pourrez ensuite ouvrir des sujets de discution</p>
            <p>Si vous avez la moindre question ou si vous souhaitez plus d\'information sur les options du forum, je vous invite à regarder la <a href="index.php?action=faq#forum">F.A.Q</a> pour tout savoir sur comment gérer le forum.</p>
            ', 
            $user, $school, $category
        );
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

    private function getCategoryWithTopic(int $idCategory, $user, $amountTopic = 0, $offset = 0)
    {
        $result = ['category' => null, 'pinnedTopics' => null, 'nonePinnedTopics' => null];

        $result['category'] = $this->getCategoryWithoutTopic($idCategory);
        $result['pinnedTopics'] = $this->getTopics($idCategory, $user, true);
        $result['nonePinnedTopics'] = $this->getTopics($idCategory, $user, false, $amountTopic, $offset);

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

    private function getCategoriesWithTopic(int $idSchool, $user, bool $pinnedTopics = true, bool $nonePinnedTopics = true, $amountTopic = 0, $offset = 0)
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
                        $result['nonePinnedTopics'][$category->getName()] = $this->getTopics($category->getId(), $user, false, $amountTopic, $offset);
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
        if ($idCategory && $idCategory > 0 && $order && $order > 1) {
            $this->sql(
                'UPDATE ' . static::$CATEGORY_TABLE_NAME . ' 
                SET categoryOrder = :order 
                WHERE id = :idCategory', 
                [':idCategory' => $idCategory, ':order' => $order-1]
            );
        }
    }
}
