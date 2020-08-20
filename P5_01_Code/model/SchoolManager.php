<?php

namespace Chemin\ArtSchool\Model;

class SchoolManager extends AbstractManager
{
    public static $OBJECT_TYPE = 'Chemin\ArtSchool\Model\School';
    public static $TABLE_NAME = 'as_school';
    public static $TABLE_PK = 'id';
    public static $TABLE_CHAMPS ='id, idAdmin, name, nameAdmin, mail, schoolGroups, code, nbEleve, 
        nbActiveAccount, DATE_FORMAT(dateInscription, "%d/%m/%Y") AS dateInscription, 
        logo, isActive, profileBannerInfo, profilePictureInfo, profileTextInfo';

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PUBLIC FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    public function add(School $school)
    {
        $this->sql(
            'INSERT INTO ' . static::$TABLE_NAME . ' (idAdmin, name, nameAdmin, mail, code, nbEleve, dateInscription, logo, isActive) 
            VALUE (:idAdmin, :name, :nameAdmin, :mail, :code, :nbEleve, NOW(), :logo, :isActive)', 
            [':idAdmin' => $school->getIdAdmin(), ':name' => $school->getName(), ':nameAdmin' => $school->getNameAdmin(), 
            ':mail' => $school->getMail(), ':code' => $school->getCode(), ':nbEleve' => $school->getNbEleve(), 
            ':logo' => $school->getLogo(), ':isActive' => intval($school->getIsActive())]
        );
        return $this;
    }

    public function getSchoolByName(string $name)
    {
        if ($this->nameExists($name) || $name === ALL_SCHOOL) {
            if ($name === ALL_SCHOOL) {
                // return all school
                $q = $this->sql(
                    'SELECT ' . static::$TABLE_CHAMPS . ' 
                    FROM ' . static::$TABLE_NAME . ' 
                    ORDER BY id'
                );
                $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
            } else {
                // return one school
                $q = $this->sql(
                    'SELECT ' . static::$TABLE_CHAMPS . ' 
                    FROM ' . static::$TABLE_NAME . ' 
                    WHERE name = :name', 
                    [':name' => $name]
                );
                $result = $q->fetchObject(static::$OBJECT_TYPE);
            }
            $q->closeCursor();
            return $result;
        } else {
            return false;
        }
    }

    public function updateById(int $id, string $elem, $value, bool $isBool = false)
    {
        if ($isBool) {
            $this->sql(
                'UPDATE ' . static::$TABLE_NAME . ' 
                SET ' . $elem . ' = :value 
                WHERE id = :id', 
                [':value' => intval($value), ':id' => $id]
            );
        } else {
            $this->sql(
                'UPDATE ' . static::$TABLE_NAME . ' 
                SET ' . $elem . ' = :value 
                WHERE id = :id', 
                [':value' => $value, ':id' => $id]
            );
        }
        return $this;
    }

    public function updateByName(string $name, string $elem, $value, bool $isBool = false)
    {
        if ($isBool) {
            $this->sql(
                'UPDATE ' . static::$TABLE_NAME . ' 
                SET ' . $elem . ' = :value 
                WHERE name = :name', 
                [':value' => intval($value), ':name' => $name]
            );
        } else {
            $this->sql(
                'UPDATE ' . static::$TABLE_NAME . ' 
                SET ' . $elem . ' = :value 
                WHERE name = :name', 
                [':value' => $value, ':name' => $name]
            );
        }
        return $this;
    }

    public function nameExists(string $name)
    {
        if (strlen($name) > 0) {
            $q = $this->sql(
                'SELECT name 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE name = :name', 
                [':name' => $name]
            );
            if ($q->fetch()) {
                   $q->closeCursor();
                   return true;
            } else {
                $q->closeCursor();
                return false;
            }
        } else {
            return  false;
        }
    }

    public function affiliationCodeExists(string $code)
    {
        $q = $this->sql(
            'SELECT name 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE code = :code', 
            [':code' => $code]
        );
        if ($result = $q->fetch()) {
            $q->closeCursor();
            return ['exist' => true, 'name' => $result['name']];
        } else {
            $q->closeCursor();
            return ['exist' => false, 'name' => null];
        }
    }

    public function searchForKeyWord($word)
    {
        $result = [];
        $regex = "'.*" . $word . ".*'";
        $q = $this->sql(
            'SELECT ' . static::$TABLE_CHAMPS . ' 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE name REGEXP ' . $regex
        );
        $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
        return $result;
    }

    public function canAddSchool(array $POST, UserManager $UserManager)
    {
        //verify script insertion
        if (!$this->checkForScriptInsertion($POST)) {
            return ['succes' => false, 'message' => "Certaines informations sont incorrects"];
        }
        //verify if user name is already use
        if ($UserManager->nameExists($POST['adminName'])) {
            return ['succes' => false, 'message' => "Ce nom d'utilisateur existe déja"];
        }
        //verify if user mail is already use
        if ($UserManager->mailExists($POST['adminMail'])) {
            return ['succes' => false, 'message' => "Il existe déja un compte associé a cette adresse mail"];
        }
        //verify if school name is already use
        if ($this->nameExists($POST['schoolName']) || $POST['schoolName'] === ALL_SCHOOL) {
            return ['succes' => false, 'message' => "Ce nom d'établissement existe déja"];
        }
        //verify if school code is already use
        $codeExist = $this->affiliationCodeExists($POST['schoolCode']);
        if ($codeExist['exist']) {
            return ['succes' => false, 'message' => "Ce code d'affiliation ne peut pas être utilisé"];
        }
        //verify 'nbEleve'
        if (intval($POST['schoolNbEleve']) <= 0) {
            return ['succes' => false, 'message' => "Le nombre d'élève doit être supérieur a 0"];
        }
        return ['succes' => true, 'message' => null];
    }

    public function addSchool(array $POST, UserManager $UserManager, HistoryManager $HistoryManager)
    {
        if ($POST['adminPassword'] === $POST['adminConfirmPassword']) {
            // add school administrator
            $UserManager->add(new User(
                ["name" => $POST['adminName'], 
                "mail" => $POST['adminMail'], 
                "password" => password_hash($POST['adminPassword'], PASSWORD_DEFAULT), 
                "school" => $POST['schoolName'], 
                "isAdmin" => true, 
                "isModerator" => false])
            );
            // create new school
            $POST['schoolDuration'] === '0' ? $isActive = false : $isActive = true;
            $POST['schoolDuration'] === '0' ? $nbEleve = 0 : $nbEleve = $POST['schoolNbEleve'];
            $this->add(new School(
                ['idAdmin' => $this->getLastInsertId(), 
                'name' => $POST['schoolName'], 
                'nameAdmin' => $POST['adminName'], 
                'mail' => $POST['schoolMail'], 
                'code' => $POST['schoolCode'], 
                'nbEleve' => $nbEleve,  
                'logo' => 'public/images/question-mark.png', 
                'isActive' => $isActive])
            );
            // first history entry
            if ($POST['schoolDuration'] === '0') {
                $entry = 'Bienvenue sur ArtSchool !';
            } else {
                $entry = 'Bienvenue sur ArtSchool ! Vous vous êtes inscrit pour une période de ' . $POST['schoolDuration'] . 
                    ' mois, avec ' . $nbEleve . ' compte(s) affiliés a votre établissement';
            }
            $HistoryManager->addEntry(new HistoryEntry(
                ['idSchool' => $this->getLastInsertId(), 
                'category' => 'activityPeriod', 
                'entry' => $entry])
            );
            return "L'établissement a bien été ajouté";
        } else {
            return "Les mots de passe ne correspondent pas";
        }
    }

    public function editSchool(array $POST, UserManager $UserManager, HistoryManager $HistoryManager)
    {
        switch ($POST['elem']) {
            case 'name' :
                return $this->editSchoolName($POST, $UserManager, $HistoryManager);
                break;
            case 'admin' :
                return $this->editSchoolAdmin($POST, $UserManager, $HistoryManager);
                break;
            case 'mail' :
                return $this->editSchoolMail($POST, $HistoryManager);
                break;
            case 'code' :
                return $this->editSchoolCode($POST, $HistoryManager);
                break;
            case 'nbEleve' :
                return $this->editSchoolNbEleve($POST, $HistoryManager);
                break;
            case 'logo' :
                return $this->editSchoolLogo($POST, $HistoryManager);
                break;
            case 'toActive' :
                return $this->editSchoolToActive($POST);
                break;
            case 'toInactive' :
                return $this->editSchoolToInactive($POST, $UserManager);
                break;
            default :
                return "Les informations renseignées sont incorrectes";
        }
    }

    public function schoolToActive($schoolIdOrStr, UserManager $userManager, int $nbEleve = 0)
    {
        if (is_int($schoolIdOrStr) && $schoolIdOrStr > 0) {
            $school = $this->getOneById($schoolIdOrStr);
        } elseif (is_string($schoolIdOrStr) && strlen($schoolIdOrStr) > 0) {
            $school = $this->getSchoolByName($schoolIdOrStr);
        }
        if ($school) {
            $users = $userManager->getUsersBySchool($school->getName(), 'admin');
            foreach ($users as  $user) {
                $userManager->updateById($user->getId(), 'isActive', true, true);
            }
            $this->updateById($school->getId(), 'isActive', true, true)
                ->updateById($school->getId(), 'nbEleve', $nbEleve);
            //add history entry
            $HistoryManager = new HistoryManager();
            $HistoryManager->addEntry(new HistoryEntry(
                ['idSchool' => $school->getId(), 
                'category' => 'activityPeriod', 
                'entry' => 'L\'établissement a été activé'])
            );
        } else {
            $this->incorrectInformation();
        }
    }

    public function schoolToInactive($schoolIdOrStr, UserManager $userManager)
    {
        if (is_int($schoolIdOrStr) && $schoolIdOrStr > 0) {
            $school = $this->getOneById($schoolIdOrStr);
        } elseif (is_string($schoolIdOrStr) && strlen($schoolIdOrStr) > 0) {
            $school = $this->getSchoolByName($schoolIdOrStr);
        }
        if ($school) {
            $users = $userManager->getUsersBySchool($school->getName());
            foreach ($users as  $user) {
                $userManager->updateById($user->getId(), 'isActive', false, true);
            }
            $this->updateById($school->getId(), 'isActive', false, true)
                ->updateById($school->getId(), 'nbEleve', 0)
                ->updateById($school->getId(), 'nbActiveAccount', 0);
            //add history entry
            $HistoryManager = new HistoryManager();
            $HistoryManager->addEntry(new HistoryEntry(
                ['idSchool' => $school->getId(), 
                'category' => 'activityPeriod', 
                'entry' => 'L\'établissement n\'est plus actif'])
            );
        } else {
            $this->incorrectInformation();
        }
    }

    public function canAddModerator(array $POST, UserManager $UserManager)
    {
        if (!$this->checkForScriptInsertion($POST)) {
            return ['canAdd' => false, 'message' => "Les informations renseignées sont incorrectes"];
        }
        //verify if school exist
        if (!$school = $this->getSchoolByName($POST['schoolName'])) {
            return ['canAdd' => false, 'message' => "Cet établissement n'existe pas"];
        }
        //verify if school is active
        if (!$school->getIsActive()) {
            return ['canAdd' => false, 'message' => "Vous ne pouvez pas ajouter de modérateur, cet établissement n'est pas actif"];
        }
        //verify if user name is already use
        if ($UserManager->nameExists($POST['moderatorName'])) {
            return ['canAdd' => false, 'message' => "Ce nom d'utilisateur existe déja"];
        }
        //verify if user mail is already use
        if ($UserManager->mailExists($POST['moderatorMail'])) {
            return ['canAdd' => false, 'message' => "Il existe déja un compte associé a cette adresse mail"];
        }
        return ['canAdd' => true, 'message' => null];
    }

    public function addModerator(array $POST, UserManager $UserManager, HistoryManager $HistoryManager)
    {
        if ($POST['moderatorPassword'] === $POST['moderatorConfirmPassword']) {
            $school = $this->getSchoolByName($POST['schoolName']);
            $UserManager->add(new User(
                ["name" => $POST['moderatorName'], 
                "mail" => $POST['moderatorMail'], 
                "password" => password_hash($POST['moderatorPassword'], PASSWORD_DEFAULT), 
                "school" => $POST['schoolName'], 
                "isAdmin" => false, 
                "isModerator" => true])
            );
            //add history entry
            $HistoryManager->addEntry(new HistoryEntry(
                ['idSchool' => $school->getId(), 
                'category' => 'account', 
                'entry' => $_SESSION['pseudo'] . ' a créé un compte modérateur : ' . $POST['moderatorName']])
            );
            return "Le modérateur a bien été ajouté";
        } else {
            return "Les mots de passe ne correspondent pas";
        }
    }

    public function createGroup($GET)
    {
        //verify if new group name already exist
        if ($this->checkForScriptInsertion([$GET['group']]) && strlen($GET['group']) <= 30 && $school = $this->getSchoolByName($GET['schoolName'])) {
            if ($school->getSchoolGroups() === null) {
                $listSchoolGroups = ',' . $GET['group'];
                $this->updateById($school->getId(), 'schoolGroups', $listSchoolGroups);
                return true;
            } elseif (!in_array($GET['group'], $school->getListSchoolGroups())) {
                $listSchoolGroups = $school->getSchoolGroups() . ',' . $GET['group'];
                $this->updateById($school->getId(), 'schoolGroups', $listSchoolGroups);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function setGroup($GET, User $user, UserManager $UserManager)
    {
        $school = $this->getSchoolByName($user->getSchool());
        if ($school && (($school->getSchoolGroups() !== null && in_array($GET['group'], $school->getListSchoolGroups())) || $GET['group'] === 'Aucun groupe')) {
            if ($GET['group'] === 'Aucun groupe') {
                $GET['group'] = null;
            }
            $UserManager->updateById($user->getId(), 'schoolGroup', $GET['group']);
            return true;
        } else {
            return false;
        }
    }

    public function deleteGroup($GET, UserManager $UserManager)
    {
        if ($this->checkForScriptInsertion([$GET['group']]) && $school = $this->getSchoolByName($GET['schoolName'])) {
            //delete user's group
            $users = $UserManager->getUsersByGroup($GET['group']);
            foreach ($users as $user) {
                $UserManager->updateById($user->getId(), "schoolGroup", null);
            }
            //delete group
            $listSchoolGroups = $school->getListSchoolGroups();
            unset($listSchoolGroups[array_search($GET['group'], $listSchoolGroups)]);
            if (!empty($listSchoolGroups)) {
                $strListSchoolGroups = ',' . implode(',', $listSchoolGroups);
            } else {
                $strListSchoolGroups = null;
            }
            $this->updateById($school->getId(), 'schoolGroups', $strListSchoolGroups);
            return true;
        } else {
            return false;
        }
    }

    public function editGrade($GET, UserManager $UserManager, HistoryManager $HistoryManager)
    {
        $GET['toAdmin'] === 'true' ? $toAdmin = true : $toAdmin = false;
        $GET['toModerator'] === 'true' ? $toModerator = true : $toModerator = false;
        $school = $this->getSchoolByName($GET['schoolName']);
        $user = $UserManager->getUserByName($GET['userName']);
        if ($user && $school) {
            if ($toModerator) {
                if (!$user->getIsAdmin()) {
                    //normal user to moderator, nb active account -1
                    $nbActiveAccount = $school->getNbActiveAccount() - 1;
                    $this->updateByName($school->getName(), 'nbActiveAccount', $nbActiveAccount);
                }
            } elseif (!$toModerator && !$toAdmin) {
                //moderator to normal user, nb active account +1
                $nbActiveAccount = $school->getNbActiveAccount();
                if ($nbActiveAccount < $school->getNbEleve()) {
                    $this->updateByName($school->getName(), 'nbActiveAccount', $nbActiveAccount + 1);
                } else {
                    throw new \Exception("Il est impossible d'effectuer cette action, vous avez atteint le nombre maximum de compte utilisateur actif");
                }
            }
            $UserManager->updateByName($user->getName(), 'grade', ['isAdmin' => $toAdmin, 'isModerator' => $toModerator]);
            //add history entry
            if ($toAdmin) {
                $grade = "administrateur";
            } elseif ($toModerator) {
                $grade = "modérateur";
            } else {
                $grade = "utilisateur";
            }
            $HistoryManager->addEntry(new HistoryEntry(
                ['idSchool' => $school->getId(), 
                'category' => 'account', 
                'entry' => $_SESSION['pseudo'] . ' a passé ' . $user->getName() . ' au grade : ' . $grade])
            );
        } else {
            $this->incorrectInformation();
        }
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PRIVATE FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    private function editSchoolName(array $POST, UserManager $UserManager, HistoryManager $HistoryManager)
    {
        if ($POST['editName'] !== ALL_SCHOOL && !$this->nameExists($POST['editName'])) {
            $users = $UserManager->getUsersBySchool($POST['schoolName']);
            foreach ($users as $user) {
                $UserManager->updateById($user->getId(), 'school', $POST['editName']);
            }
            $this->updateByName($POST['schoolName'], 'name', $POST['editName']);
            if ($_SESSION['school'] !== ALL_SCHOOL) {
                $_SESSION['school'] = $POST['editName'];
            }
            //add history entry
            $school = $this->getSchoolByName($POST['editName']);
            $HistoryManager->addEntry(new HistoryEntry(
                ['idSchool' => $school->getId(), 
                'category' => 'profil', 
                'entry' => $_SESSION['pseudo'] . ' a modifié le nom de votre établissement en : ' . $POST['editName']])
            );
            return "Le nom de l'établissement a été modifié";
        } else {
            return "Ce nom est déja utilisé";
        }
    }

    private function editSchoolAdmin(array $POST, UserManager $UserManager, HistoryManager $HistoryManager)
    {
        if ($newAdmin = $UserManager->getUserByName($POST['editAdmin'])) {
            $school = $this->getSchoolByName($POST['schoolName']);
            if ($school && $school->getName() === $newAdmin->getSchool()) {
                if ($newAdmin->getIsActive()) {
                    $UserManager->updateByName($newAdmin->getName(), 'grade', ['isAdmin' => true, 'isModerator' => false]);
                    $this->updateByName($POST['schoolName'], 'idAdmin', $newAdmin->getId())
                        ->updateByName($POST['schoolName'], 'nameAdmin', $newAdmin->getName());
                    //add history entry
                    $HistoryManager->addEntry(new HistoryEntry(
                        ['idSchool' => $school->getId(), 
                        'category' => 'profil', 
                        'entry' => $_SESSION['pseudo'] . ' a remplacé l\'administrateur principal par : ' . $newAdmin->getName()])
                    );
                    return "L'administrateur de l'établissement a été modifié";
                } else {
                    return "Ce compte est inactif";
                }
            } else {
                return "Cette personne ne fait pas parti de cet établissement";
            }
        } else {
            return "Ce nom d'utilisateur ne correspond à aucun compte éxistant";
        }
    }

    private function editSchoolMail(array $POST, HistoryManager $HistoryManager)
    {
        if ($school = $this->getSchoolByName($POST['schoolName'])) {
            $this->updateByName($POST['schoolName'], 'mail', $POST['editMail']);
            //add history entry
            $HistoryManager->addEntry(new HistoryEntry(
                ['idSchool' => $school->getId(),
                'category' => 'profil',
                'entry' => $_SESSION['pseudo'] . ' a modifié l\'adresse mail en : ' . $POST['editCode']])
            );
            return "L'adresse mail a été modifié";
        } else {
            $this->incorrectInformation();
        }
    }

    private function editSchoolCode(array $POST, HistoryManager $HistoryManager)
    {
        if (!$this->affiliationCodeExists($POST['editCode'])["exist"] && $school = $this->getSchoolByName($POST['schoolName'])) {
            $this->updateByName($POST['schoolName'], 'code', $POST['editCode']);
            //add history entry
            $HistoryManager->addEntry(new HistoryEntry(
                ['idSchool' => $school->getId(),
                'category' => 'profil',
                'entry' => $_SESSION['pseudo'] . ' a modifié le code d\'affiliation en : ' . $POST['editCode']])
            );
            return "Le code d'affiliation a été modifié";
        } else {
            return "Veuillez choisir un autre code";
        }
    }

    private function editSchoolNbEleve(array $POST, HistoryManager $HistoryManager)
    {
        if ($_SESSION['school'] === ALL_SCHOOL && $school = $this->getSchoolByName($POST['schoolName'])) {
            if (intval($school->getNbActiveAccount()) <= $POST['editNbEleve']) {
                $this->updateByName($POST['schoolName'], 'nbEleve', intval($POST['editNbEleve']));
                //add history entry
                $HistoryManager->addEntry(new HistoryEntry(
                    ['idSchool' => $school->getId(), 
                    'category' => 'profil', 
                    'entry' => 'Le nombre maximum de compte affilié à votre établissement est passé à ' . $POST['editNbEleve']])
                );
                return "Le nombre d'élèves a été modifié";
            } else {
                return "Le nombre de compte(s) actif(s) pour cette établissement est supérieur au nombre de compte(s) disponible(s) que vous annoncez";
            }
        } else {
            $this->incorrectInformation();
        }
    }

    private function editSchoolLogo(array $POST, HistoryManager $HistoryManager)
    {
        if ($school = $this->getSchoolByName($POST['schoolName'])) {
            if (!empty($POST['editLogo'])) {
                $this->updateByName($POST['schoolName'], 'logo', $POST['editLogo']);
                //add history entry
                $HistoryManager->addEntry(new HistoryEntry(
                    ['idSchool' => $school->getId(), 
                    'category' => 'profil', 
                    'entry' => $_SESSION['pseudo'] . ' a modifié le logo de l\'établissement'])
                );
                return "Le logo de votre établissement a été modifié";
            } elseif (!empty($_FILES['uploadLogo'])) {
                $arrAcceptedExtention = array("jpeg", "jpg", "png", "gif");
                require 'view/upload.php';
                if (!empty($final_path)) {
                    $this->updateByName($school->getName(), 'logo', $final_path);
                    //add history entry
                    $HistoryManager->addEntry(new HistoryEntry(
                        ['idSchool' => $school->getId(), 
                        'category' => 'profil', 
                        'entry' => $_SESSION['pseudo'] . ' a modifié le logo de l\'établissement'])
                    );
                }
                header('Location: indexAdmin.php?action=moderatSchool');
            } else {
                header('Location: indexAdmin.php?action=moderatSchool');
            }
        } else {
            $this->incorrectInformation();
        }
    }

    private function editSchoolToActive(array $POST)
    {
        if ($_SESSION['school'] === ALL_SCHOOL && $school = $this->getSchoolByName($POST['schoolName'])) {
            if (intval($POST['editToActiveDuration']) > 0) {
                $ContractManager = new ContractManager('school',$this);
                $ContractManager->extendContract($school, intval($POST['editToActiveDuration']), intval($POST['editToActive']));
                return "L'établissement a été activé, avec " . $POST['editToActive'] . " compte affilié maximum, 
                    pour une période de " . $POST['editToActiveDuration'] . " mois";
            } else {
                throw new \Exception("La période d'extension de contrat doit être supérieur a 0");
            }
        } else {
            throw new \Exception("Vous ne pouvez pas accéder a cette page");
        }
    }

    private function editSchoolToInactive(array $POST, UserManager $UserManager)
    {
        if ($_SESSION['school'] === ALL_SCHOOL && $school = $this->getSchoolByName($POST['schoolName'])) {
            $this->schoolToInactive($POST['schoolName'], $UserManager);
            $ContractManager = new ContractManager('school',$this);
            $ContractManager->deleteRemind($school);
            return "L'établissement a été désactivé";
        } else {
            throw new \Exception("Vous ne pouvez pas accéder a cette page");
        }
    }
}
