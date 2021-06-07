<?php
namespace Chemin\ArtSchools\Model;
class ForumTopic
{
    private $id,
        $idSchool,
        $idCategory,
        $idAuthor,
        $authorName,
        $title,
        $content,
        $datePublication,
        $authorizedGroupsToSee,
        $authorizedGroupsToPost,
        $listAuthorizedGroupsToSee,
        $listAuthorizedGroupsToPost,
        $isPinned,
        $pinOrder,
        $isClose;

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

        if (!$this->getAuthorName()) {
            $this->setAuthorName('Compte supprimé');
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

    public function getIdCategory()
    {
        return intval($this->idCategory);
    }

    public function getIdAuthor()
    {
        return intval($this->idAuthor);
    }

    public function getAuthorName()
    {
        return $this->authorName;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }
    
    public function getDatePublication()
    {
        return $this->datePublication;
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

    public function getListAuthorizedGroups()
    {
        return $this->listAuthorizedGroups;
    }

    public function getIsPinned()
    {
        return $this->isPinned;
    }

    public function getPinOrder()
    {
        return $this->pinOrder;
    }

    public function getIsClose()
    {
        return $this->isClose;
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

    public function setIdCategory(int $idCategory)
    {
        if ($idCategory > 0) {
            $this->idCategory = $idCategory;
        }
        return $this;
    }

    public function setIdAuthor(int $idAuthor)
    {
        if ($idAuthor > 0) {
            $this->idAuthor = $idAuthor;
        }
        return $this;
    }

    public function setAuthorName($authorName)
    {
        if (strlen($authorName) > 0) {
            $this->authorName = $authorName;
        }
        return $this;
    }

    public function setTitle($title)
    {
        if (strlen($title) > 0) {
            $this->title = $title;
        }
        return $this;
    }

    public function setContent($content)
    {
        if (strlen($content) > 0) {
            $this->content = $content;
        }
        return $this;
    }

    public function setDatePublication($date)
    {
        if (!empty($date)) {
            $this->datePublication = $date;
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

    public function setIsPinned(bool $isPinned)
    {
        $this->isPinned = $isPinned;
        return $this;
    }

    public function setPinOrder($pinOrder)
    {
        if ($pinOrder && $pinOrder > 0) {
            $this->pinOrder = $pinOrder;
        }
        return $this;
    }

    public function setIsClose(bool $isClose)
    {
        $this->isClose = $isClose;
        return $this;
    }
}
