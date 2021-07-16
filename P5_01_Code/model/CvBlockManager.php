<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\AbstractManager;

class CvBlockManager extends AbstractManager
{
    public static $BLOCK_OBJECT_TYPE = 'Chemin\ArtSchools\Model\CvBlock';
    public static $BLOCK_TABLE_NAME = 'as_cv_block';
    public static $BLOCK_TABLE_CHAMPS = 'id, idAuthor, idSection, content, blockOrder, blockSize, 
        blockBackgroundColor, blockOpacity, blockBorderWidth, blockBorderColor, blockBorderRadius';
    public static $TABLE_PK = 'id';

    public function getBlock(int $idBlock)
    {
        $q = $this->sql(
            'SELECT ' . static::$BLOCK_TABLE_CHAMPS . ' 
            FROM ' . static::$BLOCK_TABLE_NAME . ' 
            WHERE id = :idBlock', 
            [':idBlock' => $idBlock]
        );

        $result = $q->fetchObject(static::$BLOCK_OBJECT_TYPE);
        $q->closeCursor();

        return $result;
    }

    public function getBlocks(int $idSection)
    {
        if ($idSection > 0) {
            $q = $this->sql(
                'SELECT ' . static::$BLOCK_TABLE_CHAMPS . ' 
                FROM ' . static::$BLOCK_TABLE_NAME . ' 
                WHERE idSection = :idSection 
                ORDER BY blockOrder', 
                [':idSection' => $idSection]
            );
    
            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$BLOCK_OBJECT_TYPE);
            $q->closeCursor();
    
            return $result;
        } else {
            return false;
        }
    }

    public function setBlock(int $idSection, int $idAuthor, string $content = null, string $blockSize = 'blockLarge', 
    string $blockBackgroundColor = null, float $blockOpacity = 1, int $blockBorderWidth = null, string $blockBorderColor = null, 
    int $blockBorderRadius = null)
    {
        $blockOrder = $this->getCountBlocks($idSection) + 1;
        
        $this->sql(
            'INSERT INTO ' . static::$BLOCK_TABLE_NAME . ' 
            (
                idSection, idAuthor, content, blockOrder, blockSize, 
                blockBackgroundColor, blockOpacity, blockBorderWidth, blockBorderColor, blockBorderRadius
            ) 
            VALUES (
                :idSection, :idAuthor, :content, :blockOrder, :blockSize, 
                :blockBackgroundColor, :blockOpacity, :blockBorderWidth, :blockBorderColor, :blockBorderRadius
            )', 
            [
                ':idSection' => $idSection, ':idAuthor' => $idAuthor, ':content' => $content, 
                ':blockOrder' => $blockOrder, ':blockSize' => $blockSize, ':blockBackgroundColor' => $blockBackgroundColor, 
                ':blockOpacity' => $blockOpacity, ':blockBorderWidth' => $blockBorderWidth, ':blockBorderColor' => $blockBorderColor, 
                ':blockBorderRadius' => $blockBorderRadius
            ]
        );

        return $this->getLastInsertId();
    }

    public function updateBlock(int $idBlock, string $elem = null, $value, bool $isBool = false)
    {
        if ($elem && strpos(static::$BLOCK_TABLE_CHAMPS, $elem) !== false) {
            if ($isBool) {
                $this->sql(
                    'UPDATE ' . static::$BLOCK_TABLE_NAME . ' 
                    SET ' . $elem . ' = :value 
                    WHERE id = :idBlock', 
                    [':value' => intval($value), ':idBlock' => $idBlock]
                );
            } else {
                $this->sql(
                    'UPDATE ' . static::$BLOCK_TABLE_NAME . ' 
                    SET ' . $elem . ' = :value 
                    WHERE id = :idBlock', 
                    [':value' => $value, ':idBlock' => $idBlock]
                );
            }
    
            return true;
        } else {
            return false;
        }
    }

    public function changBlockOrder(string $direction, int $idSection, int $currentOrder)
    {
        switch ($direction) {
            case 'up':
                return $this->blockOrderUp($idSection, $currentOrder);
            break;

            case 'down':
                return $this->blockOrderDown($idSection, $currentOrder);
            break;

            default:
                return 'false';
        }
    }

    public function deleteBlock(CvBlock $block, bool $deleteEntireCv = false)
    {
        // delete img on block
        $filePaths = $this->extractFilePath($this->checkForImgEntries($block->getContent()));
        if (count($filePaths) > 0) {
            foreach($filePaths as $filePath) {
                $this->deleteFile($filePath);
            }
        }

        // delete block
        $this->sql(
            'DELETE FROM ' . static::$BLOCK_TABLE_NAME . ' 
            WHERE ' . static::$TABLE_PK . ' = :id', 
            [':id' => $block->getId()]
        );

        if (!$deleteEntireCv) {
            //re-order block
            $blocksAbove = $this->getblocksAboveOrderX($block->getIdSection(), $block->getBlockOrder());

            if (!empty($blocksAbove) && count($blocksAbove) > 0) {
                foreach ($blocksAbove as $block) {
                    $this->blockOrderDecrement($block->getId(), $block->getBlockOrder());
                }
            }
        }
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PRIVATE FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    private function getblocksAboveOrderX(int $idBlock, int $order)
    {
        if (!empty($idBlock) && $idBlock > 0 && !empty($order) && $order > 0) {
            $q = $this->sql(
                'SELECT ' . static::$BLOCK_TABLE_CHAMPS . ' 
                FROM ' . static::$BLOCK_TABLE_NAME . ' 
                WHERE id = :idBlock AND blockOrder > :blockOrder 
                ORDER BY blockOrder', 
                [':idBlock' => $idBlock, ':blockOrder' => $order]
            );
    
            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
            $q->closeCursor();
    
            return $result;
        }
    }

    private function getCountBlocks(int $idSection = null)
    {
        if ($idSection) {
            $q = $this->sql(
                'SELECT COUNT(*) 
                FROM ' . static::$BLOCK_TABLE_NAME . ' 
                WHERE idSection = :idSection', 
                [':idSection' => $idSection]
            );
            $result = $q->fetch();
            $q->closeCursor();

            return intval($result[0]);
        } else {
            return 0;
        }
    }

    private function blockOrderUp(int $idSection = null, int $order = null)
    {
        if ($idSection && $order && $order < $this->getCountBlocks($idSection)) {
            $this->sql(
                'UPDATE ' . static::$BLOCK_TABLE_NAME . ' 
                SET blockOrder = 0 
                WHERE idSection = :idSection AND blockOrder = :blockOrder', 
                [':idSection' => $idSection, ':blockOrder' => $order]
            );

            $this->sql(
                'UPDATE ' . static::$BLOCK_TABLE_NAME . ' 
                SET blockOrder = :newBlockOrder 
                WHERE idSection = :idSection AND blockOrder = :blockOrder', 
                [':idSection' => $idSection, ':blockOrder' => $order+1, ':newBlockOrder' => $order]
            );

            $this->sql(
                'UPDATE ' . static::$BLOCK_TABLE_NAME . ' 
                SET blockOrder = :newBlockOrder 
                WHERE idSection = :idSection AND blockOrder = 0', 
                [':idSection' => $idSection, ':newBlockOrder' => $order+1]
            );

            return 'true';
        } else {
            return 'false';
        }
    }

    private function blockOrderDown(int $idSection = null, int $order = null)
    {
        if ($idSection && $order && $order > 1) {
            $this->sql(
                'UPDATE ' . static::$BLOCK_TABLE_NAME . ' 
                SET blockOrder = 0 
                WHERE idSection = :idSection AND blockOrder = :blockOrder', 
                [':idSection' => $idSection, ':blockOrder' => $order]
            );

            $this->sql(
                'UPDATE ' . static::$BLOCK_TABLE_NAME . ' 
                SET blockOrder = :newBlockOrder 
                WHERE idSection = :idSection AND blockOrder = :blockOrder', 
                [':idSection' => $idSection, ':blockOrder' => $order-1, ':newBlockOrder' => $order]
            );

            $this->sql(
                'UPDATE ' . static::$BLOCK_TABLE_NAME . ' 
                SET blockOrder = :newBlockOrder 
                WHERE idSection = :idSection AND blockOrder = 0', 
                [':idSection' => $idSection, ':newBlockOrder' => $order-1]
            );

            return 'true';
        } else {
            return 'false';
        }
    }

    private function blockOrderDecrement(int $idBlock = null, int $order = null)
    {
        if ($idBlock && $idBlock > 0 && $order && $order > 1) {
            $this->sql(
                'UPDATE ' . static::$BLOCK_TABLE_NAME . ' 
                SET blockOrder = :order 
                WHERE id = :idBlock', 
                [':idBlock' => $idBlock, ':order' => $order-1]
            );
        }
    }
}
