<?php

namespace Chemin\ArtSchools\Model;

class ContractManager extends AbstractManager
{
    public static $TABLE_SCHOOL_CR_NAME = 'as_school_contract_reminder';
    public static $TABLE_USER_CR_NAME = 'as_user_contract_reminder';

    public static $TABLE_NAME = '';
    public static $TABLE_CHAMPS ='id, idOwner, mailToRemind, remindType, DATE_FORMAT(dateRemind, "%d/%m/%Y") AS dateRemind, 
        DATE_FORMAT(dateContractEnd, "%d/%m/%Y") AS dateContractEnd, done';
    public static $TYPE = '';
    public static $MANAGER;

    /*-------------------------------------------------------------------------------------
    ----------------------------------- FUNCTION PUBLIC ------------------------------------
    -------------------------------------------------------------------------------------*/
    public function __construct(string $type, object $Manager)
    {
        //$type -> "user" or "school" / $Manager -> UserManager or SchoolManager
        if ($type === 'user') {
            static::$TABLE_NAME = static::$TABLE_USER_CR_NAME;
        } elseif ($type === 'school') {
            static::$TABLE_NAME = static::$TABLE_SCHOOL_CR_NAME;
        }
        static::$TYPE = $type;
        static::$MANAGER = $Manager;
    }

    public function extendContract(object $owner, int $extensionDuration, int $nbEleve = 0)
    {
        // $owner expect type 'User' or 'School' / $Manager expect 'UserManager' or 'SchoolManager' (depend of the owner type)
        if ($owner->getIsActive()) {
            // disable last remind and set the new one
            if ($lastRemind = $this->getLastRemind($owner->getId())) {
                $this->disableRemind($lastRemind['id']);
                $dateContractEnd = $this->adjustDate($lastRemind['dateContractEnd'], '+', $extensionDuration, 'month');
            } else {
                // this case happend only with school, when she was just created by webmaster with more than 0 month of duration
                // (new school is create as active before there is any remind set)
                $dateContractEnd = $this->adjustDate($this->getToday(true), '+', $extensionDuration, 'month');
            }
            $this->setRemind($owner->getId(), $owner->getMail(), $dateContractEnd);
            if (static::$TYPE === 'school') {
                // add history entry
                $HistoryManager = new HistoryManager();
                $HistoryManager->addEntry(new HistoryEntry(
                    ['idSchool' => $owner->getId(), 
                    'category' => 'activityPeriod', 
                    'entry' => 'La date de fin d\'abonnement a été repoussé jusqu\'au ' . $dateContractEnd->format('d/m/Y')])
                );
            }
        } else {
            // set remind and set owner account as active
            $dateContractEnd = $this->adjustDate($this->getToday(true), '+', $extensionDuration, 'month');
            $this->setRemind($owner->getId(), $owner->getMail(), $dateContractEnd);
            if (static::$TYPE === 'user') {
                static::$MANAGER->updateById($owner->getId(), 'isActive', true, true);
            } elseif (static::$TYPE === 'school') {
                static::$MANAGER->schoolToActive($owner->getId(), new UserManager, $nbEleve);
                //add history entry
                $HistoryManager = new HistoryManager();
                $HistoryManager->addEntry(new HistoryEntry(
                    ['idSchool' => $owner->getId(), 
                    'category' => 'activityPeriod', 
                    'entry' => 'La date de fin d\'abonnement a été repoussé jusqu\'au ' . $dateContractEnd->format('d/m/Y')])
                );
            }
        }
    }

    public function deleteRemind(object $owner)
    {
        $remind = $this->getLastRemind($owner->getId());
        if (!empty($remind)) {
            $this->editDateEndContract($remind['id']);
            $this->disableRemind($remind['id']);
        }
    }

    public function getDateContractEnd(int $idOwner)
    {
        if ($lastRemind = $this->getLastRemind($idOwner)) {
            return $lastRemind['dateContractEnd'];
        } else {
            return false;
        }
        
    }

    public function checkRemind()
    {
        $reminds = $this->getAllActiveRemind();
        $arr['nbRemind'] = count($reminds);
        $arr['nbRemindDone'] = 0;
        foreach ($reminds as $remind) {
            if ($this->getToday() >= $this->getDateTime($remind['dateRemind'], 'd/m/Y')) {
                $this->remind($remind);
                $arr['nbRemindDone'] += 1;
            }
        }
        return $arr;
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- FUNCTION PRIVATE ------------------------------------
    -------------------------------------------------------------------------------------*/
    private function sendRemindMail(string $mail, string $remindType)
    {
        $subject = "Rappel pour votre abonnement ArtSchools";
        $content = $this->getMailContent($remindType);
        $headers = array('From' => '"Art-Schools"<artschoolsfr@gmail.com>', 
            'Content-Type' => 'text/html; charset=utf-8');
        ini_set("sendmail_from", "artschoolsfr@gmail.com");
        mail($mail, $subject, $content, $headers);
    }

    private function getMailContent(string $remindType)
    {
        switch ($remindType) {
            case 'month' :
                $timeLaps = 'dans un mois';
            break;
            case 'week' :
                $timeLaps = 'dans une semaine';
            break;
            case 'end' :
                $timeLaps = 'aujourd\'hui';
            break;
        }
        $content = "Bonjour, <br><br>
            nous vous envoyons un mail de rappel, car votre abonnement prend fin " . $timeLaps . "<br><br>
            Lorsque l'abonnement prend fin, Vous perdez access à certains privilège.<br>
            Vous pouvez vous référer à la <a href='index.php?action=faq'>F.A.Q</a> pour en savoir plus. 
            (ou sur le site ArtSchools et en cliquant sur 'F.A.Q' en bas de page)<br><br>
            Vous pouvez renouveler votre abonnement en passant par <a href='julienchemin.fr/projet5/index.php?action=settings'>ce lien</a>, 
            ou sur le site ArtSchools dans les paramètres de votre compte .<br><br>
            L'equipe d'ArtSchools vous remercie.";
        return wordwrap($content, 70, "\r\n");
    }

    private function remind(array $remind)
    {
        $dateContractEnd = $this->getDateTime($remind['dateContractEnd'], 'd/m/Y');
        $this->sendRemindMail($remind['mailToRemind'], $remind['remindType']);
        $this->disableRemind($remind['id']);
        if ($this->getToday() >= $dateContractEnd) {
            $this->contractEnd($remind['idOwner']);
        } else {
            $this->setRemind($remind['idOwner'], $remind['mailToRemind'], $dateContractEnd);
        }
    }

    private function contractEnd(int $idOwner)
    {
        //switch type - inactive owner
        if (static::$TYPE === 'user') {
            static::$MANAGER->updateById($idOwner, 'isActive', false, true);
        } elseif (static::$TYPE === 'school') {
            static::$MANAGER->schoolToInactive($idOwner, new UserManager);
        }
    }

    private function getAllActiveRemind()
    {
        $q = $this->sql(
            'SELECT ' . static::$TABLE_CHAMPS . ' 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE done = 0'
        );
        return $q->fetchAll();
    }

    private function getLastRemind(int $idOwner)
    {
        $q = $this->sql(
            'SELECT ' . static::$TABLE_CHAMPS . ' 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE idOwner = :idOwner 
            ORDER BY id DESC', 
            [':idOwner' => $idOwner]
        );
        if ($result = $q->fetch()) {
            return $result;
        } else {
            return false;
        }
    }

    private function setRemind(int $idOwner, string $mailOwner, \DateTime $dateContractEnd)
    {
        $infoDate = $this->createRemindInfo($dateContractEnd);
        $this->sql(
            'INSERT INTO ' . static::$TABLE_NAME . ' (idOwner, mailToRemind, remindType, dateRemind, dateContractEnd) 
            VALUES (:idOwner, :mailToRemind, :remindType, :dateRemind, :dateContractEnd)', 
            [':idOwner' => $idOwner, ':mailToRemind' => $mailOwner, ':remindType' => $infoDate['remindType'], 
                ':dateRemind' => $infoDate['dateRemind'], ':dateContractEnd' => $infoDate['dateContractEnd']]
         );
    }

    private function editDateEndContract(int $idRemind)
    {
        $today = $this->getToday(true);
        $this->sql(
            'UPDATE ' . static::$TABLE_NAME . ' 
            SET dateContractEnd = :today 
            WHERE id = :id', 
            [':today' => $today, ':id' => $idRemind]
        );
    }

    private function disableRemind(int $idRemind)
    {
        $this->sql(
            'UPDATE ' . static::$TABLE_NAME . ' 
            SET done = :done 
            WHERE id = :id', 
            [':done' => intval(true), ':id' => $idRemind]
        );
    }

    private function createRemindInfo(\DateTime $dateContractEnd)
    {
        if ($this->getToday() < $this->adjustDate($dateContractEnd, '-', 1, 'month')) {
            // set remind for 1 month before dateEndContract
            $arrInfo['dateRemind'] = $this->adjustDate($dateContractEnd, '-', 1, 'month', true);
            $arrInfo['remindType'] = 'month';
        } elseif ($this->getToday() < $this->adjustDate($dateContractEnd, '-', 1, 'week')) {
            // set remind for 1 week before dateEndContract
            $arrInfo['dateRemind'] = $this->adjustDate($dateContractEnd, '-', 1, 'week', true);
            $arrInfo['remindType'] = 'week'; 
        } else {
             // 1 week or less left before end of contract, set remind for the contract end day
             $arrInfo['dateRemind'] = $dateContractEnd->format('Y/m/d');
             $arrInfo['remindType'] = 'end';
        }
        $arrInfo['dateContractEnd'] = $dateContractEnd->format('Y/m/d');
        return $arrInfo;
    }

    private function adjustDate($date, string $operator, int $value, string $elem, bool $returnString = false, string $format = 'Y/m/d')
    {
        //$date must be object \DateTime or string with a accepted date format
        if (is_object($date)) {
            $strDate = $date->format($format);
        } else {
            $strDate = $date;
        }
        $date = date($format, strtotime($operator . $value . $elem, strtotime($strDate)));
        if ($returnString) {
            return $date;
        } else {
            return \DateTime::createFromFormat($format, $date);
        }
    }

    private function getToday(bool $returnStr = false)
    {
        if ($returnStr) {
            return date('Y/m/d');
        } else {
            return \DateTime::createFromFormat("Y/m/d", date('Y/m/d'));
        }
    }

    private function getDateTime(string $date, string $format)
    {
        return \DateTime::createFromFormat($format, $date);
    }
}