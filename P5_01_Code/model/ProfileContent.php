<?php

namespace Chemin\ArtSchool\Model;

class ProfileContent
{
    private $id,
    $userId,
    $schoolId,
    $tab,
    $size,
    $contentOrder,
    $align,
    $content;

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
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getSchoolId()
    {
        return $this->schoolId;
    }

    public function getTab()
    {
        return $this->tab;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getContentOrder()
    {
        return $this->contentOrder;
    }

    public function getAlign()
    {
        return $this->align;
    }

    public function getContent()
    {
        return $this->content;
    }

    //SETTERS
    public function setId(int $id)
    {
        if ($id > 0) {
            $this->id = $id;
            return $this;
        }
    }

    public function setUserId(int $userId)
    {
        if ($userId > 0) {
            $this->userId = $userId;
            return $this;
        }
    }

    public function setSchoolId(int $schoolId)
    {
        if ($schoolId > 0) {
            $this->schoolId = $schoolId;
            return $this;
        }
    }

    public function setTab(string $tab)
    {
        if (strlen($tab) > 0) {
            $this->tab = $tab;
        }
        return $this;
    }

    public function setSize(string $size)
    {
        if (strlen($size) > 0) {
            $this->size = $size;
        }
        return $this;
    }

    public function setContentOrder(int $contentOrder)
    {
        if ($contentOrder > 0) {
            $this->contentOrder = $contentOrder;
            return $this;
        }
    }

    public function setAlign($align)
    {
        $this->align = $align;
        return $this;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
        
        return $this;
    }
}
