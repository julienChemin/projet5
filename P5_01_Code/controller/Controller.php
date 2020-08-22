<?php
namespace Chemin\ArtSchool\Model;

abstract class Controller
{
    /*-------------------------------------------------------------------------------------
    ----------------------------------- PUBLIC FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/
    public function disconnect()
    {
        if (isset($_SESSION)) {
            session_destroy();
        }
        $this->cookieDestroy();
        header('Location: index.php');
    }

    public function error(string $error_msg)
    {
        RenderView::render('template.php', static::$SIDE . '/errorView.php', ['error_msg' => $error_msg]);
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PROTECTED FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/
    protected function useCookieToSignIn()
    {
        switch (static::$SIDE) {
            case 'frontend' :
                if (!empty($_COOKIE['artSchoolAdminId'])) {
                    $cookie = explode("-", $_COOKIE['artSchoolAdminId']);
                } else {
                    $cookie = explode("-", $_COOKIE['artSchoolId']);
                }
                break;
            case 'backend' :
                $cookie = explode("-", $_COOKIE['artSchoolAdminId']);
                break;
        }
        if (count($cookie) === 2) {
            $UserManager = new UserManager();
            $userId = htmlspecialchars($cookie[0]);
            $userPassword = htmlspecialchars($cookie[1]);
            if ($user = $UserManager->getOneById($userId)) {
                if ($user->getPassword() === $userPassword && !$user->getIsBan()) {
                    $SchoolManager = new SchoolManager();
                    if(!$SchoolManager->nameExists($user->getSchool()) && $user->getSchool() !== ALL_SCHOOL) {
                        //if school name don't exist and isn't "allSchool"
                        $this->cookieDestroy();
                        throw new \Exception(
                            "Le nom de l'établissement scolaire auquel vous êtes affilié n'existe pas / plus.
							Un message d'erreur a été envoyé à un administrateur du site et sera traité dans les plus brefs délais.
							Merci de votre compréhension"
                        );
                    } else {
                        $this->connect($user);
                    }
                } else {
                    $this->disconnect();
                }
            } else {
                $this->disconnect();
            }
        } else {
            $this->disconnect();
        }
    }

    protected function tryToConnect(string $pseudo, string $password, UserManager $UserManager, bool $adminSide, bool $stayConnect = null)
    {
        if ($UserManager->canConnect($pseudo, $password)) {
            $user = $UserManager->getUserByName($pseudo);
            if (!$adminSide || ($adminSide && ($user->getIsAdmin() || $user->getIsModerator()))) {
                if (isset($stayConnect)) {
                    $this->setCookie($user);
                }
                $this->connect($user);
                $adminSide ? $urlLocation = 'indexAdmin.php' : $urlLocation = 'index.php';
                header('Location: ' . $urlLocation);
            }
        } else {
            return 'L\'identifiant ou le mot de passe est incorrecte';
        }
    }

    protected function tryRecoverPassword(string $mail, UserManager $UserManager)
    {
        if ($user = $UserManager->getUserByMail($mail)) {
            $UserManager->mailTemporaryPassword($user);
            return "Un mail vient de vous être envoyé pour réinitialiser votre mot de passe";
        } else {
            return "l'adresse mail renseignée ne correspond à aucun utilisateur";
        }
    }

    protected function connect(User $user)
    {
        if ($user->getIsBan()) {
            $WarningManager = new WarningManager();
            if ($WarningManager->canUnban($user)) {
                $UserManager = new UserManager();
                $WarningManager->unBan($user, $UserManager);
            } else {
                $banEntry = $WarningManager->getBanEntry($user);
                throw new \Exception("Votre compte a reçu 3 avertissements et est bloqué jusqu'aux " . $banEntry['dateUnbanishment']);
            }
        }

        $_SESSION['id'] = $user->getId();
        $_SESSION['pseudo'] = $user->getName();
        $_SESSION['school'] = $user->getSchool();
        $_SESSION['group'] = $user->getSchoolGroup();
        $_SESSION['isActive'] = $user->getIsActive();

        if ($user->getIsAdmin()) {
            $_SESSION['grade'] = ADMIN;
        } elseif ($user->getIsModerator()) {
            $_SESSION['grade'] = MODERATOR;
        } elseif (static::$SIDE === 'frontend' && $_SESSION['school'] !== NO_SCHOOL) {
            $_SESSION['grade'] = STUDENT;
        } elseif (static::$SIDE === 'frontend') {
            $_SESSION['grade'] = USER;
        }
    }

    protected function forceDisconnect()
    {
        session_destroy();
        if (isset($_COOKIE['artSchoolId']) || isset($_COOKIE['artSchoolAdminId'])) {
            $this->useCookieToSignIn();
        } else {
            throw new \Exception("Certaines informations lié a votre compte ne sont plus valide, veuillez vous reconnecter pour mettre à jour ces informations.Cocher la case 'rester connecté' lors de la connection peu vous éviter ce genre de désagrément");
        }
    }

    protected function setCookie(User $user)
    {
        setcookie('artSchoolId', $user->getId() . '-' . $user->getPassword(), time()+(365*24*3600), null, null, false, true);
        if ($user->getIsAdmin() || $user->getIsModerator()) {
            setcookie('artSchoolAdminId', $user->getId() . '-' . $user->getPassword(), time()+(365*24*3600), null, null, false, true);
        }
    }

    protected function cookieDestroy()
    {
        if (isset($_COOKIE['artSchoolId'])) {
            setcookie('artSchoolId', '', time()-3600, null, null, false, true);
        }
        if (isset($_COOKIE['artSchoolAdminId'])) {
            setcookie('artSchoolAdminId', '', time()-3600, null, null, false, true);
        }
    }

    protected function redirection(string $url = null, bool $urlFirst = false)
    {
        if ($urlFirst) {
            header('Location: ' . $url);
        } elseif (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } elseif (!empty($url)) {
            header('Location: ' . $url);
        } else {
            header('Location: ' . static::$INDEX);
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

    /*------------------------------ contract info ------------------------------*/
    protected function getUserContractInfo(User $user, ContractManager $ContractManager)
    {
        if ($dateContractEnd = $ContractManager->getDateContractEnd($user->getId())) {
            if ($user->getIsActive()) {
                return 'Votre compte est actif jusqu\'au ' . $dateContractEnd;
            } else {
                return 'Votre compte est inactif depuis le ' . $dateContractEnd;
            }
        } else {
            return 'Votre compte est inactif';
        }
    }

    protected function getSchoolContractInfo($school, ContractManager $ContractManager, bool $forProfile = false)
    {
        if ($forProfile) {
            return $this->schoolContractInfoForProfile($school, $ContractManager);
        } else {
            return $this->schoolContractInfo($school, $ContractManager);
        }
    }

    protected function schoolContractInfoForProfile($school, ContractManager $ContractManager)
    {
        $contractInfo = null;
        if (is_object($school) && !$school->getIsActive()) {
            if ($dateContractEnd = $ContractManager->getDateContractEnd($school->getId())) {
                $contractInfo = 'Cet établissement n\'est plus actif sur le site depuis le ' . $dateContractEnd;
            } else {
                $contractInfo = 'Cet établissement n\'est pas actif sur le site';
            }
        }
        return $contractInfo;
    }

    protected function schoolContractInfo($school, ContractManager $ContractManager)
    {
        // $school must be object School or array which contain object School
        $contractInfo = null;
        if (is_array($school) && count($school) > 1) {
            $contractInfo = [];
            foreach ($school as $school) {
                if ($dateContractEnd = $ContractManager->getDateContractEnd($school->getId())) {
                    if ($school->getIsActive()) {
                        $contractInfo[] = 'Cet établissement est actif jusqu\'au ' . $dateContractEnd;
                    } else {
                        $contractInfo[] = 'Cet établissement n\'est plus actif depuis le ' . $dateContractEnd;
                    }
                } else {
                    $contractInfo[] = 'Cet établissement n\'est pas actif';
                }
            }
        } elseif (is_object($school)) {
            if ($dateContractEnd = $ContractManager->getDateContractEnd($school->getId())) {
                if ($school->getIsActive()) {
                    $contractInfo = 'Cet établissement est actif jusqu\'au ' . $dateContractEnd;
                } else {
                    $contractInfo = 'Cet établissement n\'est plus actif depuis le ' . $dateContractEnd;
                }
            } else {
                $contractInfo = 'Cet établissement n\'est pas actif';
            }
        }
        return $contractInfo;
    }
}
