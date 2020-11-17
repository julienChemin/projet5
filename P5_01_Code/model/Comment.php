<?php

namespace Chemin\ArtSchools\Model;

class Comment
{
    private $id,
        $idPost,
        $idAuthor,
        $firstNameAuthor,
        $lastNameAuthor,
        $profilePictureAuthor,
        $content,
        $datePublication;

    public function __construct(array $data = null)
    {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    public function hydrate(array $data)
    {
        foreach ($data as $key => $value){
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

    public function getIdPost()
    {
        return intval($this->idPost);
    }

    public function getIdAuthor()
    {
        return intval($this->idAuthor);
    }

    public function getFirstNameAuthor()
    {
        return $this->firstNameAuthor;
    }

    public function getLastNameAuthor()
    {
        return $this->lastNameAuthor;
    }

    public function getProfilePictureAuthor()
    {
        return $this->profilePictureAuthor;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getDatePublication()
    {
        return $this->datePublication;
    }

    //SETTERS
    public function setId(int $idComment)
    {
        if ($idComment > 0) {
            $this->id = $idComment;
            return $this;
        }
    }

    public function setIdPost(int $idPost)
    {
        if ($idPost > 0) {
            $this->idPost = $idPost;
            return $this;
        }
    }

    public function setIdAuthor(int $idAuthor)
    {
        if ($idAuthor > 0) {
            $this->idAuthor = $idAuthor;
            return $this;
        }
    }

    public function setFirstNameAuthor(string $firstNameAuthor = null)
    {
        if (strlen($firstNameAuthor) > 0) {
            $this->firstNameAuthor = $firstNameAuthor;
            return $this;
        }
    }

    public function setLastNameAuthor(string $lastNameAuthor = null)
    {
        if (strlen($lastNameAuthor) > 0) {
            $this->lastNameAuthor = $lastNameAuthor;
            return $this;
        }
    }

    public function setProfilePictureAuthor($profilePictureAuthor)
    {
        if ($profilePictureAuthor === null) {
            $this->profilePictureAuthor = ('public/images/question-mark.png');
        } elseif (is_string($profilePictureAuthor) && strlen($profilePictureAuthor) > 0) {
            $infos = explode(' ', $profilePictureAuthor);
            if (strpos($infos[0], 'http://') === 0 || strpos($infos[0], 'https://') === 0) {
                $url = explode('/', $infos[0]);
                $infos[0] = $url[count($url) - 4] . '/' . $url[count($url) - 3] . '/' . $url[count($url) - 2] . '/' . $url[count($url) - 1];
            }
            $this->profilePictureAuthor = ($infos[0]);
        }
        return $this;
    }

    public function setContent(string $content)
    {
        if (strlen($content) > 0) {
            $this->content = $content;
            return $this;
        }
    }

    public function setDatePublication($date)
    {
        if (!empty($date)) {
            $this->datePublication = $date;
            return $this;
        }
    }
}
