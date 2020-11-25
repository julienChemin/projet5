<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\Database;

abstract class AbstractManager extends Database
{
    /*-------------------------------------------------------------------------------------
    ----------------------------------- PUBLIC FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/
    
    public function getOneById(int $id)
    {
        if ($id > 0 && $this->exists($id)) {
            $query = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE ' . static::$TABLE_PK . ' = :id', 
                [':id' => $id]
            );
            if (isset(static::$OBJECT_TYPE)) {
                   $result = $query->fetchObject(static::$OBJECT_TYPE);
            } else {
                $result = $query->fetch();
            }
            $query->closeCursor();
            return $result;
        } else {
            return false;
        }
    }

    public function exists(int $id)
    {
        if ($id > 0) {
            $req = $this->sql(
                'SELECT * 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE ' . static::$TABLE_PK . ' = :id', 
                [':id' => $id]
            );
            if ($result = $req->fetch()) {
                   return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function delete(int $id)
    {
        if ($id > 0) {
            $this->sql(
                'DELETE FROM ' . static::$TABLE_NAME . ' 
                WHERE ' . static::$TABLE_PK . ' = :id', 
                [':id' => $id]
            );
        }
        return $this;
    }

    public function checkForScriptInsertion(array $arr)
    {
        $regexScript = '/^.*&lt; *script.*$/is';
        $regexIframe = '/^.*&lt; *iframe.*$/is';
        if (!empty($arr)) {
            foreach ($arr as $str) {
                if (is_array($str)) {
                    if (!$this->checkForScriptInsertion($str)) {
                        return false;
                    }
                } elseif (is_string($str) && (preg_match($regexScript, htmlspecialchars($str)) || preg_match($regexIframe, htmlspecialchars($str)))) {
                    return false;
                }
            }
        }
        return true;
    }

    public function checkForImgEntries(string $content)
    {
        $imgEntries = [];
        if (strlen($content) > 0) {
            $regex = '/src=\"(.+)\"/U';
            preg_match_all($regex, $content, $matches, PREG_OFFSET_CAPTURE);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $filePath) {
                    $imgEntries[] = $filePath[0];
                }
            }
        }
        return($imgEntries);
    }

    public function extractFilePath(array $imgEntries)
    {
        $arrFilePath = [];
        if (count($imgEntries) > 0) {
            foreach ($imgEntries as $entry) {
                $arr = explode('/', $entry);
                if (count($arr) > 0) {
                    $uri = '';
                    for ($i = count($arr) - 1; $i >= 0; $i--) {
                        if ($i < count($arr) - 1) {
                            $uri = '/' . $uri;
                        }
                        $uri = $arr[$i] . $uri;
                        if ($arr[$i] === 'public') {
                            break;
                        }
                    }
                }
                $arrFilePath[] = $uri;
            }
        }
        return $arrFilePath;
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PROTECTED FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    protected function arrayWithoutEmptyEntries(array $arr)
    {
        if (count($arr) > 0) {
            $sortedArray = [];
            foreach ($arr as $value) {
                if ($value !== null) {
                    $sortedArray[] = $value;
                }
            }
            return $sortedArray;
        } else {
            return $arr;
        }
    }

    protected function accessDenied()
    {
        throw new \Exception("Vous n'avez pas accès à cette page");
    }

    protected function invalidLink()
    {
        throw new \Exception("Ce lien a expiré ou la page n'existe pas");
    }

    protected function incorrectInformation()
    {
        throw new \Exception("Les informations renseignées sont incorrectes");
    }

    protected function error(string $error_msg)
    {
        if (defined('BACKEND')) {
            $side = 'backend';
        } else {
            $side = 'frontend';
        }
        RenderView::render('template.php', $side . '/errorView.php', ['error_msg' => $error_msg]);
    }

    /*------------------------------ file stuff ------------------------------*/
    protected function deleteFile(string $filePath)
    {
        if (strlen($filePath) > 0 && file_exists($filePath) && strpos($filePath, 'question-mark') === false) {                              
            unlink($filePath);
        }
        return $this;
    }
}
