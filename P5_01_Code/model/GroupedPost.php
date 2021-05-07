<?php
namespace Chemin\ArtSchools\Model;
class GroupedPost
{
    private $id,
        $idAuthor,
        $idSchool,
        $idGroup,
        $filePath,
        $urlVideo,
        $fileType;

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

    public function getIdAuthor()
    {
        return intval($this->idAuthor);
    }

    public function getIdSchool()
    {
        return intval($this->idSchool);
    }

    public function getIdGroup()
    {
        return intval($this->idGroup);
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getUrlVideo()
    {
        return $this->urlVideo;
    }

    public function getFileType()
    {
        return $this->fileType;
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

    public function setIdSchool(int $idSchool)
    {
        if ($idSchool > 0) {
            $this->idSchool = $idSchool;
        }
        return $this;
    }

    public function setIdGroup(int $idGroup)
    {
        if ($idGroup > 0) {
            $this->idGroup = $idGroup;
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

    public function setUrlVideo($urlVideo)
    {
        if (strlen($urlVideo) > 0) {
            $this->urlVideo = $urlVideo;
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
}
