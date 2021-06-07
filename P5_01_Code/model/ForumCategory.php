<?php
namespace Chemin\ArtSchools\Model;
class ForumCategory
{
    private $id,
        $idSchool,
        $name,
        $description,
        $authorizedGroupsToSee,
        $authorizedGroupsToPost,
        $listAuthorizedGroupsToSee,
        $listAuthorizedGroupsToPost,
        $categoryOrder;

    public function __construct(array $data = null)
    {
        if (!empty($data)) {
            $this->hydrate($data);
        }
        $this->init();
    }

    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function init()
    {
        if ($this->getAuthorizedGroupsToSee() !== null) {
            $this->setListAuthorizedGroupsToSee($this->getAuthorizedGroupsToSee());
        }

        if ($this->getAuthorizedGroupsToPost() !== null) {
            $this->setListAuthorizedGroupsToPost($this->getAuthorizedGroupsToPost());
        }
    }

    //GETTERS
    public function getId()
    {
        return intval($this->id);
    }

    public function getIdSchool()
    {
        return intval($this->idSchool);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getAuthorizedGroupsToSee()
    {
        return $this->authorizedGroupsToSee;
    }

    public function getAuthorizedGroupsToPost()
    {
        return $this->authorizedGroupsToPost;
    }

    public function getListAuthorizedGroupsToSee()
    {
        return $this->listAuthorizedGroupsToSee;
    }

    public function getListAuthorizedGroupsToPost()
    {
        return $this->listAuthorizedGroupsToPost;
    }

    public function getCategoryOrder()
    {
        return $this->categoryOrder;
    }

    //SETTERS
    public function setId(int $id)
    {
        if ($id > 0) {
            $this->id = $id;
        }
        return $this;
    }

    public function setIdSchool(int $idSchool)
    {
        if ($idSchool > 0) {
            $this->idSchool = $idSchool;
        }
        return $this;
    }

    public function setName($name)
    {
        if (strlen($name) > 0) {
            $this->name = $name;
        }
        return $this;
    }

    public function setDescription($description)
    {
        if (strlen($description) > 0) {
            $this->description = $description;
        }
        return $this;
    }

    public function setAuthorizedGroupsToSee($authorizedGroups)
    {
        if (strlen($authorizedGroups) > 0) {
            $this->authorizedGroups = $authorizedGroups;
        }
        return $this;
    }

    public function setAuthorizedGroupsToPost($authorizedGroups)
    {
        if (strlen($authorizedGroups) > 0) {
            $this->authorizedGroups = $authorizedGroups;
        }
        return $this;
    }

    public function setListAuthorizedGroupsToSee($list)
    {
        if ($list !== null) {
            if ($list === 'none') {
                $this->listAuthorizedGroupsToSee = 'none';
            } else {
                $listAuthorizedGroups = explode(',', $list);
                $this->listAuthorizedGroupsToSee = array_slice($listAuthorizedGroups, 1);
            }
        }
        return $this;
    }

    public function setListAuthorizedGroupsToPost($list)
    {
        if ($list !== null) {
            if ($list === 'none') {
                $this->listAuthorizedGroupsToPost = 'none';
            } else {
                $listAuthorizedGroups = explode(',', $list);
                $this->listAuthorizedGroupsToPost = array_slice($listAuthorizedGroups, 1);
            }
        }
        return $this;
    }

    public function setCategoryOrder(int $categoryOrder)
    {
        if ($categoryOrder > 0) {
            $this->categoryOrder = $categoryOrder;
        }
        return $this;
    }
}
