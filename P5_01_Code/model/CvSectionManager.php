<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\CvBlockManager;

class CvSectionManager extends CvBlockManager
{
    public static $SECTION_OBJECT_TYPE = 'Chemin\ArtSchools\Model\CvSection';
    public static $SECTION_TABLE_NAME = 'as_cv_section';
    public static $SECTION_TABLE_CHAMPS = 'id, idAuthor, name, sectionOrder, linkInNavbar, heightValue, verticalAlign, horizontalAlign, backgroundCover, backgroundFixed';

    public function getSection(int $idSection = 0, bool $withBlocks = true)
    {
        if ($idSection > 0) {
            if ($withBlocks) {
                return $this->getSectionWithBlocks($idSection);
            } else {
                return $this->getSectionWithoutBlocks($idSection);
            }
        } else {
            return false;
        }
    }

    public function getSections(int $idUser, bool $withBlocks = true)
    {
        if ($idUser > 0) {
            if ($withBlocks) {
                return $this->getSectionsWithBlocks($idUser);
            } else {
                return $this->getSectionsWithoutBlocks($idUser);
            }
        } else {
            return false;
        }
    }

    public function setSection(int $idUser, string $name = null, bool $linkInNavbar = false, string $heightValue = null, 
    string $vAlign = 'center', string $hAlign = 'between', string $backgroundCover = null, bool $backgroundFixed = false)
    {
        $sectionOrder = $this->getCountSections($idUser) + 1;
        if (!trim($name)) {
            $name = 'section ' . $sectionOrder;
        }

        $this->sql(
            'INSERT INTO ' . static::$SECTION_TABLE_NAME . ' 
            (idAuthor, name, sectionOrder, linkInNavbar, heightValue, verticalAlign, horizontalAlign, backgroundCover, backgroundFixed) 
            VALUES 
            (:idAuthor, :name, :sectionOrder, :linkInNavbar, :heightValue, :verticalAlign, :horizontalAlign, :backgroundCover, :backgroundFixed)', 
            [
                ':idAuthor' => $idUser, ':name' => $name, ':sectionOrder' => $sectionOrder, ':linkInNavbar' => intval($linkInNavbar), ':heightValue' => $heightValue, 
                ':verticalAlign' => $vAlign, ':horizontalAlign' => $hAlign, ':backgroundCover' => $backgroundCover, ':backgroundFixed' => intval($backgroundFixed)
            ]
        );

        return $this->getLastInsertId();
    }

    public function updateSection(int $idSection, string $elem = null, $value, bool $isBool = false)
    {
        if ($elem && str_contains(static::$SECTION_TABLE_CHAMPS, $elem)) {
            if ($isBool) {
                $this->sql(
                    'UPDATE ' . static::$SECTION_TABLE_NAME . ' 
                    SET ' . $elem . ' = :value 
                    WHERE id = :idSection', 
                    [':value' => intval($value), ':idSection' => $idSection]
                );
            } else {
                $this->sql(
                    'UPDATE ' . static::$SECTION_TABLE_NAME . ' 
                    SET ' . $elem . ' = :value 
                    WHERE id = :idSection', 
                    [':value' => $value, ':idSection' => $idSection]
                );
            }
    
            return true;
        } else {
            return false;
        }
    }

    public function changSectionOrder(string $direction, int $idUser, int $currentOrder)
    {
        switch ($direction) {
            case 'up':
                return $this->sectionOrderUp($idUser, $currentOrder);
            break;

            case 'down':
                return $this->sectionOrderDown($idUser, $currentOrder);
            break;

            default:
                return 'false';
        }
    }

    public function deleteSection(CvSection $section, bool $deleteEntireCv = false)
    {
        // delete blocks on this section
        $blocks = $this->getBlocks($section->getId());

        if (!empty($blocks)) {
            foreach ($blocks as $block) {
                $this->deleteBlock($block, true);
            }
        }

        // delete image cover
        if ($section->getBackgroundCover() !== null 
        && !str_contains($section->getBackgroundCover(), 'images/dl/') && !str_contains($section->getBackgroundCover(), 'images/default_cv_banner')) {
            $this->deleteFile($section->getBackgroundCover());
        }

        // delete section
        $this->sql(
            'DELETE FROM ' . static::$SECTION_TABLE_NAME . ' 
            WHERE ' . static::$TABLE_PK . ' = :id', 
            [':id' => $section->getId()]
        );

        if (!$deleteEntireCv) {
            // re-order section
            $sectionsAbove = $this->getSectionsAboveOrderX($section->getIdAuthor(), $section->getSectionOrder());

            if (!empty($sectionsAbove) && count($sectionsAbove) > 0) {
                foreach ($sectionsAbove as $section) {
                    $this->sectionOrderDecrement($section->getId(), $section->getSectionOrder());
                }
            }
        }
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PRIVATE FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/
    private function getSectionWithoutBlocks(int $idSection)
    {
        $q = $this->sql(
            'SELECT ' . static::$SECTION_TABLE_CHAMPS . ' 
            FROM ' . static::$SECTION_TABLE_NAME . ' 
            WHERE id = :idSection', 
            [':idSection' => $idSection]
        );

        $result = $q->fetchObject(static::$SECTION_OBJECT_TYPE);
        $q->closeCursor();

        return $result;
    }

    private function getSectionWithBlocks(int $idSection)
    {
        $result = ['section' => null, 'blocks' => null];

        $result['section'] = $this->getSectionWithoutBlocks($idSection);
        $result['blocks'] = $this->getBlocks($idSection);

        return $result;
    }

    private function getSectionsWithoutBlocks(int $idUser)
    {
        $q = $this->sql(
            'SELECT ' . static::$SECTION_TABLE_CHAMPS . ' 
            FROM ' . static::$SECTION_TABLE_NAME . ' 
            WHERE idAuthor = :idUser 
            ORDER BY sectionOrder', 
            [':idUser' => $idUser]
        );

        $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$SECTION_OBJECT_TYPE);
        $q->closeCursor();

        return $result;
    }

    private function getSectionsWithBlocks(int $idUser)
    {
        $result = ['sections' => null, 'blocks' => []];

        $result['sections'] = $this->getSectionsWithoutBlocks($idUser);

        if ($result['sections'] && count($result['sections']) > 0) {
            foreach ($result['sections'] as $section) {
                $result['blocks'][$section->getId()] = $this->getBlocks($section->getId());
            }
        }

        return $result;
    }

    private function getSectionsAboveOrderX(int $idUser, int $order)
    {
        if (!empty($idUser) && $idUser > 0 && !empty($order) && $order > 0) {
            $q = $this->sql(
                'SELECT ' . static::$SECTION_TABLE_CHAMPS . ' 
                FROM ' . static::$SECTION_TABLE_NAME . ' 
                WHERE idAuthor = :idUser AND sectionOrder > :sectionOrder
                ORDER BY sectionOrder', 
                [':idUser' => $idUser, ':sectionOrder' => $order]
            );
    
            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$SECTION_OBJECT_TYPE);
            $q->closeCursor();
    
            return $result;
        }
    }

    protected function getCountSections(int $idUser = null)
    {
        if ($idUser) {
            $q = $this->sql(
                'SELECT COUNT(*) 
                FROM ' . static::$SECTION_TABLE_NAME . ' 
                WHERE idAuthor = :idUser', 
                [':idUser' => $idUser]
            );

            $result = $q->fetch();
            $q->closeCursor();

            return intval($result[0]);
        } else {
            return 0;
        }
    }

    private function sectionOrderUp(int $idUser = null, int $order = null)
    {
        if ($idUser && $order && $order < $this->getCountSections($idUser)) {
            $this->sql(
                'UPDATE ' . static::$SECTION_TABLE_NAME . ' 
                SET sectionOrder = 0 
                WHERE idAuthor = :idUser AND sectionOrder = :sectionOrder', 
                [':idUser' => $idUser, ':sectionOrder' => $order]
            );

            $this->sql(
                'UPDATE ' . static::$SECTION_TABLE_NAME . ' 
                SET sectionOrder = :newSectionOrder 
                WHERE idAuthor = :idUser AND sectionOrder = :sectionOrder', 
                [':idUser' => $idUser, ':sectionOrder' => $order+1, ':newSectionOrder' => $order]
            );

            $this->sql(
                'UPDATE ' . static::$SECTION_TABLE_NAME . ' 
                SET sectionOrder = :newSectionOrder 
                WHERE idAuthor = :idUser AND sectionOrder = 0', 
                [':idUser' => $idUser, ':newSectionOrder' => $order+1]
            );

            return 'true';
        } else {
            return 'false';
        }
    }

    private function sectionOrderDown(int $idUser = null, int $order = null)
    {
        if ($idUser && $order && $order > 1) {
            $this->sql(
                'UPDATE ' . static::$SECTION_TABLE_NAME . ' 
                SET sectionOrder = 0 
                WHERE idAuthor = :idUser AND sectionOrder = :sectionOrder', 
                [':idUser' => $idUser, ':sectionOrder' => $order]
            );

            $this->sql(
                'UPDATE ' . static::$SECTION_TABLE_NAME . ' 
                SET sectionOrder = :newSectionOrder 
                WHERE idAuthor = :idUser AND sectionOrder = :sectionOrder', 
                [':idUser' => $idUser, ':sectionOrder' => $order-1, ':newSectionOrder' => $order]
            );

            $this->sql(
                'UPDATE ' . static::$SECTION_TABLE_NAME . ' 
                SET sectionOrder = :newSectionOrder 
                WHERE idAuthor = :idUser AND sectionOrder = 0', 
                [':idUser' => $idUser, ':newSectionOrder' => $order-1]
            );

            return 'true';
        } else {
            return 'false';
        }
    }

    private function sectionOrderDecrement(int $idSection = null, int $order = null)
    {
        if ($idSection && $idSection > 0 && $order && $order > 1) {
            $this->sql(
                'UPDATE ' . static::$SECTION_TABLE_NAME . ' 
                SET sectionOrder = :order 
                WHERE id = :idSection', 
                [':idSection' => $idSection, ':order' => $order-1]
            );
        }
    }
}
