<?php
namespace Chemin\ArtSchools\Model;
class Post
{
    private $id,
        $idAuthor,
        $school,
        $title,
        $filePath,
        $urlVideo,
        $description,
        $datePublication,
        $isPrivate,
        $authorizedGroups,
        $listAuthorizedGroups,
        $postType,
        $fileType,
        $onFolder,
        $tags,
        $listTags,
        $nbLike,
        $comments;

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
        if ($this->getTags() !== null) {
            $this->setListTags($this->getTags());
        }
        if ($this->getAuthorizedGroups() !== null) {
            $this->setListAuthorizedGroups($this->getAuthorizedGroups());
        }
        if ($this->getNbLike() === null) {
            $this->setNbLike(0);
        }
    }

    //GETTERS
    public function getId()
    {
        return $this->id;
    }

    public function getIdAuthor()
    {
        return $this->idAuthor;
    }

    public function getSchool()
    {
        return $this->school;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getUrlVideo()
    {
        return $this->urlVideo;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getDatePublication()
    {
        return $this->datePublication;
    }

    public function getIsPrivate()
    {
        return $this->isPrivate;
    }

    public function getAuthorizedGroups()
    {
        return $this->authorizedGroups;
    }

    public function getListAuthorizedGroups()
    {
        return $this->listAuthorizedGroups;
    }

    public function getPostType()
    {
        return $this->postType;
    }

    public function getFileType()
    {
        return $this->fileType;
    }

    public function getOnFolder()
    {
        return $this->onFolder;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function getListTags()
    {
        return $this->listTags;
    }

    public function getNbLike()
    {
        return $this->nbLike;
    }

    public function getComments()
    {
        return $this->comments;
    }

    //SETTERS
    public function setId(int $id)
    {
        if ($id > 0) {
            $this->id = $id;
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

    public function setSchool(string $school)
    {
        if (strlen($school) > 0) {
            $this->school = $school;
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

    public function setFilePath($filePath)
    {
        if (strlen($filePath) > 0) {
            $this->filePath = $filePath;
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

    public function setDatePublication($date)
    {
        if (!empty($date)) {
            $this->datePublication = $date;
        }
        return $this;
    }

    public function setIsPrivate(bool $isPrivate)
    {
        $this->isPrivate = $isPrivate;
        return $this;
    }

    public function setAuthorizedGroups($authorizedGroups)
    {
        if (strlen($authorizedGroups) > 0) {
            $this->authorizedGroups = $authorizedGroups;
        }
        return $this;
    }

    public function setListAuthorizedGroups($list)
    {
        if ($list !== null) {
            $listAuthorizedGroups = explode(',', $list);
            $this->listAuthorizedGroups = array_slice($listAuthorizedGroups, 1);
        }
        return $this;
    }

    public function setPostType(string $postType)
    {
        if (strlen($postType) > 0) {
            $this->postType = $postType;
        }
        return $this;
    }

    public function setFileType(string $fileType)
    {
        if (strlen($fileType) > 0) {
            $this->fileType = $fileType;
        }
        return $this;
    }

    public function setUrlVideo($urlVideo)
    {
        if (strlen($urlVideo) > 0) {
            $this->urlVideo = $urlVideo;
        }
        return $this;
    }

    public function setOnFolder($onFolder)
    {
        $this->onFolder = $onFolder;
        return $this;
    }

    public function setTags($tags)
    {
        if (strlen($tags) > 0) {
            $this->tags = $tags;
        }
        return $this;
    }

    public function setListTags(string $list)
    {
        if ($list !== null) {
            $listTags = explode(',', $list);
            $this->listTags = array_slice($listTags, 1);
        }
        return $this;
    }

    public function setNbLike(int $nbLike)
    {
        if (strlen($nbLike) >= 0) {
            $this->nbLike = $nbLike;
        }
        return $this;
    }

    public function setComments(array $comments)
    {
        $this->comments = $comments;
        return $this;
    }
}
