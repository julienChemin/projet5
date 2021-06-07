<?php
namespace Chemin\ArtSchools\Model;

abstract class Controller
{
    public static $SIDE = '';
    public static $INDEX = '';
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
                if (!empty($_COOKIE['artSchoolsAdminId'])) {
                    $cookie = explode("-", $_COOKIE['artSchoolsAdminId']);
                } else {
                    $cookie = explode("-", $_COOKIE['artSchoolsId']);
                }
                break;
            case 'backend' :
                $cookie = explode("-", $_COOKIE['artSchoolsAdminId']);
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
                        $fullNameAndPseudo = $user->getPseudo() . " ( " . $user->getFirstname() . " " . $user->getLastName() . " )";
                        $errorMsg = "L'utilisateur - " . $fullNameAndPseudo . " - ne peut plus se connecter car";
                        $errorMsg .= " l'établissement - " . $user->getSchool() . " - n'existe pas / plus.";
                        $this->setErrorReport($errorMsg, $user->getPseudo());
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

    protected function setErrorReport(string $errorMsg = 'no error message')
    {
        $ReportManager = new ReportManager();
        $ReportManager->setReport('other', $errorMsg, null, $_SESSION['id']);
    }

    protected function tryToConnect(string $pseudo, string $password, UserManager $UserManager, bool $adminSide, bool $stayConnect = null)
    {
        if ($UserManager->canConnect($pseudo, $password)) {
            $user = $UserManager->getUserByPseudo($pseudo);
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
        $this->sessionUpdate($user);
    }

    protected function forceDisconnect()
    {
        session_destroy();
        if (isset($_COOKIE['artSchoolsId']) || isset($_COOKIE['artSchoolsAdminId'])) {
            $this->useCookieToSignIn();
        } else {
            throw new \Exception("Certaines informations lié a votre compte ne sont plus valide, veuillez vous reconnecter pour mettre à jour ces informations.Cocher la case 'rester connecté' lors de la connection peu vous éviter ce genre de désagrément");
        }
    }

    protected function sessionUpdate(User $user)
    {
        $SchoolManager = new SchoolManager();
        $school = $SchoolManager->getSchoolByName($user->getSchool());
        
        $school && is_object($school) ? $_SESSION['idSchool'] = $school->getId() : $_SESSION['idSchool'] = 0;
        
        $_SESSION['id'] = $user->getId();
        $_SESSION['pseudo'] = $user->getPseudo();
        $_SESSION['firstName'] = $user->getFirstName();
        $_SESSION['lastName'] = $user->getLastName();
        $_SESSION['fullName'] = $user->getFirstName() . ' ' . $user->getLastName();
        $_SESSION['school'] = $user->getSchool();
        $_SESSION['schoolGroup'] = $user->getSchoolGroup();
        $_SESSION['isActive'] = $user->getIsActive();
        
        if ($user->getIsAdmin()) {
            $_SESSION['grade'] = ADMIN;
            $_SESSION['color'] = '#de3b12';
        } elseif ($user->getIsModerator()) {
            $_SESSION['grade'] = MODERATOR;
            $_SESSION['color'] = '#de3b12';
        } elseif (static::$SIDE === 'frontend' && $_SESSION['school'] !== NO_SCHOOL) {
            $_SESSION['grade'] = STUDENT;
            $_SESSION['color'] = '#CF8B3F';
        } elseif (static::$SIDE === 'frontend') {
            $_SESSION['grade'] = USER;
            $_SESSION['color'] = '#b0a396';
        }
    }

    protected function setCookie(User $user)
    {
        setcookie('artSchoolsId', $user->getId() . '-' . $user->getPassword(), time()+(365*24*3600), null, null, false, true);
        if ($user->getIsAdmin() || $user->getIsModerator()) {
            setcookie('artSchoolsAdminId', $user->getId() . '-' . $user->getPassword(), time()+(365*24*3600), null, null, false, true);
        }
    }

    protected function cookieDestroy()
    {
        if (isset($_COOKIE['artSchoolsId'])) {
            setcookie('artSchoolsId', '', time()-3600, null, null, false, true);
        }
        if (isset($_COOKIE['artSchoolsAdminId'])) {
            setcookie('artSchoolsAdminId', '', time()-3600, null, null, false, true);
        }
    }

    protected function eraseCookie(User $user)
    {
        $this->cookieDestroy();
        $this->setcookie($user);
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

    protected function incorrectInformation(string $reason = null)
    {
        if ($reason) {
            throw new \Exception($reason);
        } else {
            throw new \Exception("Les informations renseignées sont incorrectes");
        }
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

    /*------------------------------ file stuff ------------------------------*/
    protected function deleteFile($filePath)
    {
        if (!empty($filePath) && strlen($filePath) > 0 && file_exists($filePath) && strpos($filePath, 'question-mark') === false) {                              
            unlink($filePath);
        }
        return $this;
    }

    protected function moveFile(string $filePath, string $destination = "public/images/dl/")
    {
        if (!file_exists($filePath)) {
            return -1;
        } else {
            if(!copy($filePath, $destination)) {
                return -2;
            } else {
                if(!unlink($filePath)) {
                    return -3;
                }
            }
        }
        return 1;
    }

    protected function convert_bytes($val , $type_val , $type_wanted)
    {
        /*
            Titre : Convertisseur octet, Ko, Mo, Go, To, Po                                                                       
                                                                                                                                
            URL   : https://phpsources.net/code_s.php?id=997                                                                                   
        */
        $tab_val = array("o", "ko", "Mo", "Go", "To", "Po", "Eo");

        if (!(in_array($type_val, $tab_val) && in_array($type_wanted, $tab_val))) {
            return 0;
        }
        
        $tab = array_flip($tab_val);
        $diff = $tab[$type_val] - $tab[$type_wanted];

        if ($diff > 0) {
            return ($val * pow(1024, $diff));
        }
        
        if ($diff < 0) {
            return ($val / pow(1024, -$diff));
        }
        
        return ($val);
    }

    /*--- forum ---*/
    protected function accessTheForum()
    {
        $result = ['school' => null, 'user' => null];

        if (!empty($_SESSION['school']) && $_SESSION['school'] !== NO_SCHOOL) {
            $SchoolManager = new SchoolManager();
            $school = $SchoolManager->getSchoolByName($_SESSION['school']);
            $UserManager = new UserManager();
            $user = $UserManager->getOneById($_SESSION['id']);

            if ($school) {
                if ($_SESSION['school'] === ALL_SCHOOL || $school->getIsActive()) {
                    $result['school'] = $school;
                } else {
                    $result['school'] = false;
                }
            }

            if ($user) {
                if ($user->getIsActive()) {
                    $result['user'] = $user;
                } else {
                    $result['user'] = false;
                }
            }
        }

        return $result;
    }

    protected function cannotAccessForum($user, $school) {
        if ($school === false) {
            $this->incorrectInformation("Le forum n'est pas accessible car l'abonnement de l'établissement scolaire est désactivé");
        } else if ($user === false) {
            $this->incorrectInformation("Le forum ne vous est pas accessible car l'abonnement de votre compte n'est pas actif");
        } else {
            $this->incorrectInformation();
        }
    }

    /*--- other ---*/
    protected function checkForScriptInsertion(array $arr)
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
}
