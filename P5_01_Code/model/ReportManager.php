<?php

namespace Chemin\ArtSchools\Model;

use Chemin\ArtSchools\Model\AbstractManager;

class ReportManager extends AbstractManager
{
    public static $REPORT_POST_TABLE_NAME = 'as_report_post';
    public static $REPORT_POST_TABLE_CHAMPS = 'idPost, idUser, DATE_FORMAT(dateReport, "%d/%m/%Y à %H:%i %s") AS dateReport, content';
    public static $REPORT_POST_TABLE_CHAMPS_WITH_USER = 'r.idPost, r.idUser, DATE_FORMAT(r.dateReport, "%d/%m/%Y à %H:%i %s") AS dateReport, r.content, 
        u.firstName AS authorFirstName, u.lastName AS authorLastName';

    public static $REPORT_COMMENT_TABLE_NAME = 'as_report_comment';
    public static $REPORT_COMMENT_TABLE_CHAMPS = 'idComment, idUser, DATE_FORMAT(dateReport, "%d/%m/%Y à %H:%i %s") AS dateReport, content';
    public static $REPORT_COMMENT_TABLE_CHAMPS_WITH_USER = 'r.idComment, r.idUser, DATE_FORMAT(r.dateReport, "%d/%m/%Y à %H:%i %s") AS dateReport, r.content, 
        u.firstName AS authorFirstName, u.lastName AS authorLastName';

    public static $REPORT_OTHER_TABLE_NAME = 'as_report_other';
    public static $REPORT_OTHER_TABLE_CHAMPS = 'id, idUser, DATE_FORMAT(dateReport, "%d/%m/%Y à %H:%i %s") AS dateReport, content';
    public static $REPORT_OTHER_TABLE_CHAMPS_WITH_USER = 'r.id, r.idUser, DATE_FORMAT(r.dateReport, "%d/%m/%Y à %H:%i %s") AS dateReport, r.content, 
        u.firstName AS authorFirstName, u.lastName AS authorLastName';

    public static $TABLE_USER_NAME = 'as_user';
    public static $LIMIT = 10;

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PUBLIC FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    public function getReports(string $elem, bool $limit = false, int $offset = 0)
    {
        if (!empty($elem)) {
            $limit ? $clauseLimit = 'LIMIT ' . static::$LIMIT . ' OFFSET ' . $offset : $clauseLimit = '';
            switch ($elem) {
            case 'post' :
                $query = $this->getPostReports($clauseLimit);
                break;
            case 'comment' :
                $query = $this->getCommentReports($clauseLimit);
                break;
            case 'other' :
                $query = $this->getOtherReports($clauseLimit);
                break;
            }
            $result = $query->fetchAll();
            $query->closeCursor();
            return $result;
        }
    }

    public function getReportsFromElem(string $elem, int $idElem)
    {
        if (!empty($elem) && $idElem > 0) {
            switch ($elem) {
            case 'post' :
                $query = $this->sql(
                    'SELECT ' . static::$REPORT_POST_TABLE_CHAMPS_WITH_USER . ' 
                    FROM ' . static::$REPORT_POST_TABLE_NAME . ' AS r 
                    LEFT JOIN ' . static::$TABLE_USER_NAME . ' AS u 
                    ON u.id = r.idUser 
                    WHERE r.idPost = :idPost', 
                    [':idPost' => $idElem]
                );
                break;
            case 'comment' :
                $query = $this->sql(
                    'SELECT ' . static::$REPORT_COMMENT_TABLE_CHAMPS_WITH_USER . ' 
                    FROM ' . static::$REPORT_COMMENT_TABLE_NAME . ' AS r 
                    LEFT JOIN ' . static::$TABLE_USER_NAME . ' AS u 
                    ON u.id = r.idUser 
                    WHERE r.idComment = :idComment', 
                    [':idComment' => $idElem]
                );
                break;
            }
            $result = $query->fetchAll();
            $query->closeCursor();
            return $result;
        }
    }

    public function setReport(string $elem, string $content, int $idElem = null, int $idUser = null)
    {
        if (!empty($elem) && !empty($content) && $idUser !== null && $idUser > 0 && $this->checkForScriptInsertion([$content])) {
            switch ($elem) {
                case 'post' :
                    if (!empty($idElem) && $idElem > 0) {
                        $this->setPostReport($content, $idElem, $idUser);
                    }
                    break;
                case 'comment' :
                    if (!empty($idElem) && $idElem > 0) {
                        $this->setCommentReport($content, $idElem, $idUser);
                    }
                    break;
                case 'other' :
                    $this->setOtherReport($content, $idUser);
                    break;
            }
        }
        return $this;
    }

    public function deleteReport(string $elem, int $idElem, int $idUser = null)
    {
        if (!empty($elem)) {
            switch ($elem) {
                case 'post' :
                    $this->deletePostReport($idElem, $idUser);
                    break;
                case 'comment' :
                    $this->deleteCommentReport($idElem, $idUser);
                    break;
                case 'other' :
                    $this->deleteOtherReport($idElem);
                    break;
            }
        }
        return $this;
    }

    public function deleteReportsFromElem(string $elem, int $idElem)
    {
        if (!empty($elem) && $idElem > 0) {
            switch ($elem) {
            case 'post' :
                $this->sql(
                    'DELETE FROM ' . static::$REPORT_POST_TABLE_NAME . ' 
                    WHERE idPost = :idPost',
                    [':idPost' => $idElem]
                );
                break;
            case 'comment' :
                $this->sql(
                    'DELETE FROM ' . static::$REPORT_COMMENT_TABLE_NAME . ' 
                    WHERE idComment = :idComment',
                    [':idComment' => $idElem]
                );
                break;
            }
        }
        return $this;
    }

    public function reportExists(string $elem, int $idElem, int $idUser = null)
    {
        if (!empty($elem)) {
            switch ($elem) {
                case 'post' :
                    $result = $this->postReportExists($idElem, $idUser);
                    break;
                case 'comment' :
                    $result = $this->commentReportExists($idElem, $idUser);
                    break;
                case 'other' :
                    $result = $this->otherReportExists($idElem);
                    break;
                default : 
                    return false;
            }
            if ($result) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getCount(string $elem, int $idElem = null)
    {
        if (!empty($elem)) {
            switch ($elem) {
                case 'post' :
                    $query = $this->getPostReportCount($idElem);
                    break;
                case 'comment' :
                    $query = $this->getCommentReportCount($idElem);
                    break;
                case 'other' :
                    //count all other reports
                    $query = $this->getOtherReportCount();
                    break;
            }
            $response = $query->fetch();
            $query->closeCursor();
            return $response[0];
        }
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PRIVATE FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    private function getPostReports(string $clauseLimit)
    {
        return $this->sql(
            'SELECT ' . static::$REPORT_POST_TABLE_CHAMPS_WITH_USER . ' 
            FROM ' . static::$REPORT_POST_TABLE_NAME . ' AS r 
            LEFT JOIN ' . static::$TABLE_USER_NAME . ' AS u 
            ON u.id = r.idUser 
            ' . $clauseLimit
        );
    }

    private function getCommentReports(string $clauseLimit)
    {
        return $this->sql(
            'SELECT ' . static::$REPORT_COMMENT_TABLE_CHAMPS_WITH_USER . ' 
            FROM ' . static::$REPORT_COMMENT_TABLE_NAME . ' AS r 
            LEFT JOIN ' . static::$TABLE_USER_NAME . ' AS u 
            ON u.id = r.idUser 
            ' . $clauseLimit
        );
    }

    private function getOtherReports(string $clauseLimit)
    {
        return $this->sql(
            'SELECT ' . static::$REPORT_OTHER_TABLE_CHAMPS_WITH_USER . ' 
            FROM ' . static::$REPORT_OTHER_TABLE_NAME . ' AS r 
            LEFT JOIN ' . static::$TABLE_USER_NAME . ' AS u 
            ON u.id = r.idUser 
            ' . $clauseLimit
        );
    }

    private function setPostReport(string $content, int $idElem, int $idUser)
    {
        $this->sql(
            'INSERT INTO ' . static::$REPORT_POST_TABLE_NAME . ' (idPost, idUser, content, dateReport) 
            VALUES (:idPost, :idUser, :content, NOW())', 
            [':idPost' => $idElem, ':idUser' => $idUser, ':content' => $content]
        );
    }

    private function setCommentReport(string $content, int $idElem, int $idUser)
    {
        $this->sql(
            'INSERT INTO ' . static::$REPORT_COMMENT_TABLE_NAME . ' (idComment, idUser, content, dateReport) 
            VALUES (:idComment, :idUser, :content, NOW())', 
            [':idComment' => $idElem, ':idUser' => $idUser, ':content' => $content]
        );
    }

    private function setOtherReport(string $content, int $idUser)
    {
        $this->sql(
            'INSERT INTO ' . static::$REPORT_OTHER_TABLE_NAME . ' (idUser, content, dateReport) 
            VALUES (:idUser, :content, NOW())', 
            [':idUser' => $idUser, ':content' => $content]
        );
    }

    private function deletePostReport(int $idElem, int $idUser)
    {
        if ($idElem > 0 && $idUser > 0 && $this->reportExists('post', $idElem, $idUser)) {
            $this->sql(
                'DELETE FROM ' . static::$REPORT_POST_TABLE_NAME . ' 
                WHERE idUser = :idUser AND idPost = :idPost',
                [':idUser' => $idUser, ':idPost' => $idElem]
            );
        }
    }

    private function deleteCommentReport(int $idElem, int $idUser)
    {
        if ($idElem > 0 && $idUser > 0 && $this->reportExists('comment', $idElem, $idUser)) {
            $this->sql(
                'DELETE FROM ' . static::$REPORT_COMMENT_TABLE_NAME . ' 
                WHERE idUser = :idUser AND idComment = :idComment',
                [':idUser' => $idUser, ':idComment' => $idElem]
            );
        }
    }

    private function deleteOtherReport(int $idElem)
    {
        if ($idElem > 0 && $this->reportExists('other', $idElem)) {
            $this->sql(
                'DELETE FROM ' . static::$REPORT_OTHER_TABLE_NAME . ' 
                WHERE id = :id',
                [':id' => $idElem]
            );
        }
    }

    private function postReportExists(int $idElem, int $idUser)
    {
        if ($idElem > 0 && $idUser > 0) {
            $req = $this->sql(
                'SELECT * 
                FROM ' . static::$REPORT_POST_TABLE_NAME . ' 
                WHERE idUser = :idUser AND idPost = :idPost',
                [':idUser' => $idUser, ':idPost' => $idElem]
            );
            $result = $req->fetch();
            $req->closeCursor();
            return $result;
        } else {
            return false;
        }
    }

    private function commentReportExists(int $idElem, int $idUser)
    {
        if ($idElem > 0 && $idUser > 0) {
            $req = $this->sql(
                'SELECT * 
                FROM ' . static::$REPORT_COMMENT_TABLE_NAME . ' 
                WHERE idUser = :idUser AND idComment = :idComment',
                [':idUser' => $idUser, ':idComment' => $idElem]
            );
            $result = $req->fetch();
            $req->closeCursor();
            return $result;
        } else {
            return false;
        }
    }

    private function otherReportExists(int $idElem)
    {
        if ($idElem > 0) {
            $req = $this->sql(
                'SELECT * 
                FROM ' . static::$REPORT_OTHER_TABLE_NAME . ' 
                WHERE id = :id',
                [':id' => $idElem]
            );
            $result = $req->fetch();
            $req->closeCursor();
            return $result;
        } else {
            return false;
        }
    }

    private function getPostReportCount($idElem = null)
    {
        if (!empty($idElem)) {
            //count for one post
            return $this->sql(
                'SELECT COUNT(*) 
                FROM ' . static::$REPORT_POST_TABLE_NAME . ' 
                WHERE idPost = :idPost', 
                [':idPost' => $idElem]
            );
        } else {
            //count all post reports
            return $this->sql(
                'SELECT COUNT(*) 
                FROM ' . static::$REPORT_POST_TABLE_NAME
            );
        }
    }

    private function getCommentReportCount($idElem = null)
    {
        if (!empty($idElem)) {
            //count for one elem
            return $this->sql(
                'SELECT COUNT(*) 
                FROM ' . static::$REPORT_COMMENT_TABLE_NAME . ' 
                WHERE idComment = :idComment', 
                [':idComment' => $idElem]
            );
        } else {
            //count all comment reports
            return $this->sql(
                'SELECT COUNT(*) 
                FROM ' . static::$REPORT_COMMENT_TABLE_NAME
            );
        }
    }

    private function getOtherReportCount()
    {
        return $this->sql(
            'SELECT COUNT(*) 
            FROM ' . static::$REPORT_OTHER_TABLE_NAME
        );
    }
}
