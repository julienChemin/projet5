<?php
namespace Chemin\ArtSchools\Model;
class CvBlock
{
    private $id, 
        $idAuthor, 
        $idSection, 
        $content, 
        $blockOrder, 
        $blockSize, 
        $blockBackgroundColor, 
        $blockOpacity, 
        $blockBorderWidth, 
        $blockBorderColor, 
        $blockBorderRadius, 
        $blockClasses, 
        $blockStyle;

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
        if ($this->getBlockSize() !== null) {
            $this->setBlockClasses($this->getBlockSize());
        }

        if ($this->getBlockOrder() !== null) {
            $this->setBlockStyle($this->getBlockOrder());
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

    public function getIdSection()
    {
        return intval($this->idSection);
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getBlockOrder()
    {
        return intval($this->blockOrder);
    }

    public function getBlockSize()
    {
        return $this->blockSize;
    }

    public function getBlockBackgroundColor()
    {
        return $this->blockBackgroundColor;
    }

    public function getBlockOpacity()
    {
        return $this->blockOpacity;
    }

    public function getBlockBorderWidth()
    {
        return $this->blockBorderWidth;
    }

    public function getBlockBorderColor()
    {
        return $this->blockBorderColor;
    }

    public function getBlockBorderRadius()
    {
        return $this->blockBorderRadius;
    }

    public function getBlockClasses()
    {
        return $this->blockClasses;
    }

    public function getBlockStyle()
    {
        return $this->blockStyle;
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

    public function setIdSection(int $idSection)
    {
        if ($idSection > 0) {
            $this->idSection = $idSection;
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

    public function setBlockOrder(int $blockOrder)
    {
        if ($blockOrder > 0) {
            $this->blockOrder = $blockOrder;
        }
        return $this;
    }

    public function setBlockSize(int $blockSize)
    {
        if ($blockSize > 0) {
            $this->blockSize = $blockSize;
        }
        return $this;
    }

    public function setBlockBackgroundColor(string $blockBackgroundColor)
    {
        if (strlen($blockBackgroundColor) > 0) {
            $this->blockBackgroundColor = $blockBackgroundColor;
        }
        return $this;
    }

    public function setBlockOpacity(float $blockOpacity)
    {
        $this->blockOpacity = $blockOpacity;
        return $this;
    }

    public function setBlockBorderWidth(int $blockBorderWidth)
    {
        $this->blockBorderWidth = $blockBorderWidth;
        return $this;
    }

    public function setBlockBorderColor(string $blockBorderColor)
    {
        if (strlen($blockBorderColor) > 0) {
            $this->blockBorderColor = $blockBorderColor;
        }
        return $this;
    }

    public function setBlockBorderRadius(int $blockBorderRadius)
    {
        $this->blockBorderRadius = $blockBorderRadius;
        return $this;
    }

    public function setBlockClasses(string $blockSize)
    {
        $this->blockClasses = 'block' . ucfirst($blockSize);
        return $this;
    }

    public function setBlockStyle(int $blockOrder = 0)
    {
        if ($blockOrder > 0) {
            $blockStyle = 'order: ' . $blockOrder . ';';

            if ($this->getBlockBackgroundColor() && $this->getBlockOpacity()) {
                $blockStyle .= 'background-color:rgba(' . $this->getBlockBackgroundColor() . ', ' . $this->getBlockOpacity() . ');';
            } else {
                $blockStyle .= 'background-color:rgba(0, 0, 0, ' . $this->getBlockOpacity() . ');';
            }

            if ($this->getBlockBorderWidth()) {
                $blockStyle .= 'border-width: ' . $this->getBlockBorderWidth() . 'px;';
            }

            if ($this->getBlockBorderColor()) {
                $blockStyle .= 'border-color: rgb(' . $this->getBlockBorderColor() . ');';
            }

            if ($this->getBlockBorderRadius() && $this->getBlockBorderRadius() > 0) {
                $blockStyle .= 'border-radius: ' . $this->getBlockBorderRadius() . 'px;';
            }

            $this->blockStyle = $blockStyle;
        }
        return $this;
    }
}
