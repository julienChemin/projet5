<?php
namespace Chemin\ArtSchool\Model;
use Chemin\ArtSchool\Model\Database;

class TagsManager extends Database
{
    public static $TABLE_NAME = 'as_tag';

    public function get(int $limit = null, int $offset = 0, string $orderBy = 'tagCount DESC')
    {
        if (!empty($limit)) {
            $q = $this->sql(
                'SELECT * 
                FROM ' . static::$TABLE_NAME . ' 
                ORDER BY ' . $orderBy . ' 
                LIMIT :limit OFFSET :offset', 
                [':limit' => $limit, ':offset' => $offset]
            );
        } else {
            //all tags
            $q = $this->sql(
                'SELECT * 
                FROM ' . static::$TABLE_NAME . ' 
                ORDER BY ' . $orderBy
            );
        }
        $result = $q->fetchAll();    
        $q->closeCursor();
        return $result;
    }

    public function getMany(array $tags)
    {
        if (count($tags) > 0) {
            $clauseWhere = '';
            $arrayValue = [];
            for ($i = 0; $i < count($tags); $i++) {
                $tagWithoutSpace = str_replace(' ', '', $tags[$i]) . $i;
                if ($i === 0) {
                    $clauseWhere = $clauseWhere . 'WHERE name = :' . $tagWithoutSpace;
                } else {
                    $clauseWhere = $clauseWhere . ' OR name = :' . $tagWithoutSpace;
                }
                $arrayValue[':' . $tagWithoutSpace] = $tags[$i];
            }
            $q = $this->sql(
                'SELECT * 
                FROM ' . static::$TABLE_NAME . ' 
                ' . $clauseWhere . ' 
                ORDER BY tagCount DESC', 
                $arrayValue
            );
            $result = $q->fetchAll();
            $q->closeCursor();
            return $result;
        }
    }

    public function getOneByName(string $name)
    {
        $q = $this->sql(
            'SELECT * 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE name = :name', 
            [':name' => $name]
        );
        $result = $q->fetch();    
        $q->closeCursor();
        return $result;
    }

    public function getMostPopularTags(int $limit = null, int $offset = 0, array $tags = null)
    {
        $mostPopularTags = [];
        if (!empty($tags) && count($tags) > 0) {
            //tags on this post
            $sortedTags = $this->getMany($tags);
            if (!empty($limit)) {
                count($sortedTags) > $limit ? $loop = $limit : $loop = count($sortedTags);
                for ($i = 0; $i < $loop; $i++) {
                    $mostPopularTags[] = $sortedTags[$i];
                }
            } else {
                $mostPopularTags = $sortedTags;
            }
        } else {
            //all tags
            if (!empty($limit)) {
                $mostPopularTags = $this->get($limit, $offset);
            } else {
                $mostPopularTags = $this->get();
            }
        }
        return $mostPopularTags;
    }

    public function set(string $name)
    {
        if (strlen($name) > 0) {
            $this->sql(
                'INSERT INTO ' . static::$TABLE_NAME . ' (name) 
                VALUES(:name)', 
                [':name' => $name]
            );
        }
        return $this;
    }

    public function setRelationPostTag(string $tagName, int $idPost)
    {
        if (strlen($tagName) > 0 && $idPost > 0) {
            $this->sql(
                'INSERT INTO as_tag_post (idPost, tagName) 
                VALUES(:idPost, :tagName)', 
                [':idPost' => $idPost, ':tagName' => $tagName]
            );
        }
        return $this;
    }

    public function exists(string $name)
    {
        if (strlen($name) > 0) {
            $q = $this->sql(
                'SELECT name 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE name = :name', 
                [':name' => $name]
            );
            if ($q->fetch()) {
                   $q->closeCursor();
                   return true;
            } else {
                $q->closeCursor();
                return false;
            }
        } else {
            return false;
        }
    }

    public function deleteUnusedTags()
    {
        $this->sql(
            'DELETE FROM ' . static::$TABLE_NAME . ' 
            WHERE tagCount <= :zero', 
            [':zero' => 0]
        );
        return $this;
    }

    public function checkForNewTag(string $listTags, int $idPost)
    {
        if (!empty($listTags) && $idPost > 0) {
            $arrTags = explode(',', $listTags);
            for ($i=1; $i<count($arrTags); $i++) {
                if (!$this->exists($arrTags[$i])) {
                    $this->set($arrTags[$i]);
                }
                $this->setRelationPostTag($arrTags[$i], $idPost);
            }
        }
    }

    public function searchForKeyWord($word)
    {
        $result = [];
        $regex = "'.*" . $word . ".*'";
        $q = $this->sql(
            'SELECT name, tagCount 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE name REGEXP ' . $regex
        );
        $result = $q->fetchAll();
        return $result;
    }

    public function tagsAreValide(array $tags)
    {
        $regex = '/^[a-z0-9]+[a-z0-9 ]*[a-z0-9]+$/i';
        foreach ($tags as $tag) {
            if (!preg_match($regex, $tag)) {
                return false;
            }
        }
        return true;
    }

    public function sortByAlphabeticalOrder(array $tags)
    {
        if (count($tags) > 0) {
            $sortedTags = [];
            foreach ($tags as $tag) {
                $sortedTags[strtoupper($tag['name'][0])][] = $tag;
            }
            return $sortedTags;
        } else {
            return [];
        }
    }
}
