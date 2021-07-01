<?php
namespace Chemin\ArtSchools\Model;
class Cv
{
    private $id, 
        $idUser, 
        $isOnline, 
        $displayNavbar,  
        $shortLink;

    public function __construct(array $data = null)
    {
        if (!empty($data)) {
            $this->hydrate($data);
        }
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

    //GETTERS
    public function getId()
    {
        return intval($this->id);
    }

    public function getIdUser()
    {
        return intval($this->idUser);
    }

    public function getIsOnline()
    {
        return boolval($this->isOnline);
    }

    public function getDisplayNavbar()
    {
        return boolval($this->displayNavbar);
    }

    public function getShortLink()
    {
        return $this->shortLink;
    }

    //SETTERS
    public function setId(int $id)
    {
        if ($id > 0) {
            $this->id = $id;
        }
        return $this;
    }

    public function setIdUser(int $idUser)
    {
        if ($idUser > 0) {
            $this->idUser = $idUser;
        }
        return $this;
    }

    public function setIsOnline(bool $isOnline)
    {
        $this->isOnline = $isOnline;
        return $this;
    }

    public function setDisplayNavbar(bool $displayNavbar)
    {
        $this->displayNavbar = $displayNavbar;
        return $this;
    }

    public function setShortLink($shortLink)
    {
        if (strlen($shortLink) > 0) {
            $this->shortLink = $shortLink;
        }
        return $this;
    }
}
