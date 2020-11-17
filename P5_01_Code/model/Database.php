<?php

namespace Chemin\ArtSchools\Model;

abstract class Database
{
    protected static $db;

    const DB_HOST = "mysql:host=;dbname=art_school;charset=utf8",
    DB_LOGIN = "root",
    DB_PASSWORD = "",
    DB_ERRMODE = array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION);

    protected function getConnection()
    {
        if (empty(self::$db)) {
            self::$db = new \PDO(self::DB_HOST, self::DB_LOGIN, self::DB_PASSWORD, self::DB_ERRMODE);
            return self::$db;
        } else {
            return self::$db;
        }
    }

    public function sql($req, array $parameters = null)
    {
        $q = $this->getConnection()->prepare($req);

        if ($parameters) {
            foreach ($parameters as $paraKey => $paraValue) {
                if (is_int($paraValue)) {
                    $q->bindValue($paraKey, $paraValue, \PDO::PARAM_INT);
                } else {//is string
                    $q->bindValue($paraKey, $paraValue);
                }
            }
        }

        $q->execute();

        return $q;
    }

    public function getLastInsertId()
    {
        $q = $this->getConnection();
        return $q->lastInsertId();
    }
}
