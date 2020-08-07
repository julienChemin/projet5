<?php
namespace Chemin\ArtSchool\Model;
use Chemin\ArtSchool\Model\Database;

class UserManager extends AbstractManager
{
    public static $OBJECT_TYPE = 'Chemin\ArtSchool\Model\User';
    public static $TABLE_NAME = 'as_user';
    public static $TABLE_PK = 'id';
    public static $TABLE_CHAMPS ='id, name, password, mail, school, schoolGroup, temporaryPassword, beingReset, 
        nbWarning, isBan, isAdmin, isModerator, 
        isActive, profileBannerInfo, profilePictureInfo, profileTextInfo';

    public function add(User $user)
    {
        $this->sql(
            'INSERT INTO ' . static::$TABLE_NAME . ' (name, mail, school, password, isAdmin, isModerator, isActive) 
            VALUES (:name, :mail, :school, :password, :isAdmin, :isModerator, :isActive)', 
            [':name' => $user->getName(), ':mail' => $user->getMail(), ':school' => $user->getSchool(), 
            ':password' => $user->getPassword(), ':isAdmin' => intval($user->getIsAdmin()), ':isModerator' => intval($user->getIsModerator()), 
            ':isActive' => intval($user->getIsActive())]
        );
        return $this;
    }

    public function getUserByName(string $name)
    {
        if (strlen($name) > 0) {
            $q = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE name = :name', 
                [':name' => $name]
            );
            $result = $q->fetchObject(static::$OBJECT_TYPE);
            $q->closeCursor();
            return $result;
        }
    }

    public function getUserByMail(string $mail)
    {
        if (strlen($mail) > 0) {
            $q = $this->sql(
                'SELECT ' . static::$TABLE_CHAMPS . ' 
                FROM ' . static::$TABLE_NAME . ' 
                WHERE mail = :mail', 
                [':mail' => $mail]
            );
            $result = $q->fetchObject(static::$OBJECT_TYPE);
            $q->closeCursor();
            return $result;
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
        }
    }

    public function getUsersBySchool(string $school, string $grade = null)
    {
        if (strlen($school) > 0) {
            if ($school === ALL_SCHOOL) {
                //every school
                if ($grade === 'admin') {
                    //admins and moderator
                    $q = $this->sql(
                        'SELECT ' . static::$TABLE_CHAMPS . ' 
                        FROM ' . static::$TABLE_NAME . ' 
                        WHERE isAdmin = true OR isModerator = true'
                    );
                } elseif ($grade === 'user') {
                    //users except admins and moderators
                    $q = $this->sql(
                        'SELECT ' . static::$TABLE_CHAMPS . ' 
                        FROM ' . static::$TABLE_NAME . ' 
                        WHERE isAdmin = false AND isModerator = false'
                    );
                } else {
                    //all users
                    $q = $this->sql(
                        'SELECT ' . static::$TABLE_CHAMPS . ' 
                        FROM ' . static::$TABLE_NAME
                    );
                }
            } else {
                //only school $school
                if ($grade === 'admin') {
                    //admins and moderators of school $school
                    $q = $this->sql(
                        'SELECT ' . static::$TABLE_CHAMPS . ' 
                        FROM ' . static::$TABLE_NAME . ' 
                        WHERE school = :school AND (isAdmin = true OR isModerator = true)', 
                        [':school' => $school]
                    );
                } elseif ($grade === 'user') {
                    //users of school $school except admins and moderators
                    $q = $this->sql(
                        'SELECT ' . static::$TABLE_CHAMPS . ' 
                        FROM ' . static::$TABLE_NAME . ' 
                        WHERE school = :school AND isAdmin = false AND isModerator = false', 
                        [':school' => $school]
                    );
                } else {
                    //all users of school $school
                    $q = $this->sql(
                        'SELECT ' . static::$TABLE_CHAMPS . ' 
                        FROM ' . static::$TABLE_NAME . ' 
                        WHERE school = :school', 
                        [':school' => $school]
                    );
                }
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

    public function updateByName(string $name, string $elem, $value, bool $isBool = false)
    {
        switch ($elem) {
        case 'grade' :
            $this->sql(
                'UPDATE ' . static::$TABLE_NAME . ' 
                SET isAdmin = :isAdmin, isModerator = :isModerator 
                WHERE name = :name', 
                [':isAdmin' => intval($value['isAdmin']), ':isModerator' => intval($value['isModerator']), ':name' => $name]
            );
            break;
        default :
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
        }
    }

    public function mailExists(string $mail)
    {
        $mail = htmlspecialchars($mail);
        if (strlen($mail) > 0) {
            $q = $this->sql(
                'SELECT name 
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
        }
    }

    public function checkPassword(User $user, string $password)
    {
        if (strlen($password) > 0) {
            return password_verify($password, $user->getPassword());
        }
    }

    public function canConnect(string $userName, string $password)
    {
        if (!empty($userName) && !empty($password)) {
            if ($this->nameExists($userName)) {
                $user = $this->getUserByName($userName);
                if ($this->checkPassword($user, $password)) {
                    return true;
                } else {
                    return false;
                }
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
            return false;
        }
    }

    public function mailTemporaryPassword(User $user)
    {
        $temporaryPassword = password_hash($user->getName() . time(), PASSWORD_DEFAULT);
        $this->updateById($user->getId(), 'temporaryPassword', $temporaryPassword);

        $subject = 'Recuperation de mot de passe';
        $content = "Bonjour " . $user->getName() . ", vous avez demande a reinitialiser votre mot de passe.<br><br>
			En suivant <a style='text-decoration: underline;' href='http://julienchemin.fr/projet5/index.php?action=resetPassword&key=" . $temporaryPassword . "&id=" . 
        $user->getId() . "'>ce lien</a> vous serez redirige vers une page pour modifier votre mot de passe.<br><br>
			Si le lien ne fonctionne pas, rendez vous a l'adresse suivante : <br>http://julienchemin.fr/projet5/index.php?action=resetPassword&key=" . $temporaryPassword . 
        "&id=" . $user->getId() . "<br><br>L'equipe d'ArtSchool vous remercie.";
        $content = wordwrap($content, 70, "\r\n");
        $headers = array('From' => '"Art-School"<julchemin@orange.fr>', 
            'Content-Type' => 'text/html; charset=utf-8');
        ini_set("sendmail_from", "julchemin@orange.fr");
        mail($user->getMail(), $subject, $content, $headers);
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

    public function toArray($elem)
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

    public function objectPostToArray(User $user)
    {
        $arr = ['id' => $user->getId(),
            'name' => $user->getName(),
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

    public function moderatAdminSorting($users)
    {
        if ($_SESSION['school'] === ALL_SCHOOL) {
            //order users by school
            $arrUsersBySchool = [];
            foreach ($users as $user) {
                if ($user->getSchool() !== ALL_SCHOOL) {
                    $arrUsersBySchool[$user->getSchool()][] = $user;
                    //count nb moderator order by school
                    if (!isset($arrNbModerator[$user->getSchool()])) {
                        $arrNbModerator[$user->getSchool()] = 0;
                    }
                    if ($user->getIsModerator()) {
                        $arrNbModerator[$user->getSchool()] += 1;
                    }
                }
            }
            return ['users' => $arrUsersBySchool, 'nbModerator' => $arrNbModerator];
        } elseif ($_SESSION['grade'] === ADMIN) {
            $nbModerator = 0;
            foreach ($users as $user) {
                if ($user->getIsModerator()) {
                    $nbModerator++;
                }
            }
            return ['users' => $users, 'nbModerator' => $nbModerator];
        }
    }

    public function moderatUsersSorting($users, $schools)
    {
        if ($_SESSION['school'] === ALL_SCHOOL) {
            //order users by school
            $arrUsersBySchool = [];
            $arrIsActive = [];
            foreach ($users as $user) {
                if ($user->getSchool() !== ALL_SCHOOL) {
                    if ($user->getIsActive()) {
                        $arrUsersBySchool[$user->getSchool()]['active'][] = $user;
                    } else {
                        $arrUsersBySchool[$user->getSchool()]['inactive'][] = $user;
                    }
                }
            }
            foreach ($schools as $school) {
                if (!empty($arrUsersBySchool[$school->getName()]['active'])) {
                    $arrIsActive[$school->getName()]['active'] = true;
                } else {
                    $arrIsActive[$school->getName()]['active'] = false;
                }

                if (!empty($arrUsersBySchool[$school->getName()]['inactive'])) {
                    $arrIsActive[$school->getName()]['inactive'] = true;
                } else {
                    $arrIsActive[$school->getName()]['inactive'] = false;
                }
            }
            return ['users' => $arrUsersBySchool, 'isActive' => $arrIsActive];
        } else {
            $arrIsActive = [];
            $nbAccount = count($users);
            if ($schools->getNbActiveAccount() > 0) {
                $arrIsActive['active'] = true;
            } else {
                $arrIsActive['active'] = false;
            }

            if ($nbAccount - $schools->getNbActiveAccount() > 0) {
                $arrIsActive['inactive'] = true;
            } else {
                $arrIsActive['inactive'] = false;
            }
            return ['users' => $users, 'isActive' => $arrIsActive];
        }
    }

    public function signUp($POST, SchoolManager $SchoolManager, HistoryManager $HistoryManager)
    {
        if ($this->checkForScriptInsertion($_POST)) {
            if ($POST['confirmPassword'] === $POST['password']) {
                if (!$this->nameExists($POST['pseudo'])) {
                    if (!$this->mailExists($POST['mail'])) {
                        if (!empty($POST['affiliationCode'])) {
                            //with affiliation code
                            $result = $SchoolManager->affiliationCodeExists($POST['affiliationCode']);
                            if ($result['exist']) {
                                   $schoolName = $result['name'];
                                   $school = $SchoolManager->getSchoolByName($schoolName);
                                   //check nb active account
                                if ($school->getNbActiveAccount() < $school->getNbEleve()) {
                                    $SchoolManager->updateByName($schoolName, 'nbActiveAccount', $school->getNbActiveAccount() + 1);
                                    $this->add(new User(
                                        ['name' => $POST['pseudo'], 
                                        'mail' => $POST['mail'], 
                                        'password' => password_hash($POST['password'], PASSWORD_DEFAULT), 
                                        'school' => $schoolName, 
                                        'isAdmin' => false, 
                                        'isModerator' => false])
                                    );
                                    //add history entry
                                    $HistoryManager->addEntry(new HistoryEntry(
                                        ['idSchool' => $school->getId(), 
                                        'category' => 'account', 
                                        'entry' => $POST['pseudo'] . ' a créé un compte affilié à votre établissement'])
                                    );
                                    return $message = "Le compte à bien été créé, vous pouvez maintenant <a href='index.php?action=signIn'>vous connecter</a>";
                                } else {
                                    return $message = "Il est impossible de créer un compte pour le moment, le nombre maximum de compte utilisateur pour cet établissement a été atteint. Veuillez contacter un responsable de cet établissement pour plus d'informations";
                                }
                            } else {
                                return $message = "Le code d'affiliation est incorrect";
                            }
                        } else {
                            //without affiliation code
                            $this->add(new User(
                                ['name' => $POST['pseudo'], 
                                'mail' => $POST['mail'], 
                                'password' => password_hash($POST['password'], PASSWORD_DEFAULT), 
                                'school' => NO_SCHOOL, 
                                'isAdmin' => false, 
                                'isModerator' => false, 
                                'isActive' => false])
                            );
                            return $message = "Le compte à bien été créé, vous pouvez maintenant <a href='index.php?action=signIn'>vous connecter</a>";
                        }
                    } else {
                        return $message = "Cette adresse mail est déja lié a un compte";
                    }
                } else {
                    return $message = "Ce nom d'utilisateur est déja utilisé";
                }
            } else {
                return $message = "Vous devez entrer deux mot de passe identiques";
            }
        } else {
            return $message = "Les informations renseignées sont incorrectes";
        }
    }

    public function toggleIsActive($GET, SchoolManager $SchoolManager, HistoryManager $HistoryManager)
    {
        $user = $this->getUserByName($GET['userName']);
        $school = $SchoolManager->getSchoolByName($GET['schoolName']);
        if ($user->getIsActive()) {
            $this->updateByName($GET['userName'], 'isActive', false, true);
            //nb active account - 1
            $SchoolManager->updateByName($GET['schoolName'], 'nbActiveAccount', $school->getNbActiveAccount() - 1);
            //add history entry
            $HistoryManager->addEntry(new HistoryEntry(
                ['idSchool' => $school->getId(), 
                'category' => 'account', 
                'entry' => $_SESSION['pseudo'] . ' a désactivé le compte de ' . $user->getName()])
            );
            return true;
        } else {
            //account is inactive
            if ($school->getNbActiveAccount() < $school->getNbEleve()) {
                $this->updateByName($GET['userName'], 'isActive', true, true);
                //nb active account + 1
                $SchoolManager->updateByName($GET['schoolName'], 'nbActiveAccount', $school->getNbActiveAccount() + 1);
                //add history entry
                $HistoryManager->addEntry(new HistoryEntry(
                    ['idSchool' => $school->getId(), 
                    'category' => 'account', 
                    'entry' => $_SESSION['pseudo'] . ' a activé le compte de ' . $user->getName()])
                );
                return true;
            } else {
                return false;
            }
        }
    }

    public function deleteUser($GET, SchoolManager $SchoolManager)
    {
        $HistoryManager = new HistoryManager();
        $PostsManager = new PostsManager();
        $user = $this->getUserByName($GET['userName']);
        $school = $SchoolManager->getSchoolByName($GET['schoolName']);
        $posts = $PostsManager->getPostsByAuthor($user->getName());
        if (!$user->getIsAdmin()) {
            //if account is active and not moderator, nb active account -1
            if ($user->getIsActive() && !$user->getIsModerator()) {
                $SchoolManager->updateByName($GET['schoolName'], 'nbActiveAccount', $school->getNbActiveAccount() - 1);
            }
            //delete posts
            foreach ($posts as $post) {
                $PostsManager->deletePost($post->getId());
            }
            //delete account
            $this->delete($user->getId());
            //add history entry
            $HistoryManager->addEntry(new HistoryEntry(
                ['idSchool' => $school->getId(), 
                'category' => 'account', 
                'entry' => $_SESSION['pseudo'] . ' a supprimé le compte de ' . $user->getName()])
            );
            return true;
        } else {
            return false;
        }
    }
}
