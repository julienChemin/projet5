<?php
namespace Chemin\ArtSchools\Model;
class CvSection
{
    private $id, 
        $idAuthor, 
        $name, 
        $sectionOrder, 
        $linkInNavbar, 
        $heightValue, 
        $verticalAlign, 
        $horizontalAlign, 
        $backgroundCover, 
        $backgroundFixed, 
        $sectionClasses, 
        $sectionStyle;

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
        if ($this->getVerticalAlign() !== null && $this->getHorizontalAlign() !== null) {
            $this->setSectionClasses($this->getVerticalAlign(), $this->getHorizontalAlign());
        }

        if ($this->getSectionOrder() !== null) {
            $this->setSectionStyle($this->getSectionOrder());
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

    public function getName()
    {
        return $this->name;
    }

    public function getSectionOrder()
    {
        return intval($this->sectionOrder);
    }

    public function getLinkInNavbar()
    {
        return boolval($this->linkInNavbar);
    }

    public function getHeightValue()
    {
        return $this->heightValue;
    }

    public function getVerticalAlign()
    {
        return $this->verticalAlign;
    }

    public function getHorizontalAlign()
    {
        return $this->horizontalAlign;
    }

    public function getbackgroundCover()
    {
        return $this->backgroundCover;
    }

    public function getbackgroundFixed()
    {
        return boolval($this->backgroundFixed);
    }

    public function getSectionClasses()
    {
        return $this->sectionClasses;
    }

    public function getSectionStyle()
    {
        return $this->sectionStyle;
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

    public function setName($name)
    {
        if (strlen($name) > 0) {
            $this->name = $name;
        }
        return $this;
    }

    public function setSectionOrder(int $sectionOrder)
    {
        if ($sectionOrder > 0) {
            $this->sectionOrder = $sectionOrder;
        }
        return $this;
    }

    public function setLinkInNavbar(bool $linkInNavbar)
    {
        $this->linkInNavbar = $linkInNavbar;
        return $this;
    }

    public function setHeightValue($heightValue)
    {
        if (strlen($heightValue) > 0) {
            $this->heightValue = $heightValue;
        }
        return $this;
    }

    public function setVerticalAlign($verticalAlign)
    {
        if (strlen($verticalAlign) > 0) {
            $this->verticalAlign = $verticalAlign;
        }
        return $this;
    }

    public function setHorizontalAlign($horizontalAlign)
    {
        if (strlen($horizontalAlign) > 0) {
            $this->horizontalAlign = $horizontalAlign;
        }
        return $this;
    }

    public function setBackgroundCover($backgroundCover)
    {
        if (strlen($backgroundCover) > 0) {
            $this->backgroundCover = $backgroundCover;
        }
        return $this;
    }

    public function setBackgroundFixed(bool $backgroundFixed)
    {
        $this->backgroundFixed = $backgroundFixed;
        return $this;
    }

    public function setSectionClasses(string $verticalAlign, string $horizontalAlign)
    {
        $this->sectionClasses = 'horizontalAlign' . ucfirst($horizontalAlign) . ' verticalAlign' . ucfirst($verticalAlign);
        return $this;
    }

    public function setSectionStyle(int $sectionOrder = 0)
    {
        if ($sectionOrder > 0) {
            $sectionStyle = 'order: ' . $sectionOrder . ';';

            if ($this->getBackgroundCover() !== null) {
                $sectionStyle .= 'background-image: url(' . $this->getBackgroundCover() . ');';
            }

            if ($this->getHeightValue() !== null) {
                $sectionStyle .= 'min-height: ' . $this->getHeightValue() . ';';
            }

            if ($this->getBackgroundFixed()) {
                $sectionStyle .= 'background-attachment: fixed;';
            }

            $this->sectionStyle = $sectionStyle;
        }
        return $this;
    }
}
