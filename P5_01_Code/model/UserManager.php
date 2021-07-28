<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\Database;

class UserManager extends AbstractManager
{
    public static $OBJECT_TYPE = 'Chemin\ArtSchools\Model\User';
    public static $TABLE_NAME = 'as_user';
    public static $TABLE_PK = 'id';
    public static $TABLE_CHAMPS ='id, pseudo, firstName, lastName, password, mail, school, schoolGroup, temporaryPassword, beingReset, 
        nbWarning, isBan, isAdmin, isModerator, 
        isActive, profileBannerInfo, profilePictureInfo, profileTextInfo';

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PUBLIC FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    public function add(User $user)
    {
        $this->sql(
            'INSERT INTO ' . static::$TABLE_NAME . ' (pseudo, firstName, lastName, mail, school, password, isAdmin, isModerator, isActive) 
            VALUES (:pseudo, :firstName, :lastName, :mail, :school, :password, :isAdmin, :isModerator, :isActive)', 
            [':pseudo' => $user->getPseudo(), ':firstName' => $user->getFirstName(), ':lastName' => $user->getLastName(), 
            ':mail' => $user->getMail(), ':school' => $user->getSchool(), ':password' => $user->getPassword(), 
            ':isAdmin' => intval($user->getIsAdmin()), ':isModerator' => intval($user->getIsModerator()), 
            ':isActive' => intval($user->getIsActive())]
        );
        return $this;
    }

    public function getUserByPseudo(string $pseudo)
    {
        if ($this->pseudoExists($pseudo)) {
            $q = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE pseudo = :pseudo', 
                [':pseudo' => $pseudo]
            );
            $result = $q->fetchObject(static::$OBJECT_TYPE);
            $q->closeCursor();
            return $result;
        } else {
            return false;
        }
    }

    public function getUserByMail(string $mail)
    {
        if ($this->mailExists($mail)) {
            $q = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE mail = :mail', 
                [':mail' => $mail]
            );
            $result = $q->fetchObject(static::$OBJECT_TYPE);
            $q->closeCursor();
            return $result;
        } else {
            return false;
        }
    }

    public function getUsersByGroup(string $group)
    {
        if (strlen($group) > 0) {
            $q = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE schoolGroup = :schoolGroup', 
                [':schoolGroup' => $group]
            );
            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
            $q->closeCursor();
            return $result;
        } else {
            return false;
        }
    }

    public function getUsersBySchool(string $school, string $grade = null, array $orderBy = [])
    {
        if (strlen($school) > 0) {
            if ($school === ALL_SCHOOL) {
                //every school
                $q = $this->getAllSchoolUsers($grade, $orderBy);
            } else {
                //only school $school
                $q = $this->getSchoolUsers($school, $grade, $orderBy);
            }
            $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
            $q->closeCursor();
            return $result;
        }
    }

    public function updateById(int $id, string $elem, $value, bool $isBool = false)
    {
        switch ($elem) {
            case 'grade' :
                $this->sql(
                    'UPDATE ' . static::$TABLE_NAME . ' 
                    SET isAdmin = :isAdmin, isModerator = :isModerator 
                    WHERE id = :id', 
                    [':isAdmin' => intval($value['isAdmin']), ':isModerator' => intval($value['isModerator']), ':id' => $id]
                );
                break;
            default :
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
        }
        return $this;
    }

    public function updateByPseudo(string $pseudo, string $elem, $value, bool $isBool = false)
    {
        switch ($elem) {
            case 'grade' :
                $this->sql(
                    'UPDATE ' . static::$TABLE_NAME . ' 
                    SET isAdmin = :isAdmin, isModerator = :isModerator 
                    WHERE pseudo = :pseudo', 
                    [':isAdmin' => intval($value['isAdmin']), ':isModerator' => intval($value['isModerator']), ':pseudo' => $pseudo]
                );
                break;
            default :
                if ($isBool) {
                    $this->sql(
                        'UPDATE ' . static::$TABLE_NAME . ' 
                        SET ' . $elem . ' = :value 
                        WHERE pseudo = :pseudo', 
                        [':value' => intval($value), ':pseudo' => $pseudo]
                    );
                } else {
                    $this->sql(
                        'UPDATE ' . static::$TABLE_NAME . ' 
                        SET ' . $elem . ' = :value 
                        WHERE pseudo = :pseudo', 
                        [':value' => $value, ':pseudo' => $pseudo]
                    );
                }
        }
        return $this;
    }

    public function pseudoExists(string $pseudo)
    {
        if (strlen($pseudo) > 0) {
            $q = $this->sql(
                'SELECT pseudo 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE pseudo = :pseudo', 
                [':pseudo' => $pseudo]
            );
            if ($q->fetch()) {
                $q->closeCursor();
                return true;
            } else {
                $q->closeCursor();
                return false;
            }
        } else {
            return false;
        }
    }

    public function mailExists(string $mail)
    {
        $mail = htmlspecialchars($mail);
        if (strlen($mail) > 0) {
            $q = $this->sql(
                'SELECT mail 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE mail = :mail', 
                [':mail' => $mail]
            );
            if ($q->fetch()) {
                $q->closeCursor();
                return true;
            } else {
                $q->closeCursor();
                return false;
            }
        } else {
            return false;
        }
    }

    public function canConnect(string $pseudo, string $password)
    {
        if (!empty($pseudo) && !empty($password) && $user = $this->getUserByPseudo($pseudo)) {
            if ($this->checkPassword($user, $password)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function checkWrongPasswordMessage($msg)
    {
        if (!empty($msg)) {
            switch ($msg) {
                case 1 :
                    return "Vous devez entrer deux mot de passe identiques";
                    break;
                case 2 :
                    return "Le nouveau mot de passe doit être différent de l'ancien";
                    break;
                default :
                    return "Il y a eu une erreur au niveau de mot de passe";
            }
        } else {
            return null;
        }
    }

    public function mailTemporaryPassword(User $user)
    {
        // set temporary password
        $temporaryPassword = password_hash($user->getPseudo() . time(), PASSWORD_DEFAULT);
        $this->updateById($user->getId(), 'temporaryPassword', $temporaryPassword)
            ->updateById($user->getId(), 'beingReset', true, true);
        // send recoveryMail
        $subject = 'Recuperation de mot de passe';
        $content = "Bonjour " . $user->getPseudo() . ", vous avez demande a reinitialiser votre mot de passe.<br><br>
			En suivant <a style='text-decoration: underline;' href='http://julienchemin.fr/projet5/index.php?action=resetPassword&key=" . $temporaryPassword . "&id=" . 
        $user->getId() . "'>ce lien</a> vous serez redirige vers une page pour modifier votre mot de passe.<br><br>
			Si le lien ne fonctionne pas, rendez vous a l'adresse suivante : <br>http://julienchemin.fr/projet5/index.php?action=resetPassword&key=" . $temporaryPassword . 
        "&id=" . $user->getId() . "<br><br>L'equipe d'ArtSchools vous remercie.";
        $content = wordwrap($content, 70, "\r\n");
        $headers = array('From' => '"Art-Schools"<artschoolsfr@gmail.com>', 
            'Content-Type' => 'text/html; charset=utf-8');
        ini_set("sendmail_from", "artschoolsfr@gmail.com");
        mail($user->getMail(), $subject, $content, $headers);
        return $this;
    }

    public function searchForKeyWord($word)
    {
        $result = [];
        $regex = "'.*" . $word . ".*'";
        $q = $this->sql(
            'SELECT ' . static::$TABLE_CHAMPS . ' 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE firstName REGEXP ' . $regex . ' OR lastName REGEXP ' . $regex
        );
        $result = $q->fetchAll(\PDO::FETCH_CLASS, static::$OBJECT_TYPE);
        return $result;
    }

    public function sortByGrade($users)
    {
        if (count($users) > 0) {
            $sortedUsers = ['admin' => [], 'moderator' => [], 'student' => []];
            foreach ($users as $user) {
                if ($user->getIsAdmin()) {
                    $sortedUsers['admin'][] = $this->toArray($user);
                } elseif ($user->getIsModerator()) {
                    $sortedUsers['moderator'][] = $this->toArray($user);
                } else {
                    $sortedUsers['student'][] = $this->toArray($user);
                }
            }
            return $sortedUsers;
        }
    }

    public function orderUsersBySchool(array $users, bool $sortByActiveInactive = false, bool $webmasterSide = false)
    {
        // One school -> $result [$user1, $user2, ...] / more than one school -> $result [$schoolName => [$user1, $user2, ...]]
        $result = [];
        if (count($users) > 0) {
            foreach ($users as $user) {
                if ($user->getSchool() !== ALL_SCHOOL) {
                    $result[$user->getSchool()][] = $user;
                }
            }
            //store countResult in var because $result will change value if his count is === 1, and still need this value after reassignment
            $countResult = count($result);
            if ($countResult === 1 && !$webmasterSide) {
                //all users are in the same school, don't need an array of array
                $result = $result[$users[0]->getSchool()];
                if ($sortByActiveInactive) {
                    $result = $this->sortByActiveInactive($result);
                }
            }
            if ($countResult > 1 && $sortByActiveInactive) {
                foreach ($result as $key => $value) {
                    $result[$key] = $this->sortByActiveInactive($value);
                }
            }
        }
        return $result;
    }

    public function countModerator(array $users)
    {
        if (count($users) > 0) {
            foreach ($users as $user) {
                //count nb moderator order by school
                if (!isset($arrNbModerator[$user->getSchool()])) {
                    $arrNbModerator[$user->getSchool()] = 0;
                }
                if ($user->getIsModerator()) {
                    $arrNbModerator[$user->getSchool()] += 1;
                }
            }
            if (count($arrNbModerator) === 1) {
                //all moderator are in the same school, don't need an array of array
                $arrNbModerator = $arrNbModerator[$users[0]->getSchool()];
            }
            return $arrNbModerator;
        } else {
            return 0;
        }
    }

    public function signUp(array $POST, SchoolManager $SchoolManager, HistoryManager $HistoryManager)
    {
        if (!empty($POST['affiliationCode'])) {
            //with affiliation code
            return $this->signUpWithAffiliation($POST, $SchoolManager, $HistoryManager);
        } else {
            //without affiliation code
            return $this->signUpWithoutAffiliation($POST);
        }
    }

    public function toggleIsActive($GET, SchoolManager $SchoolManager, HistoryManager $HistoryManager)
    {
        $user = $this->getUserByPseudo($GET['userName']);
        $school = $SchoolManager->getSchoolByName($GET['schoolName']);
        if ($user && $school) {
            if ($user->getIsActive()) {
                return $this->userToInactive($GET, $school, $user, $SchoolManager, $HistoryManager);
            } else {
                return $this->userToActive($GET, $school, $user, $SchoolManager, $HistoryManager);
            }
        } else {
            return false;
        }
    }

    public function deleteUser($GET, SchoolManager $SchoolManager)
    {
        $HistoryManager = new HistoryManager();
        $PostsManager = new PostsManager();
        $Cvmanager = new CvManager();
        $user = $this->getUserByPseudo($GET['userName']);
        $school = $SchoolManager->getSchoolByName($GET['schoolName']);
        $posts = $PostsManager->getPostsByAuthor($user->getId());
        
        if ($school && $user && !$user->getIsAdmin()) {
            //nb active account -1 if account is active and not moderator 
            if ($user->getIsActive() && !$user->getIsModerator()) {
                $SchoolManager->updateByName($GET['schoolName'], 'nbActiveAccount', $school->getNbActiveAccount() - 1);
            }
            //delete posts
            foreach ($posts as $post) {
                $PostsManager->deletePost($post->getId());
            }
            // delete cv
            $cvInfo = $Cvmanager->getCvInfo($user->getId());
            
            if ($cvInfo) {
                $Cvmanager->deleteCv($user->getId());
            }
            //delete PP, banner and then account
            $this->deleteFile($user->getProfileBanner())->deleteFile($user->getProfilePicture());
            $this->delete($user->getId());
            //add history entry
            $HistoryManager->addEntry(new HistoryEntry(
                ['idSchool' => $school->getId(), 
                'category' => 'account', 
                'entry' => $_SESSION['fullName'] . ' a supprimé le compte de ' . $user->getFirstName() . ' ' . $user->getLastName()])
            );
            return true;
        } else {
            return false;
        }
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PRIVATE FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    private function getAllSchoolUsers(string $grade = null, array $orderBy = [])
    {
        $clauseOrderBy = '';
        if (count($orderBy) > 0) {
            $clauseOrderBy = ' ORDER BY ' . $orderBy[0];
            for ($i = 1; $i<count($orderBy); $i++) {
                $clauseOrderBy .= ', ' . $orderBy[$i];
            }
        }
        
        if ($grade === 'admin') {
            //admins and moderator
            return $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE isAdmin = true OR isModerator = true' . 
                $clauseOrderBy
            );
        } elseif ($grade === 'user') {
            //users except admins and moderators
            return $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE isAdmin = false AND isModerator = false' . 
                $clauseOrderBy
            );
        } else {
            //all users
            return $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . 
                $clauseOrderBy
            );
        }
    }

    private function getSchoolUsers(string $school, string $grade = null, array $orderBy = [])
    {
        $clauseOrderBy = '';
        if (count($orderBy) > 0) {
            $clauseOrderBy = ' ORDER BY ' . $orderBy[0];
            for ($i = 1; $i<count($orderBy); $i++) {
                $clauseOrderBy .= ', ' . $orderBy[$i];
            }
        }
        
        if ($grade === 'admin') {
            //admins and moderators of school $school
            return $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE school = :school AND (isAdmin = true OR isModerator = true)' . 
                $clauseOrderBy, 
                [':school' => $school]
            );
        } elseif ($grade === 'user') {
            //users of school $school except admins and moderators
            return $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE school = :school AND isAdmin = false AND isModerator = false' . 
                $clauseOrderBy, 
                [':school' => $school]
            );
        } else {
            //all users of school $school
            return $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE school = :school' . 
                $clauseOrderBy, 
                [':school' => $school]
            );
        }
    }

    private function checkPassword(User $user, string $password)
    {
        if (strlen($password) > 0) {
            return password_verify($password, $user->getPassword());
        }
    }

    private function toArray($elem)
    {
        if (is_array($elem)) {
            $result = [];
            foreach ($elem as $user) {
                $result[] = $this->objectPostToArray($user);
            }
        } elseif (is_object($elem)) {
            $result = $this->objectPostToArray($elem);
        }
        return $result;
    }

    private function objectPostToArray(User $user)
    {
        $arr = ['id' => $user->getId(),
            'pseudo' => $user->getPseudo(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'school' => $user->getSchool(), 
            'schoolGroup' => $user->getSchoolGroup(), 
            'nbWarning' => $user->getNbWarning(), 
            'isBan' => $user->getIsBan(),  
            'isAdmin' => $user->getIsAdmin(), 
            'isModerator' => $user->getIsModerator(), 
            'isActive' => $user->getIsActive(), 
            'profilePicture' => $user->getProfilePicture()];
        return $arr;
    }

    private function sortByActiveInactive(array $users = null)
    {
        $result = ['active' => [], 'inactive' => []];
        if (!empty($users)) {
            foreach ($users as $user) {
                if ($user->getIsActive()) {
                    $result['active'][] = $user;
                } else {
                    $result['inactive'][] = $user;
                }
            }
        }
        return $result;
    }

    /*------------------------------ sign up ------------------------------*/
    private function signUpWithAffiliation(array $POST, SchoolManager $SchoolManager, HistoryManager $HistoryManager)
    {
        $affiliationCode = $SchoolManager->affiliationCodeExists($POST['affiliationCode']);
        if ($affiliationCode['exist']) {
                $schoolName = $affiliationCode['name'];
                $school = $SchoolManager->getSchoolByName($schoolName);
                //check nb active account
            if ($school->getNbActiveAccount() < $school->getNbEleve()) {
                $SchoolManager->updateByName($schoolName, 'nbActiveAccount', $school->getNbActiveAccount() + 1);
                $this->add(new User(
                    ['pseudo' => $POST['signUpPseudo'], 
                    'firstName' => $POST['signUpFirstName'], 
                    'lastName' => $POST['signUpLastName'], 
                    'mail' => $POST['signUpMail'], 
                    'password' => password_hash($POST['password'], PASSWORD_DEFAULT), 
                    'school' => $schoolName, 
                    'isActive' => true, 
                    'isAdmin' => false, 
                    'isModerator' => false])
                );
                //add history entry
                $HistoryManager->addEntry(new HistoryEntry(
                    ['idSchool' => $school->getId(), 
                    'category' => 'account', 
                    'entry' => $POST['signUpFirstName'] . ' ' . $POST['signUpLastName'] . ' a créé un compte affilié à votre établissement'])
                );
                return "Le compte à bien été créé, vous pouvez maintenant <a href='index.php?action=signIn'>vous connecter</a>";
            } else {
                return "Il est impossible de créer un compte pour le moment, le nombre maximum de compte utilisateur pour cet établissement a été atteint. Veuillez contacter un responsable de cet établissement pour plus d'informations";
            }
        } else {
            return "Le code d'affiliation est incorrect";
        }
    }

    private function signUpWithoutAffiliation(array $POST)
    {
        $this->add(new User(
            ['pseudo' => $POST['signUpPseudo'], 
            'firstName' => $POST['signUpFirstName'], 
            'lastName' => $POST['signUpLastName'],
            'mail' => $POST['signUpMail'], 
            'password' => password_hash($POST['password'], PASSWORD_DEFAULT), 
            'school' => NO_SCHOOL, 
            'isAdmin' => false, 
            'isModerator' => false, 
            'isActive' => false])
        );
        return "Le compte à bien été créé, vous pouvez maintenant <a href='index.php?action=signIn'>vous connecter</a>";
    }

    /*------------------------------ toggle user active / inactive ------------------------------*/
    private function userToActive($GET, School $school, User $user, SchoolManager $SchoolManager, HistoryManager $HistoryManager)
    {
        if ($school->getNbActiveAccount() < $school->getNbEleve()) {
            $this->updateByPseudo($GET['userName'], 'isActive', true, true);
            //nb active account + 1
            $SchoolManager->updateByName($GET['schoolName'], 'nbActiveAccount', $school->getNbActiveAccount() + 1);
            //add history entry
            $HistoryManager->addEntry(new HistoryEntry(
                ['idSchool' => $school->getId(), 
                'category' => 'account', 
                'entry' => $_SESSION['fullName'] . ' a activé le compte de ' . $user->getFirstName() . ' ' . $user->getLastName()])
            );
            return true;
        } else {
            return false;
        }
    }

    private function userToInactive($GET, School $school, User $user, SchoolManager $SchoolManager, HistoryManager $HistoryManager)
    {
        $this->updateByPseudo($GET['userName'], 'isActive', false, true);
        //nb active account - 1
        $SchoolManager->updateByName($GET['schoolName'], 'nbActiveAccount', $school->getNbActiveAccount() - 1);
        //add history entry
        $HistoryManager->addEntry(new HistoryEntry(
            ['idSchool' => $school->getId(), 
            'category' => 'account', 
            'entry' => $_SESSION['fullName'] . ' a désactivé le compte de ' . $user->getFirstName() . ' ' . $user->getLastName()])
        );
        return true;
    }
}
