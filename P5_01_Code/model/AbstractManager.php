<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\Database;

abstract class AbstractManager extends Database
{
    public static $TABLE_NAME = '';
    public static $TABLE_CHAMPS = '';
    public static $TABLE_PK = '';
    public static $OBJECT_TYPE = '';
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

    public function moveImgAndUpdateContent(string $content = null, string $destination = 'public/images/dl', int $maxFile = 5)
    {
        if (!empty($content)) {
            $tempFolder = 'public/images/temp';
            // get filepaths from img entries
            $imgEntries = $this->checkForImgEntries($content);
            $filePaths = $this->extractFilePath($imgEntries);
            // delete extra entries in content and associated files
            $content = $this->deleteExtraImgEntries($content, $this->extractFileName($imgEntries), $maxFile);
            $filePaths = $this->deleteExtraFiles($filePaths, $maxFile);
            // move uploaded img from $tempFolder to $destination folder
            $success = true;
            $arrSuccess = [];
            if (count($filePaths) > 0) {
                foreach ($filePaths as $filePath) {
                    if (strpos($filePath, $tempFolder) !== false) {
                        // img is on the temp folder -> move to $destination
                        if ($this->moveFile($filePath, $destination) !== 1) {
                            $success = false;
                            $arrSuccess[] = false;
                        } else {
                            $arrSuccess[] = true;
                        }
                    } else {
                        // img is not in the temp folder
                        // set $arrSuccess[] to false -> because if $success === false, we don't have to str_replace to delete this img, 
                        $arrSuccess[] = false;
                    }
                }
            }
            if ($success) {
                // update file path on content
                $newFilePaths = str_replace($tempFolder, $destination, $filePaths);
                $content = str_replace($filePaths, $newFilePaths, $content);
            } else {
                // delete all uploaded img
                for ($i = 0; $i < count($filePaths); $i++) {
                    if ($arrSuccess[$i]) {
                        $newFilePath = str_replace($tempFolder, $destination, $filePaths[$i]);
                        $this->deleteFile($newFilePath);
                    } else {
                        $this->deleteFile($filePaths[$i]);
                    }
                }
                return false;
            }
        }
        return $content;
    }

    public function deleteImgDoublon(string $content = null)
    {
        if (!empty($content)) {
            $arrUniqueElem = [];
            $pattern = [];
            $imgEntries = $this->checkForImgEntries($content);
            $fileNames = $this->extractFileName($imgEntries);
            if (count($imgEntries) > 0) {
                for ($i = 0; $i < count($imgEntries); $i++) {
                    if (!in_array($imgEntries[$i], $arrUniqueElem)) {
                        // not a doublon
                        $arrUniqueElem[] = $imgEntries[$i];
                    } else {
                        // doublon
                        $pattern[] = '#\<img[^\>]*' . $fileNames[$i] . '[^\<]*\>#';
                    }
                }
                if (count($pattern) > 0) {
                    $content = preg_replace($pattern, '', $content, 1);
                }
            }
        }
        return $content;
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PROTECTED FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/
    protected function getUniqueElems(array $elems, int $qtt = null)
    {
        $arrUniqueElems = [];
        if (count($elems) > 0 && $qtt !== null && $qtt > 0) {
            foreach ($elems as $elem) {
                if (!in_array($elem, $arrUniqueElems)) {
                    $arrUniqueElems[] = $elem;
                }
                if (count($arrUniqueElems) === $qtt) {
                    break;
                }
            }
        }
        return $arrUniqueElems;
    }

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
    protected function deleteFile(string $filePath = null)
    {
        if ($filePath && strlen($filePath) > 0 && file_exists($filePath) && strpos($filePath, 'question-mark') === false) {                              
            unlink($filePath);
        }
        return $this;
    }

    protected function deleteExtraFiles(array $filePaths = [], int $maxFiles = 5)
    {
        $uniqueFilePaths = $this->getUniqueElems($filePaths, $maxFiles);
        if (!empty($filePaths) && count($filePaths) > $maxFiles) {
            for ($i = 0; $i < count($filePaths); $i++) {
                if (!in_array($filePaths[$i], $uniqueFilePaths)) {
                    $this->deleteFile($filePaths[$i]);
                }
            }
        }
        return $uniqueFilePaths;
    }

    protected function deleteExtraImgEntries(string $content = null, array $fileNames = [], int $maxEntries = 5)
    {
        if (!empty($content) && count($fileNames) > $maxEntries) {
            $uniqueFileNames = $this->getUniqueElems($fileNames, $maxEntries);
            $pattern = [];
            for ($i = 0; $i < count($fileNames); $i++) {
                if (!in_array($fileNames[$i], $uniqueFileNames)) {
                    $pattern[] = '#\<img[^\>]*' . $fileNames[$i] . '[^\<]*\>#';
                }
            }
            if (count($pattern) > 0) {
                $content = preg_replace($pattern, '', $content);
            }
        }
        return $content;
    }

    protected function moveFile(string $filePath, string $destination = "public/images/dl")
    {
        if (!file_exists($filePath)) {
            return -1;
        } else {
            $newFilePath = str_replace('public/images/temp', $destination, $filePath);
            if(!copy($filePath, $newFilePath)) {
                return -2;
            } else {
                if(!unlink($filePath)) {
                    return -3;
                }
            }
        }
        return 1;
    }

    protected function extractFileName(array $imgEntries = null)
    {
        if (!empty($imgEntries)) {
            for ($i = 0; $i < count($imgEntries); $i++) {
                $arr = explode('/', $imgEntries[$i]);
                $arr = explode('.', $arr[count($arr) - 1]);
                $imgEntries[$i] = $arr[0];
            }
        }
        return $imgEntries;
    }
}
