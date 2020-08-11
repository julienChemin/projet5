<?php
namespace Chemin\ArtSchool\Model;
use Chemin\ArtSchool\Model\Controller;

class Backend extends Controller
{
    public static $SIDE = 'backend';
    public static $INDEX = 'indexAdmin.php';

    public function verifyInformation()
    {
        if (isset($_SESSION['grade']) && ($_SESSION['grade'] === ADMIN  || $_SESSION['grade'] === MODERATOR)) {
            //user is connect as admin or moderator
            $SchoolManager = new SchoolManager();
            $UserManager = new UserManager();
            if((!$SchoolManager->nameExists($_SESSION['school']) && !($_SESSION['school'] === ALL_SCHOOL)) || !$UserManager->nameExists($_SESSION['pseudo'])) {
                //if user name don't exist or if school name don't exist and isn't "allSchool" 
                $this->forceDisconnect();
            } else {
                $user = $UserManager->getUserByName($_SESSION['pseudo']);
                if ($user->getIsBan()) {
                    $this->disconnect();
                }
            }
        } elseif (isset($_SESSION['grade']) && $_SESSION['grade'] !== ADMIN  && $_SESSION['grade'] !== MODERATOR) {
            //user is connect but not as admin or moderator
            header('Location: index.php');
        } elseif (isset($_COOKIE['artSchoolAdminId'])) {
            //user is not connect but there is a cookie to sign in
            $this->useCookieToSignIn();
        } elseif (isset($_GET['action']) && $_GET['action'] !== 'resetPassword') {
            //home
            header('Location: indexAdmin.php');
        }
    }

    public function home()
    {
        $UserManager = new UserManager();
        $message = null;
        //if user is not connected and try to connect
        if (isset($_POST['ConnectPseudoAdmin']) && isset($_POST['ConnectPasswordAdmin'])) {
            if ($UserManager->canConnect($_POST['ConnectPseudoAdmin'], $_POST['ConnectPasswordAdmin'])) {
                $user = $UserManager->getUserByName($_POST['ConnectPseudoAdmin']);
                if ($user->getIsAdmin() || $user->getIsModerator()) {
                    if (isset($_POST['stayConnect'])) {
                        //if user want to stay connect
                        $this->setCookie($user);
                    }
                    $this->connect($user);
                    header('Location: indexAdmin.php');
                } else {
                    $this->accessDenied();
                }
            } else {
                $message = 'L\'identifiant ou le mot de passe est incorrecte';
            }
            //if user try to get back his password
        } else if (isset($_POST['postMail'])) {
            if ($UserManager->mailExists($_POST['postMail'])) {
                $UserManager->mailTemporaryPassword($UserManager->getUserByMail($_POST['postMail']));
                $message = "Un mail vient de vous être envoyé pour réinitialiser votre mot de passe.";
            } else {
                $message = "l'adresse mail renseignée ne correspond à aucun utilisateur";
            }
        }
        RenderView::render('template.php', 'backend/indexAdminView.php', ['option' => ['forgetPassword', 'signIn', 'home'], 'message' => $message]);
    }

    public function addSchool()
    {
        $message = null;
        if ($_SESSION['school'] === ALL_SCHOOL) {
            if (!empty($_POST['adminPassword']) && !empty($_GET['option']) && $_GET['option'] === 'add') {
                //if form to add school is filled
                $SchoolManager = new SchoolManager();
                $HistoryManager = new HistoryManager();
                $UserManager = new UserManager();
                $arrCanAddSchool = $SchoolManager->canAddSchool($_POST, $UserManager);
                if ($arrCanAddSchool['canAdd']) {
                    //add school and school administrator
                    $message = $SchoolManager->addSchool($_POST, $UserManager, $HistoryManager);
                    $ContractManager = new ContractManager('school', $SchoolManager);
                    $school = $SchoolManager->getSchoolByName($_POST['schoolName']);
                    $ContractManager->extendContract($school, $_POST['schoolDuration']);
                    if ($_POST['schoolDuration'] === '0') {
                        $SchoolManager->schoolToInactive($school->getId(), $UserManager);
                    }
                } else {
                    $message = $arrCanAddSchool['message'];
                }
            }
            RenderView::render('template.php', 'backend/addSchoolView.php', ['option' => ['addSchool'], 'message' => $message]);
        } else {
            header('Location: indexAdmin.php');
        }
    }

    public function settings()
    {
        if (!empty($_SESSION) && $_SESSION['grade'] === ADMIN && !empty($_SESSION['school'])) {
            $SchoolManager = new SchoolManager();
            $school = $SchoolManager->getSchoolByName($_SESSION['school']);
            $ContractManager = new ContractManager('school', $SchoolManager);
            if ($dateEndContract = $ContractManager->getDateContractEnd($school->getId())) {
                if ($school->getIsActive()) {
                    $contractInfo = 'Votre établissement est actif jusqu\'au ' . $dateEndContract;
                } else {
                    $contractInfo = 'Votre établissement est inactif depuis le ' . $dateEndContract;
                }
            } else {
                $contractInfo = 'Votre établissement est inactif';
            }
            RenderView::render('template.php', 'backend/settingsView.php', ['school' => $school, 'contractInfo' => $contractInfo]);
        } else {
            $this->accessDenied();
        }
    }

    public function moderatSchool()
    {
        if ($_SESSION['grade'] === ADMIN) {
            $SchoolManager = new SchoolManager();
            $schools = $SchoolManager->getSchoolByName($_SESSION['school']);
            $ContractManager = new ContractManager('school', $SchoolManager);
            if (is_array($schools) && count($schools) > 1) {
                $contractInfo = [];
                foreach ($schools as $school) {
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
            } else {
                if ($dateContractEnd = $ContractManager->getDateContractEnd($schools->getId())) {
                    if ($schools->getIsActive()) {
                        $contractInfo = 'Cet établissement est actif jusqu\'au ' . $dateContractEnd;
                    } else {
                        $contractInfo = 'Cet établissement n\'est plus actif depuis le ' . $dateContractEnd;
                    }
                } else {
                    $contractInfo = 'Cet établissement n\'est pas actif';
                }
            }
            $_SESSION['school'] === ALL_SCHOOL ? $view = 'moderatSchoolViewWebM.php' : $view = 'moderatSchoolView.php';
            RenderView::render('template.php', 'backend/' . $view, 
                ['schools' => $schools, 'contractInfo' => $contractInfo, 
                'option' => ['buttonToggleSchool', 'moderatSchool']]);
        } else {
            header('Location: indexAdmin.php');
        }
    }

    public function editSchool()
    {
        if ($_SESSION['grade'] === ADMIN 
        && ($_SESSION['school'] === ALL_SCHOOL || (!empty($_POST['schoolName']) && $_POST['schoolName'] === $_SESSION['school']))) {
            $SchoolManager = new SchoolManager();
            $message = null;
            if (!empty($_POST['elem'])) {
                //consulting form to edit school information
                $message = $SchoolManager->editSchool($_POST, new UserManager(), new HistoryManager());
            }
            RenderView::render('template.php', 'backend/editSchoolView.php', ['message' => $message]);
        } else {
            $this->accessDenied();
        }
    }

    public function moderatWebsite()
    {
        if ($_SESSION['school'] === ALL_SCHOOL) {
            RenderView::render('template.php', 'backend/moderatWebsiteView.php', ['option' => ['moderatWebsite']]);
        } else {
            $this->redirection();
        }
    }

    public function checkContract()
    {
        $arrAcceptedValues = ['user', 'school'];
        if (!empty($_SESSION['school']) && $_SESSION['school'] === ALL_SCHOOL 
        && !empty($_GET['type']) && in_array($_GET['type'], $arrAcceptedValues)) {
            if ($_GET['type'] === 'user') {
                $Manager = new UserManager();
            } elseif ($_GET['type'] === 'school') {
                $Manager = new SchoolManager();
            }
            $ContractManager = new ContractManager($_GET['type'], $Manager);
            $result = $ContractManager->checkRemind();
            echo json_encode($result);
        } else {
            echo 'false';
        }
    }

    public function checkWarnings()
    {
        if (!empty($_SESSION['school']) && $_SESSION['school'] === ALL_SCHOOL) {
            $WarningManager = new WarningManager();
            $allWarnEntries = $WarningManager->getAllActiveWarn();
            $response['nbActiveWarn'] = count($allWarnEntries);
            $response['nbEntriesUnwarned'] = 0;
            foreach ($allWarnEntries as $warn) {
                if ($WarningManager->canUnwarn($warn['dateUnwarning'])) {
                    $WarningManager->unWarnEntry(intval($warn['id']));
                    $response['nbEntriesUnwarned'] += 1;
                }
            }
            echo json_encode($response);
        } else {
            echo 'false';
        }
    }

    public function checkBanishments()
    {
        if (!empty($_SESSION['school']) && $_SESSION['school'] === ALL_SCHOOL) {
            $UserManager = new UserManager();
            $WarningManager = new WarningManager();
            $allBanEntries = $WarningManager->getAllActiveBan();
            $response['nbActiveBan'] = count($allBanEntries);
            $response['nbEntriesUnbanished'] = 0;
            foreach ($allBanEntries as $ban) {
                $user = $UserManager->getOneById(intval($ban['idUser']));
                if ($WarningManager->canUnban($user)) {
                    $WarningManager->unBan($user, $UserManager);
                    $response['nbEntriesUnbanished'] += 1;
                }
            }
            echo json_encode($response);
        } else {
            return 'false';
        }
    }

    public function checkUnusedImg()
    {
        if (!empty($_SESSION['school']) && $_SESSION['school'] === ALL_SCHOOL) {
            $ProfileContentManager = new ProfileContentManager();
            $response = $ProfileContentManager->deleteUnusedImg();
            echo json_encode($response);
        } else {
            return 'false';
        }
    }

    public function checkUnusedTag()
    {
        if (!empty($_SESSION['school']) && $_SESSION['school'] === ALL_SCHOOL) {
            $TagsManager = new TagsManager();
            $TagsManager->deleteUnusedTags();
            return 'true';
        } else {
            return 'false';
        }
    }

    public function warnUser()
    {
        if (!empty($_SESSION) && $_SESSION['school'] === ALL_SCHOOL) {
            $UserManager = new UserManager();
            if (isset($_GET['idUser']) && $UserManager->exists(intval($_GET['idUser']))) {
                $user = $UserManager->getOneById(intval($_GET['idUser']));
                $WarningManager = new WarningManager();
                $nbActiveWarn = $WarningManager->getNbActiveWarn($user);
                if ($WarningManager->isBan($user)) {
                    $banishmentInfo = $WarningManager->getBanEntry($user);
                } else {
                    $banishmentInfo = null;
                }
                RenderView::render(
                    'template.php', 'backend/warnUserView.php', 
                    ['user' => $user, 'nbActiveWarn' => $nbActiveWarn, 'banishmentInfo' => $banishmentInfo, 
                        'option' => ['warnUser']]);
            } else {
                $this->incorrectInformation();
            }
        } else {
            $this->accessDenied();
        } 
    }

    public function addWarning()
    {
        $UserManager = new UserManager();
        if (!empty($_SESSION) && $_SESSION['school'] === ALL_SCHOOL && isset($_POST['idUser']) && $UserManager->exists(intval($_POST['idUser']))) {
            $WarningManager = new WarningManager();
            $user = $UserManager->getOneById(intval($_POST['idUser']));
            $WarningManager->warn($user, $_POST['reasonWarn'], $UserManager);
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function moderatAdmin()
    {
        if ($_SESSION['grade'] === ADMIN) {
            $UserManager = new UserManager();
            $SchoolManager = new SchoolManager();
            $message = null;
            if (isset($_GET['option'], $_POST['schoolName']) && $_GET['option'] === 'addModerator') {
                //add new moderator
                if ($_SESSION['school'] === $_POST['schoolName'] || $_SESSION['school'] === ALL_SCHOOL) {
                    $arrCanAddModerator = $SchoolManager->canAddModerator($_POST, $UserManager);
                    if ($arrCanAddModerator['canAdd']) {
                        $message = $SchoolManager->addModerator($_POST, $UserManager, new HistoryManager());
                    } else {
                        $message = $arrCanAddModerator['message'];
                    }
                } else {
                    $this->accessDenied();
                }
            }
            $schools = $SchoolManager->getSchoolByName($_SESSION['school']);
            $sorting = $UserManager->moderatAdminSorting($UserManager->getUsersBySchool($_SESSION['school'], 'admin'));
            $_SESSION['school'] === ALL_SCHOOL ? $view = 'moderatAdminViewWebM.php' : $view = 'moderatAdminView.php';
            RenderView::render(
                'template.php', 'backend/' . $view, 
                ['users' => $sorting['users'], 'schools' => $schools, 'nbModerator' => $sorting['nbModerator'], 'message' => $message, 
                    'option' => ['moderatAdmin', 'buttonToggleSchool']]);
        } else {
            $this->accessDenied();
        }
    }

    public function moderatUsers()
    {
        $UserManager = new UserManager();
        $SchoolManager = new SchoolManager();
        $users = $UserManager->getUsersBySchool($_SESSION['school'], 'user');
        $schools = $SchoolManager->getSchoolByName($_SESSION['school']);
        $sorting = $UserManager->moderatUsersSorting($users, $schools);
        $_SESSION['school'] === ALL_SCHOOL ? $view = 'moderatUsersViewWebM.php' : $view = 'moderatUsersView.php';
        RenderView::render(
            'template.php', 'backend/' . $view, 
            ['incrementalId' => 0, 'users' => $sorting['users'], 'schools' => $schools, 'isActive' => $sorting['isActive'], 
                'option' => ['moderatUsers', 'buttonToggleSchool']]
        );
    }

    public function moderatReports()
    {
        if ($_SESSION['school'] === ALL_SCHOOL) {
            $arrAcceptedValue = array('post', 'comment');
            $idElem = null;
            if (!empty($_GET['elem']) && in_array($_GET['elem'], $arrAcceptedValue) && !empty($_GET['idElem']) && intval($_GET['idElem']) > 0) {
                $idElem = $this->getConcernedPostIdFromReport ($_GET['elem'], intval($_GET['idElem']));
            }
            RenderView::render('template.php', 'backend/moderatReportsView.php', ['idElem' => $idElem, 'option' => ['moderatReports']]);
        } else {
            $this->accessDenied();
        }
    }

    public function getReports()
    {
        if (!empty($_GET['elem']) && isset($_GET['offset']) && intval($_GET['offset']) >= 0) {
            $ReportManager = new ReportManager();
            echo json_encode($ReportManager->getReports($_GET['elem'], true, $_GET['offset']));
        } else {
            echo 'false';
        }
    }

    public function getReportsFromElem()
    {
        if (!empty($_GET['elem']) && isset($_GET['idElem']) && intval($_GET['idElem']) > 0) {
            $ReportManager = new ReportManager();
            echo json_encode($ReportManager->getReportsFromElem($_GET['elem'], $_GET['idElem']));
        } else {
            echo 'false';
        }
    }

    public function getCountReports()
    {
        $arrAcceptedValue = array('post', 'comment');
        if (!empty($_GET['elem']) && in_array($_GET['elem'], $arrAcceptedValue)) {
            $ReportManager = new ReportManager();
            echo json_encode($ReportManager->getCount($_GET['elem']));
        }
    }

    public function deleteReport()
    {
        $ReportManager = new ReportManager();
        $arrAcceptedValue = array('post', 'comment');
        if (!empty($_GET['elem']) && in_array($_GET['elem'], $arrAcceptedValue) && !empty($_GET['idElem']) && intval($_GET['idElem']) > 0 && !empty($_GET['idUser']) && intval($_GET['idUser']) > 0) {
            if ($ReportManager->reportExists($_GET['elem'], $_GET['idElem'], $_GET['idUser'])) {
                $ReportManager->deleteReport($_GET['elem'], $_GET['idElem'], $_GET['idUser']);
                echo true;
            } else {
                echo false;
            }
        } else {
            echo false;
        }
    }

    public function deleteReportsFromElem()
    {
        $ReportManager = new ReportManager();
        $arrAcceptedValue = array('post', 'comment');
        if (!empty($_GET['elem']) && in_array($_GET['elem'], $arrAcceptedValue) && !empty($_GET['idElem']) && intval($_GET['idElem']) > 0) {
            $ReportManager->deleteReportsFromElem($_GET['elem'], $_GET['idElem']);
            echo true;
        } else {
            echo false;
        }
    }

    public function createGroup()
    {
        if (!empty($_GET['group']) && !empty($_GET['schoolName']) && ($_GET['schoolName'] === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL)) {
            $SchoolManager = new SchoolManager();
            echo $SchoolManager->createGroup($_GET);
        } else {
            echo false;
        }
    }

    public function getGroup()
    {
        if (!empty($_GET['schoolName']) && ($_GET['schoolName'] === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL)) {
            $SchoolManager = new SchoolManager();
            $school = $SchoolManager->getSchoolByName($_GET['schoolName']);
            echo json_encode($school->getListSchoolGroups());
        }
    }

    public function setGroup()
    {
        if (!empty($_GET['userName']) && !empty($_GET['group'])) {
            $UserManager = new UserManager();
            if ($UserManager->nameExists($_GET['userName'])) {
                $SchoolManager = new SchoolManager();
                $user = $UserManager->getUserByName($_GET['userName']);
                if ($user->getSchool() === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL) {
                    echo $SchoolManager->setGroup($_GET, $user, $UserManager);
                } else {
                    echo false;
                }
            } else {
                echo false;
            }
        } else {
            echo false;
        }
    }

    public function deleteGroup()
    {
        if (!empty($_GET['group']) && !empty($_GET['schoolName']) && ($_GET['schoolName'] === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL)) {
            $SchoolManager = new SchoolManager();
            $UserManager = new UserManager();
            if ($SchoolManager->deleteGroup($_GET, $UserManager)) {
                $this->redirection();
            } else {
                $this->incorrectInformation();
            }
        } else {
            $this->incorrectInformation();
        }
    }

    public function editGrade()
    {
        if (isset($_GET['userName'], $_GET['schoolName'], $_GET['toAdmin'], $_GET['toModerator'])) {
            if (!($_GET['toAdmin'] === 'true' && $_GET['toModerator'] === 'true')) {
                $SchoolManager = new SchoolManager();
                $HistoryManager = new HistoryManager();
                $UserManager = new UserManager();
                if ($SchoolManager->nameExists($_GET['schoolName']) && $UserManager->nameExists($_GET['userName'])) {
                    $SchoolManager->editGrade($_GET, $UserManager, $HistoryManager);
                } else {
                    $this->incorrectInformation();
                }
            } else {
                throw new \Exception("Un utilisateur ne peut pas être à la fois modérateur et administrateur");
            }
        } else {
            $this->incorrectInformation();
        }
        $this->redirection();
    }

    public function toggleUserIsActive()
    {
        if (!empty($_GET['userName']) && !empty($_GET['schoolName'])) {
            if ($_SESSION['school'] === ALL_SCHOOL || $_SESSION['school'] === $_GET['schoolName']) {
                $SchoolManager = new SchoolManager();
                $HistoryManager = new HistoryManager();
                $UserManager = new UserManager();
                if ($SchoolManager->nameExists($_GET['schoolName']) && $UserManager->nameExists($_GET['userName'])) {
                    if ($UserManager->toggleIsActive($_GET, $SchoolManager, $HistoryManager)) {
                        $this->redirection();
                    } else {
                        throw new \Exception("Il est impossible d'effectuer cette action, vous avez atteint le nombre maximum de compte utilisateur actif");
                    }
                } else {
                    $this->incorrectInformation();
                }
            } else {
                $this->accessDenied();
            }
        } else {
            $this->incorrectInformation();
        }
    }

    public function delete()
    {
        if (!empty($_GET['elem'])) {
            $SchoolManager = new SchoolManager();
            $UserManager = new UserManager();
            switch ($_GET['elem']) {
            case 'user' :
                if (!empty($_GET['userName']) && !empty($_GET['schoolName']) && $UserManager->nameExists($_GET['userName']) 
                    && ($_SESSION['school'] === ALL_SCHOOL || $_SESSION['school'] === $_GET['schoolName'])
                ) {
                    if ($UserManager->deleteUser($_GET, $SchoolManager)) {
                        $this->redirection();
                    } else {
                        throw new \Exception("Vous ne pouvez pas supprimer un compte administrateur");
                    }
                } else {
                    $this->incorrectInformation();
                }
                break;
            default :
                $this->incorrectInformation();
            }
        }
    }

    public function schoolProfile()
    {
        if (!empty($_GET['school']) 
        && ($_GET['school'] === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL)) {
            $SchoolManager = new SchoolManager();
            $school = $SchoolManager->getSchoolByName($_GET['school']);
            $contractInfo = null;
            if (!$school->getIsActive()) {
                $ContractManager = new ContractManager('school', $SchoolManager);
                if ($dateContractEnd = $ContractManager->getDateContractEnd($school->getId())) {
                    $contractInfo = 'Cet établissement n\'est plus actif sur le site depuis le ' . $dateContractEnd;
                } else {
                    $contractInfo = 'Cet établissement n\'est pas actif sur le site';
                }
            }
            $ProfileContentManager = new ProfileContentManager();
            $profileContent = $ProfileContentManager->getByProfileId($school->getId(), true);
            $_SESSION['grade'] === ADMIN ? $view = 'backend' : $view = 'frontend';
            RenderView::render('template.php', $view . '/schoolProfileView.php', 
                ['school' => $school, 'profileContent' => $profileContent, 'contractInfo' => $contractInfo, 
                'option' => ['schoolProfile', 'tinyMCE']]);
        } else {
            $this->incorrectInformation();
        }
    }

    public function updateProfile()
    {
        $SchoolManager = new SchoolManager();
        if (!empty($_GET['school']) && !empty($_GET['elem']) && $_GET['school'] === $_SESSION['school']) {
            $school = $SchoolManager->getSchoolByName($_SESSION['school']);
            switch ($_GET['elem']) {
            case 'profileBanner' :
                if (isset($_GET['noBanner'], $_GET['value'])) {
                    if (strpos($_GET['value'], $school->getProfileBanner()) === false && file_exists($school->getProfileBanner())) {
                        unlink($school->getProfileBanner());
                    }
                    $infos = $_GET['value'] . ' ' . $_GET['noBanner'];
                    $SchoolManager->updateByName($_GET['school'], 'profileBannerInfo', $infos);
                }
                break;
            case 'profilePicture' :
                if (isset($_GET['orientation'], $_GET['size'], $_GET['value'])) {
                    if (strpos($_GET['value'], $school->getProfilePicture()) === false && file_exists($school->getProfilePicture())) {
                        unlink($school->getProfilePicture());
                    }
                    $infos = $_GET['value'] . ' ' . $_GET['orientation'] . ' ' . $_GET['size'];
                    $SchoolManager->updateByName($_GET['school'], 'profilePictureInfo', $infos);
                }
                break;
            case 'profileText' :
                if (isset($_GET['block'], $_GET['school'], $_GET['schoolPos'])) {
                    $infos = $_GET['block'] . ' ' . $_GET['schoolPos'];
                    $SchoolManager->updateByName($_GET['school'], 'profileTextInfo', $infos);
                }
                break;
            case 'content' :
                $ProfileContentManager = new ProfileContentManager();
                $SchoolManager->updateProfileContent($_GET, $_POST, $ProfileContentManager);
                $this->redirection();
                break;
            } 
        } else {
            $this->incorrectInformation();
        }
    }

    public function upload()
    {
        if (!empty($_GET['elem'])) {
            $arrAcceptedExtention = array("jpeg", "jpg", "png", "gif");
            require 'view/upload.php';
            if (!empty($final_path)) {
                switch ($_GET['elem']) {
                case 'banner' :
                    $this->uploadBanner($_GET, $final_path);
                    break;
                case 'picture' :
                    $this->uploadProfilePicture($_GET, $final_path);
                    break;
                default :
                    $this->incorrectInformation();
                }
                $this->redirection();
            } else {
                throw new \Exception("Le fichier n'est pas conforme");
            }
        } else {
            $this->incorrectInformation();
        }
    }

    public function uploadBanner(array $GET, string $finalPath)
    {
        $validBannerValue = array('true', 'false');
        if (!empty($GET['noBanner']) && in_array($GET['noBanner'], $validBannerValue)) {
            $SchoolManager = new SchoolManager();
            $school = $SchoolManager->getSchoolByName($_SESSION['school']);
            if (file_exists($school->getProfileBanner())) {
                unlink($school->getProfileBanner());
            }    
            $infos = $finalPath . ' ' . $GET['noBanner'];
            $SchoolManager->updateByName($_SESSION['school'], 'profileBannerInfo', $infos);
        } else {
            $this->incorrectInformation();
        }
    }

    public function uploadProfilePicture(array $GET, string $finalPath)
    {
        $validOrientationValue = array('highPicture', 'widePicture');
        $validSizeValue = array('smallPicture', 'mediumPicture', 'bigPicture');
        if (!empty($GET['orientation']) && in_array($GET['orientation'], $validOrientationValue)
            && !empty($GET['size']) && in_array($GET['size'], $validSizeValue)
        ) {
            $SchoolManager = new SchoolManager();
            $school = $SchoolManager->getSchoolByName($_SESSION['school']);
            if (file_exists($school->getProfilePicture())) {
                unlink($school->getProfilePicture());
            }
            $infos = $finalPath . ' ' . $GET['orientation'] . ' ' . $GET['size'];
            $SchoolManager->updateByName($_SESSION['school'], 'profilePictureInfo', $infos);
        } else {
            $this->incorrectInformation();
        }
    }

    public function schoolHistory()
    {
        $SchoolManager = new SchoolManager();
        if ($_SESSION['school'] === ALL_SCHOOL) {
            $schools = $SchoolManager->getSchoolByName($_SESSION['school']);
            RenderView::render('template.php', 'backend/schoolHistoryViewWebM.php', ['schools' => $schools, 'option' => ['schoolHistory']]);
        } else {
            $HistoryManager = new HistoryManager();
            $school = $SchoolManager->getSchoolByName($_SESSION['school']);
            $entries = $HistoryManager->getBySchool($school->getId());
            RenderView::render('template.php', 'backend/schoolHistoryView.php', ['school' => $school, 'entries' => $entries, 'option' => ['schoolHistory']]);
        }
    }

    public function getSchoolHistory()
    {
        if (!empty($_GET['school']) && ($_SESSION['school'] === ALL_SCHOOL || (!empty($_GET['schoolName']) && $_GET['schoolName'] === $_SESSION['school']))) {
            $HistoryManager = new HistoryManager();
            $SchoolManager = new SchoolManager();
            if ($SchoolManager->exists(intval($_GET['school']))) {
                echo json_encode($HistoryManager->getSchoolHistory($_GET));
            } else {
                $this->incorrectInformation();
            }
        }
    }

    public function addSchoolPost()
    {
        if ($_SESSION['school'] !== ALL_SCHOOL) {
            $SchoolManager = new SchoolManager();
            $school = $SchoolManager->getSchoolByName($_SESSION['school']);
            if ($_SESSION['grade'] === STUDENT) {
                $isStudent = 'true';
                $urlForm = 'index.php?action=uploadPost';
                $uploadType = 'private';
            } else {
                $isStudent = 'false';
                $urlForm = 'indexAdmin.php?action=uploadSchoolPost';
                $uploadType = 'public';
            }
            RenderView::render(
                'template.php', 'backend/addSchoolPostView.php', 
                ['isStudent' => $isStudent, 'urlForm' => $urlForm, 'uploadType' => $uploadType, 'groups' => $school->getListSchoolGroups(), 'option' => ['addPost', 'tinyMCE']]);
        } else {
            $this->incorrectInformation();
        }
    }

    public function uploadSchoolPost()
    {
        if (!empty($_POST['fileTypeValue']) && !empty($_POST['uploadType'])) {
            $PostsManager = new PostsManager();
            $TagsManager = new TagsManager();
            //check upload type
            $_POST['listGroup'] === "all" ? $authorizedGroups = null : $authorizedGroups = $_POST['listAuthorizedGroups'];
            if (isset($_SESSION['id'], $_POST)) {
                if ($response = $PostsManager->canUploadPost($_POST, $TagsManager)) {
                    if ($PostsManager->uploadPost($response, true, $authorizedGroups)) {
                        header('Location: indexAdmin.php?action=schoolProfile&school=' . $_SESSION['school']);
                    } else {
                        throw new \Exception("Le fichier n'est pas conforme");
                    }
                } else {
                    $this->incorrectInformation();
                }
            } else {
                $this->accessDenied();
            }
        }
        header('Location: indexAdmin.php?action=schoolProfile&school=' . $_SESSION['school']);
    }

    private function getConcernedPostIdFromReport(string $elem, int $idElem)
    {
        switch ($elem) {
            case 'post':
                return $idElem;
                break;
            case 'comment':
                $CommentsManager = new CommentsManager();
                if ($CommentsManager->exists($idElem)) {
                    $comment = $CommentsManager->getOneById($idElem);
                    return $comment->getIdPost();
                } else {
                    $this->incorrectInformation();
                }
                break;
            default :
                $this->incorrectInformation();
        }
    }
}
