<?php
namespace Chemin\ArtSchools\Model;
class ForumReply
{
    private $id,
        $idSchool,
        $idTopic,
        $idAuthor,
        $profilePictureInfoAuthor,
        $profilePictureAuthor,
        $pseudoAuthor,
        $firstNameAuthor,
        $lastNameAuthor,
        $content,
        $datePublication;

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
        $this->setProfilePictureAuthor($this->getProfilePictureInfoAuthor());
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

    public function getIdTopic()
    {
        return intval($this->idTopic);
    }

    public function getIdAuthor()
    {
        return intval($this->idAuthor);
    }

    public function getProfilePictureInfoAuthor()
    {
        return $this->profilePictureInfoAuthor;
    }

    public function getProfilePictureAuthor()
    {
        return $this->profilePictureAuthor;
    }

    public function getPseudoAuthor()
    {
        return $this->pseudoAuthor;
    }

    public function getFirstNameAuthor()
    {
        return $this->firstNameAuthor;
    }

    public function getLastNameAuthor()
    {
        return $this->lastNameAuthor;
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

    public function setIdTopic(int $idTopic)
    {
        if ($idTopic > 0) {
            $this->idTopic = $idTopic;
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

    public function setProfilePictureInfoAuthor($profilePictureInfoAuthor)
    {
        if (strlen($profilePictureInfoAuthor) > 0) {
            $this->profilePictureInfoAuthor = $profilePictureInfoAuthor;
        }
        return $this;
    }

    public function setProfilePictureAuthor($profilePictureAuthorInfo)
    {
        if ($profilePictureAuthorInfo === null || strlen($profilePictureAuthorInfo) <= 0) {
            $this->setProfilePictureAuthor('public/images/question-mark.png');
        } elseif (is_string($profilePictureAuthorInfo) && strlen($profilePictureAuthorInfo) > 0) {
            $infos = explode(' ', $profilePictureAuthorInfo);
            if ((strpos($infos[0], 'http://') === 0 || strpos($infos[0], 'https://') === 0) && strpos($infos[0], 'images/question-mark.png') === false) {
                $url = explode('/', $infos[0]);
                $infos[0] = $url[count($url) - 4] . '/' . $url[count($url) - 3] . '/' . $url[count($url) - 2] . '/' . $url[count($url) - 1];
            }
            $this->setProfilePictureAuthor($infos[0]);
        }
        return $this;
    }

    public function setPseudoAuthor($pseudoAuthor)
    {
        if (strlen($pseudoAuthor) > 0) {
            $this->pseudoAuthor = $pseudoAuthor;
        }
        return $this;
    }

    public function setFirstNameAuthor($firstNameAuthor)
    {
        if (strlen($firstNameAuthor) > 0) {
            $this->firstNameAuthor = $firstNameAuthor;
        }
        return $this;
    }

    public function setLastNameAuthor($lastNameAuthor)
    {
        if (strlen($lastNameAuthor) > 0) {
            $this->lastNameAuthor = $lastNameAuthor;
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
}
