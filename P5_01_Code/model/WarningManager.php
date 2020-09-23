<?php

namespace Chemin\ArtSchools\Model;

class WarningManager extends AbstractManager
{
    public static $TABLE_WARN_NAME = 'as_warning';
    public static $TABLE_WARN_CHAMPS ='id, idUser, reason, DATE_FORMAT(dateWarning, "%d/%m/%Y") AS dateWarning, DATE_FORMAT(dateUnwarning, "%d/%m/%Y") AS dateUnwarning, isActive';
    public static $TABLE_BAN_NAME = 'as_banishment';
    public static $TABLE_BAN_CHAMPS ='id, idUser, DATE_FORMAT(dateBanishment, "%d/%m/%Y") AS dateBanishment, DATE_FORMAT(dateUnbanishment, "%d/%m/%Y") AS dateUnbanishment, isActive';

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PUBLIC FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    /*------------------------------ warning ------------------------------*/
    public function warn(User $user, string $reason, UserManager $UserManager)
    {
        //add a warning to $user, send mail and banish $user if he have 3 or more warnings
        $this->setWarn($user, $reason, $UserManager);
        $nbWarn = $this->getNbActiveWarn($user);
        $this->sendWarnMail($user, $reason, $nbWarn);
        if ($nbWarn >= 3) {
            $this->ban($user, $UserManager);
        }
    }

    public function getWarnEntries(User $user, string $option = 'both')
    {
        //get active/inactive/both warnings of $user
        if ($option === 'active' || $option === 'both') {
            $arr['active'] = $this->getActiveWarn($user);
        }
        if ($option === 'inactive' || $option === 'both') {
            $arr['inactive'] = $this->getInactiveWarn($user);
        }
        return $arr;
    }

    public function getAllActiveWarn()
    {
        //get all active warnings
        $q = $this->sql(
            'SELECT ' . static::$TABLE_WARN_CHAMPS . ' 
            FROM ' . static::$TABLE_WARN_NAME . ' 
            WHERE isActive = 1'
        );
        return $q->fetchAll();
    }

    public function getNbActiveWarn(User $user)
    {
        //get the number of active warning on $user
        $q = $this->sql(
            'SELECT COUNT(*) 
            FROM ' . static::$TABLE_WARN_NAME . ' 
            WHERE idUser = :idUser AND isActive = 1', 
            [':idUser' => $user->getId()]
        );
        $result = $q->fetch();
        return intval($result[0]);
    }

    public function canUnwarn(string $dateUnwarn)
    {
        //check if $user can be unwarn
        $today = \DateTime::createFromFormat("d/m/Y", date('d/m/Y'));
        $dateUnwarn = \DateTime::createFromFormat("d/m/Y", $dateUnwarn);
        if ($today >= $dateUnwarn) {
            return true;
        } else {
            return false;
        }
    }

    public function unWarnUser(User $user)
    {
        //inactive all warnings of a user
        $this->sql(
            'UPDATE ' . static::$TABLE_WARN_NAME . ' 
            SET isActive = :isActive 
            WHERE idUser = :idUser AND isActive = 1', 
            [':isActive' => intval(false), ':idUser' => $user->getId()]
        );
    }

    public function unWarnEntry(int $idWarn)
    {
        //inactive one warn entry
        $this->sql(
            'UPDATE ' . static::$TABLE_WARN_NAME . ' 
            SET isActive = :isActive 
            WHERE id = :id AND isActive = 1', 
            [':isActive' => intval(false), ':id' => $idWarn]
        );
    }

    /*------------------------------ banishment ------------------------------*/
    public function ban(User $user, UserManager $UserManager)
    {
        //banish $user, inactive his warnings and send mail
        $this->setBan($user, $UserManager);
        $this->unWarnUser($user);
        $banEntry = $this->getBanEntry($user);
        $this->sendBanMail($user, $banEntry['dateUnbanishment']);
    }

    public function getBanEntry(User $user)
    {
        //get the ban entry of $user
        $q = $this->sql(
            'SELECT ' . static::$TABLE_BAN_CHAMPS . ' 
            FROM ' . static::$TABLE_BAN_NAME . ' 
            WHERE idUser = :idUser AND isActive = 1', 
            [':idUser' => $user->getId()]
        );
        return $q->fetch();
    }

    public function getAllActiveBan()
    {
        //get all the ban entries
        $q = $this->sql(
            'SELECT ' . static::$TABLE_BAN_CHAMPS . ' 
            FROM ' . static::$TABLE_BAN_NAME . ' 
            WHERE isActive = 1'
        );
        return $q->fetchAll();
    }

    public function unBan(User $user, UserManager $UserManager)
    {
        //unbanish $user, send mail and set 'isBan' as false on user's table
        $this->sql(
            'UPDATE ' . static::$TABLE_BAN_NAME . ' 
            SET isActive = :isActive 
            WHERE idUser = :idUser AND isActive = 1', 
            [':isActive' => intval(false), ':idUser' => $user->getId()]
        );
        $this->sendUnbanMail($user);
        //add information that the user is unban in the user table
        $UserManager->updateById($user->getId(), 'isBan', false, true);
    }

    public function canUnban(User $user)
    {
        //check if $user can be unbanish
        $banEntry = $this->getBanEntry($user);
        $today = \DateTime::createFromFormat("d/m/Y", date('d/m/Y'));
        $dateUnban = \DateTime::createFromFormat("d/m/Y", $banEntry['dateUnbanishment']);
        if ($today >= $dateUnban) {
            return true;
        } else {
            return false;
        }
    }

    public function isBan(User $user)
    {
        //check if $user is banish
        $q = $this->sql(
            'SELECT id 
            FROM ' . static::$TABLE_BAN_NAME . ' 
            WHERE idUser = :idUser AND isActive = 1', 
            [':idUser' => $user->getId()]
        );
        if ($result = $q->fetch()) {
            return true;
        } else {
            return false;
        }
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PRIVATE FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/
    private function sendWarnMail(User $user, string $reason, int $nbWarn)
    {
        $subject = "Votre compte a fait l'objet d'un avertissement";
        $content = "Bonjour " . $user->getName() . ".<br><br>
            Nous vous informons que votre compte a fait l'objet d'un avertissement, pour le motif suivant : <br> - " . $reason . "<br>
            Il s'agit de l'avertissement no° " . $nbWarn . ".<br><br>
            Au bout de 3 avertissements, votre compte est suspendu pendant un mois. 
            La durée de suspension du compte augmente d'un mois à chaque fois que vous atteignez les 3 avertissements.<br><br>
            L'equipe d'ArtSchools vous remercie.";
        $content = wordwrap($content, 70, "\r\n");
        $headers = array('From' => '"Art-Schools"<artschoolsfr@gmail.com>', 
            'Content-Type' => 'text/html; charset=utf-8');
        ini_set("sendmail_from", "artschoolsfr@gmail.com");
        mail($user->getMail(), $subject, $content, $headers);
    }

    private function sendBanMail(User $user, string $dateUnban)
    {
        $subject = "Votre compte a été suspendu";
        $content = "Bonjour " . $user->getName() . ".<br><br>
            Nous vous informons que votre compte a été suspendu, suite à 3 avertissements.<br>
            Votre compte sera de nouveau disponible a partir du " . $dateUnban . ".<br><br>
            La durée de suspension du compte augmente de un mois a chaque fois que vous atteignez les 3 avertissements.<br><br>
            L'equipe d'ArtSchools vous remercie.";
        $content = wordwrap($content, 70, "\r\n");
        $headers = array('From' => '"Art-Schools"<artschoolsfr@gmail.com>', 
            'Content-Type' => 'text/html; charset=utf-8');
        ini_set("sendmail_from", "artschoolsfr@gmail.com");
        mail($user->getMail(), $subject, $content, $headers);
    }

    private function sendUnbanMail(User $user)
    {
        $subject = "Votre compte est de nouveau disponible";
        $content = "Bonjour " . $user->getName() . ".<br><br>
            Nous avons le plaisir de vous informer que votre compte est de nouveau disponible.<br><br>
            L'equipe d'ArtSchools vous remercie.";
        $content = wordwrap($content, 70, "\r\n");
        $headers = array('From' => '"Art-Schools"<artschoolsfr@gmail.com>', 
            'Content-Type' => 'text/html; charset=utf-8');
        ini_set("sendmail_from", "artschoolsfr@gmail.com");
        mail($user->getMail(), $subject, $content, $headers);
    }

    /*------------------------------ warning ------------------------------*/
    private function setWarn(User $user, string $reason, UserManager $UserManager)
    {
        //set a warning to $user
        $dateWarn = \DateTime::createFromFormat("d/m/Y", date('d/m/Y'));
        $strDateUnwarn = $this->getDateUnwarning($dateWarn);
        $strDateWarn = $dateWarn->format('Y/m/d');
        $this->sql(
           'INSERT INTO ' . static::$TABLE_WARN_NAME . ' (idUser, reason, dateWarning, dateUnwarning) 
           VALUES (:idUser, :reason, :dateWarning, :dateUnwarning)', 
           [':idUser' => $user->getId(), ':reason' => $reason, ':dateWarning' => $strDateWarn, ':dateUnwarning' => $strDateUnwarn]
        );
        //add 1 to nbWarning on user table
        $UserManager->updateById($user->getId(), 'nbWarning', (intval($user->getNbWarning()) + 1));
    }

    private function getDateUnwarning(\DateTime $dateWarning, string $format = 'Y/m/d', string $responseType = 'string')
    {
        //return the date of unwarning comparing to $dateWarning
        $dateUnwarn = date($format, strtotime('+3 month', strtotime($dateWarning->format('Y/m/d'))));
        if ($responseType === 'DateTime') {
            $dateUnwarn = \DateTime::createFromFormat($format, $dateUnwarn);
        }
        return $dateUnwarn;
    }

    private function getInactiveWarn(User $user)
    {
        //get inactive warnings entries of $user
        $q = $this->sql(
            'SELECT ' . static::$TABLE_WARN_CHAMPS . ' 
            FROM ' . static::$TABLE_WARN_NAME . ' 
            WHERE idUser = :idUser AND isActive = 0', 
            [':idUser' => $user->getId()]
        );
        return $q->fetchAll();
    }

    private function getActiveWarn(User $user)
    {
        //get active warnings entries of $user
        $q = $this->sql(
            'SELECT ' . static::$TABLE_WARN_CHAMPS . ' 
            FROM ' . static::$TABLE_WARN_NAME . ' 
            WHERE idUser = :idUser AND isActive = 1', 
            [':idUser' => $user->getId()]
        );
        return $q->fetchAll();
    }

    /*------------------------------ banishment ------------------------------*/
    private function setBan(User $user, UserManager $UserManager)
    {
        //banish $user
        $dateBan = \DateTime::createFromFormat("d/m/Y", date('d/m/Y'));
        $strDateUnban = $this->getDateUnbanishment($dateBan, $user);
        $strDateBan = $dateBan->format('Y/m/d');
        $this->sql(
           'INSERT INTO ' . static::$TABLE_BAN_NAME . ' (idUser, dateBanishment, dateUnbanishment) 
           VALUES (:idUser, :dateBanishment, :dateUnbanishment)', 
           [':idUser' => $user->getId(), ':dateBanishment' => $strDateBan, ':dateUnbanishment' => $strDateUnban]
        );
        //add information that the user is ban, in the user table
        $UserManager->updateById($user->getId(), 'isBan', true, true);
    }

    private function getDateUnbanishment(\DateTime $dateBanishment, User $user, string $format = 'Y/m/d', string $responseType = 'string')
    {
        //return the date of unbanishment comparing to $datebanishment
        //the ban time is extended by one month for each previous user's ban
        $duration = $this->getNbBan($user) + 1;
        $dateUnban = date($format, strtotime('+' . $duration . ' month', strtotime($dateBanishment->format('Y/m/d'))));
        if ($responseType === 'DateTime') {
            $dateUnban = \DateTime::createFromFormat($format, $dateUnban);
        }
        return $dateUnban;
    }

    private function getNbBan(User $user)
    {
        //get the number of banishment $user have
        $q = $this->sql(
            'SELECT COUNT(*) 
            FROM ' . static::$TABLE_BAN_NAME . ' 
            WHERE idUser = :idUser', 
            [':idUser' => $user->getId()]
        );
        $result = $q->fetch();
        return intval($result[0]);
    }
}