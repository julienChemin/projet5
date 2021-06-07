<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\Controller;

class Backend extends Controller
{
    public static $SIDE = 'backend';
    public static $INDEX = 'indexAdmin.php';

    /*-------------------------------------------------------------------------------------
    ----------------------------------- FUNCTION PUBLIC ------------------------------------
    -------------------------------------------------------------------------------------*/

    public function verifyInformation()
    {
        // this function is call every time the visitor open a new page
        // verify user information (school, pseudo, mail [etc..] can change)
        if (isset($_SESSION['grade']) && ($_SESSION['grade'] === ADMIN  || $_SESSION['grade'] === MODERATOR)) {
            //user is connect as admin or moderator
            $SchoolManager = new SchoolManager();
            $UserManager = new UserManager();
            if((!$SchoolManager->nameExists($_SESSION['school']) && !($_SESSION['school'] === ALL_SCHOOL)) || !$UserManager->pseudoExists($_SESSION['pseudo'])) {
                //if user name or school name don't exist and isn't "allSchool" -> disconnect
                $this->forceDisconnect();
            } else {
                $user = $UserManager->getUserByPseudo($_SESSION['pseudo']);
                if ($user->getIsBan()) {
                    // user is ban -> disconnect
                    $this->disconnect();
                } else {
                    // all it's ok -> MAJ session info
                    $this->sessionUpdate($user);
                }
            }
        } elseif (isset($_SESSION['grade']) && $_SESSION['grade'] !== ADMIN  && $_SESSION['grade'] !== MODERATOR) {
            //user is connect but not as admin or moderator
            header('Location: index.php');
        } elseif (isset($_COOKIE['artSchoolsAdminId'])) {
            //user is not connect but there is a cookie to sign in
            $this->useCookieToSignIn();
        } elseif (isset($_GET['action']) && $_GET['action'] !== 'resetPassword') {
            //home
            header('Location: indexAdmin.php');
        }
    }

    public function maintenance() {
        RenderView::render('template.php', 'maintenanceView.php');
    }

    public function home()
    {
        $message = null;
        if (isset($_POST['ConnectPseudoAdmin']) && isset($_POST['ConnectPasswordAdmin'])) {
            // user try to connect
            !empty($_POST['stayConnect']) ? $stayConnect = $_POST['stayConnect'] : $stayConnect = null;
            $message = $this->tryToConnect($_POST['ConnectPseudoAdmin'], $_POST['ConnectPasswordAdmin'], new UserManager(), true, $stayConnect);
        } else if (isset($_POST['postMail'])) {
            // user try to get back his password
            $message = $this->tryRecoverPassword($_POST['postMail'], new UserManager());
        }
        RenderView::render('template.php', 'backend/indexAdminView.php', ['option' => ['forgetPassword', 'signIn', 'home'], 'message' => $message]);
    }

    public function addSchool()
    {
        $message = null;
        if ($_SESSION['school'] === ALL_SCHOOL) {
            if (!empty($_POST['adminPassword']) && !empty($_GET['option']) && $_GET['option'] === 'add') {
                // form to add school is filled
                $SchoolManager = new SchoolManager();
                $UserManager = new UserManager();
                $canAddSchool = $SchoolManager->canAddSchool($_POST, $UserManager);
                if ($canAddSchool['succes']) {
                    //add school and school administrator
                    $message = $SchoolManager->addSchool($_POST, $UserManager, new HistoryManager());
                    $ForumCategoryManager = new ForumCategoryManager();
                    $ContractManager = new ContractManager('school', $SchoolManager);
                    $user = $UserManager->getOneById($_SESSION['id']);
                    $school = $SchoolManager->getSchoolByName($_POST['schoolName']);
                    $ForumCategoryManager->setupForumForNewSchool($user, $school);
                    if (intval($_POST['schoolDuration']) > 0) {
                        $ContractManager->extendContract($school, intval($_POST['schoolDuration']));
                    } else {
                        $SchoolManager->schoolToInactive($school->getId(), $UserManager);
                    }
                } else {
                    $message = $canAddSchool['message'];
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
            $contractInfo = $this->getSchoolContractInfo($school, new ContractManager('school', $SchoolManager));
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
            $contractInfo = $this->getSchoolContractInfo($schools, new ContractManager('school', $SchoolManager));
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

    public function warnUser()
    {
        if (!empty($_SESSION) && $_SESSION['school'] === ALL_SCHOOL) {
            $UserManager = new UserManager();
            if (isset($_GET['idUser']) && $user = $UserManager->getOneById(intval($_GET['idUser']))) {
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

    public function moderatAdmin()
    {
        if ($_SESSION['grade'] === ADMIN) {
            $UserManager = new UserManager();
            $SchoolManager = new SchoolManager();
            $schools = $SchoolManager->getSchoolByName($_SESSION['school']);
            $users = $UserManager->getUsersBySchool($_SESSION['school'], 'admin', ['lastName']);
            $message = null;
            if (isset($_GET['option'], $_POST['schoolName']) && $_GET['option'] === 'addModerator' 
            && ($_SESSION['school'] === ALL_SCHOOL || $schools->getIsActive())) {
                //add new moderator
                $message = $this->adminCreateNewModerator($UserManager, $SchoolManager);
            }
            $nbModerator = $UserManager->countModerator($users);
            $_SESSION['school'] === ALL_SCHOOL ? $webmasterSide = true : $webmasterSide = false;
            $users = $UserManager->orderUsersBySchool($users, false, $webmasterSide);
            $_SESSION['school'] === ALL_SCHOOL ? $view = 'moderatAdminViewWebM.php' : $view = 'moderatAdminView.php';
            RenderView::render(
                'template.php', 'backend/' . $view, 
                ['users' => $users, 'schools' => $schools, 'nbModerator' => $nbModerator, 'message' => $message, 
                    'option' => ['moderatAdmin', 'buttonToggleSchool']]);
        } else {
            $this->accessDenied();
        }
    }

    public function moderatUsers()
    {
        $UserManager = new UserManager();
        $SchoolManager = new SchoolManager();
        $users = $UserManager->getUsersBySchool($_SESSION['school'], 'user', ['schoolGroup', 'lastName']);
        $schools = $SchoolManager->getSchoolByName($_SESSION['school']);
        $users = $UserManager->orderUsersBySchool($users, true);
        $_SESSION['school'] === ALL_SCHOOL ? $view = 'moderatUsersViewWebM.php' : $view = 'moderatUsersView.php';
        RenderView::render(
            'template.php', 'backend/' . $view, 
            ['incrementalId' => 0, 'users' => $users, 'schools' => $schools, 
                'option' => ['moderatUsers', 'buttonToggleSchool']]
        );
    }

    public function moderatReports()
    {
        if ($_SESSION['school'] === ALL_SCHOOL) {
            $arrAcceptedValue = array('profile', 'post', 'comment');
            $idElem = null;

            if (!empty($_GET['elem']) && in_array($_GET['elem'], $arrAcceptedValue) && !empty($_GET['idElem']) && intval($_GET['idElem']) > 0) {
                $idElem = $this->getIdElemForRedirection ($_GET['elem'], intval($_GET['idElem']));
            }
            RenderView::render('template.php', 'backend/moderatReportsView.php', ['idElem' => $idElem, 'option' => ['moderatReports']]);
        } else {
            $this->accessDenied();
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
                $UserManager = new UserManager();
                if ($SchoolManager->nameExists($_GET['schoolName']) && $UserManager->pseudoExists($_GET['userName'])) {
                    $SchoolManager->editGrade($_GET, $UserManager, new HistoryManager());
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
        if ($_SESSION['school'] === ALL_SCHOOL || $_SESSION['school'] === $_GET['schoolName']) {
            $SchoolManager = new SchoolManager();
            $UserManager = new UserManager();
            if (!empty($_GET['userName']) && !empty($_GET['schoolName']) 
            && $SchoolManager->nameExists($_GET['schoolName']) && $UserManager->pseudoExists($_GET['userName'])) {
                if ($UserManager->toggleIsActive($_GET, $SchoolManager, new HistoryManager())) {
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
    }

    public function delete()
    {
        if (!empty($_GET['elem'])) {
            $UserManager = new UserManager();
            switch ($_GET['elem']) {
                case 'user' :
                    if (!empty($_GET['userName']) && !empty($_GET['schoolName']) && $UserManager->pseudoExists($_GET['userName']) 
                    && ($_SESSION['school'] === ALL_SCHOOL || ($_SESSION['school'] === $_GET['schoolName'] && ($_SESSION['grade'] === ADMIN || $_SESSION['grade'] === MODERATOR)))) {
                        if ($UserManager->deleteUser($_GET, new SchoolManager())) {
                            $this->redirection();
                        } else {
                            throw new \Exception("Vous ne pouvez pas supprimer ce compte");
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

    public function leaveSchool()
    {
        // Admin can remove user from his school
        if (!empty($_SESSION['id']) && !empty($_GET['userName']) && !empty($_GET['schoolName'])) {
            $UserManager = new UserManager();
            $user = $UserManager->getUserByPseudo($_GET['userName']);
            $SchoolManager = new SchoolManager();
            $school = $SchoolManager->getSchoolByName($_GET['schoolName']);
            if ($user && $school && !$user->getIsAdmin() && $user->getSchool() === $school->getName() && $school->getName() === $_SESSION['school'] && $_SESSION['grade'] === ADMIN) {
                $HistoryManager = new HistoryManager();
                // nb active account - 1
                if (!$user->getIsModerator() && $user->getIsActive() && $school->getIsActive()) {
                    $SchoolManager->updateByName($school->getName(), 'nbActiveAccount', $school->getNbActiveAccount() - 1);
                }
                // edit User info
                $UserManager->updateById($user->getId(), 'school', NO_SCHOOL)
                    ->updateById($user->getId(), 'isActive', false, true)
                    ->updateById($user->getId(), 'isAdmin', false, true)
                    ->updateById($user->getId(), 'isModerator', false, true)
                    ->updateById($user->getId(), 'schoolGroup', null);
                // add school history entry
                $HistoryManager->addEntry(new HistoryEntry(
                    ['idSchool' => $school->getId(), 
                    'category' => 'account', 
                    'entry' => $user->getFirstName() . ' ' . $user->getLastName() . ' a quitté l\'établissement'])
                );
                $this->redirection();
            } else {
                $this->incorrectInformation();
            }
        } else {
            $this->incorrectInformation();
        }
    }

    public function schoolProfile()
    {
        if (!empty($_GET['school']) && ($_GET['school'] === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL)) {
            $SchoolManager = new SchoolManager();
            $UserManager = new UserManager();
            $school = $SchoolManager->getSchoolByName($_GET['school']);
            $user = $UserManager->getOneById($_SESSION['id']);
            $userIsActive = $user->getIsActive();
            $contractInfo = $this->getSchoolContractInfo($school, new ContractManager('school', $SchoolManager), true);
            $ProfileContentManager = new ProfileContentManager();
            $profileContent = $ProfileContentManager->getByProfileId($school->getId(), true);
            if (!$user->getIsAdmin() || !$user->getIsActive()) {
                $view = 'frontend';
            } else {
                $view = 'backend';
            }
            RenderView::render('template.php', $view . '/schoolProfileView.php', 
                ['school' => $school, 'profileContent' => $profileContent, 'contractInfo' => $contractInfo, 
                'userIsActive' => $userIsActive, 'option' => ['schoolProfile', 'tinyMCE']]);
        } else {
            $this->incorrectInformation();
        }
    }

    public function updateProfile()
    {
        $SchoolManager = new SchoolManager();
        if (!empty($_GET['school']) && !empty($_GET['elem']) && ($_GET['school'] === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL)) {
            $school = $SchoolManager->getSchoolByName($_GET['school']);
            switch ($_GET['elem']) {
                case 'profileBanner' :
                    $this->updateProfileBanner($school, $SchoolManager);
                break;

                case 'profilePicture' :
                    $this->updateProfilePicture($school, $SchoolManager);
                break;

                case 'profileText' :
                    $this->updateProfileText($SchoolManager);
                break;

                case 'content' :
                    $ProfileContentManager = new ProfileContentManager();
                    $ProfileContentManager->updateProfileContent($_POST, intval($school->getId()), true);
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
            $arrAcceptedExtention = array("jpeg", "jpg", 'jfif', "png", "gif");
            require 'view/upload.php';

            $SchoolManager = new SchoolManager();
            if (!empty($_GET['school']) && $_SESSION['school'] === ALL_SCHOOL) {
                $school = $SchoolManager->getSchoolByName($_GET['school']);
            } else {
                $school = $SchoolManager->getSchoolByName($_SESSION['school']);
            }

            if (!empty($final_path)) {
                switch ($_GET['elem']) {
                    case 'banner' :
                        $this->uploadBanner($_GET, $school, $final_path);
                    break;

                    case 'picture' :
                        $this->uploadProfilePicture($_GET, $school, $final_path);
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

    public function addSchoolPost()
    {
        $SchoolManager = new SchoolManager();
        $UserManager = new UserManager();
        $PostsManager = new PostsManager();
        $school = $SchoolManager->getSchoolByName($_SESSION['school']);
        $user = $UserManager->getUserByPseudo($_SESSION['pseudo']);
        if ($_SESSION['school'] !== ALL_SCHOOL && $school && $user && $user->getIsActive()) {
            if (!empty($_GET['folder'])) {
                $this->addSchoolPostOnFolder($PostsManager, $user, $school);
            } else {
                $authorizedGroups = $PostsManager->getListAuthorizedGroups($school->getListSchoolGroups());
                RenderView::render(
                    'template.php', 'backend/addSchoolPostView.php', 
                    ['groups' => $authorizedGroups, 'option' => ['addPost', 'tinyMCE']]
                );
            }
        } elseif (!$user->getIsActive()) {
            $this->error('Vous ne pouvez pas faire de publication sur le profil de votre établissement car il n\'est pas actif');
        } else {
            $this->incorrectInformation();
        }
    }

    public function tryUploadSchoolPost()
    {
        $SchoolManager = new SchoolManager();
        $UserManager = new UserManager();
        $PostsManager = new PostsManager();
        $arrAcceptedValues = ['onSchoolProfile', 'private'];
        $school = $SchoolManager->getSchoolByName($_SESSION['school']);

        //check listGroup (list authorized group is only for private post by admin / moderator)
        if (empty($_POST['listAuthorizedGroups'])) {
            $_POST['listAuthorizedGroups'] = null;
        }

        if (isset($_GET['type']) && in_array($_GET['type'], $arrAcceptedValues) && $user = $UserManager->getOneById($_SESSION['id'])) {
            if ($response = $PostsManager->canUploadPost($_GET['type'], $user, $_POST, new TagsManager(), $SchoolManager)) {

                if ($response['fileTypeValue'] === 'grouped') {
                    $GroupedPostsManager = new GroupedPostsManager();
                    if ($GroupedPostsManager->uploadPost($response, $school->getId(), true)) {
                        header('Location: indexAdmin.php?action=schoolProfile&school=' . $_SESSION['school']);
                    } else {
                        throw new \Exception("Le fichier n'est pas conforme");
                    }
                } else {
                    if ($PostsManager->uploadPost($response, $school->getId(), true)) {
                        header('Location: indexAdmin.php?action=schoolProfile&school=' . $_SESSION['school']);
                    } else {
                        throw new \Exception("Le fichier n'est pas conforme");
                    }
                }
            } else {
                $this->incorrectInformation();
            }
        } else {
            $this->accessDenied();
        }
    }

    public function manageForum()
    {
        if (!empty($_SESSION['school']) && !empty($_GET['school']) && $_SESSION['school'] !== NO_SCHOOL 
        && ($_SESSION['school'] === ALL_SCHOOL || $_SESSION['school'] === $_GET['school']))
        {
            $SchoolManager = new SchoolManager();
            $school = $SchoolManager->getSchoolByName($_GET['school']);

            if ($_GET['school'] === ALL_SCHOOL && $_SESSION['school'] === ALL_SCHOOL) {
                RenderView::render('template.php', 'frontend/forumViewWebM.php', ['schools' => $school]);
            } else {
                if ($school && ($school->getIsActive() || $_SESSION['school'] === ALL_SCHOOL)) {
                    $UserManager = new UserManager();
                    $user = $UserManager->getOneById($_SESSION['id']);

                    if ($user && $user->getIsActive()) {
                        if ($user->getIsAdmin() || $user->getIsModerator()) {
                            $ForumCategoryManager = new ForumCategoryManager();
                            $forumInfo = $ForumCategoryManager->getCategories($school->getId(), $user, true, true, false);
    
                            RenderView::render(
                                'template.php', 'backend/manageForumView.php', 
                                [
                                    'option' => ['manageForum'], 
                                    'user' => $user, 'school' => $school, 'listSchoolGroups' => $school->getListSchoolGroups(), 'forumInfo' => $forumInfo
                                ]);
                        } else {
                            $this->accessDenied();
                        }
                    } else {
                        $this->incorrectInformation("Le forum n'est pas accessible car l'abonnement de votre compte utilisateur est désactivé");
                    }
                } else {
                    $this->incorrectInformation("Le forum n'est pas accessible car l'abonnement de l'établissement scolaire est désactivé");
                }
            }
        } else {
            $this->incorrectInformation();
        }
    }

    public function deleteCategory()
    {
        if (!empty($_GET['idCategory']) && !empty($_SESSION['id'])) {
            $SchoolManager = new SchoolManager();
            $school = $SchoolManager->getSchoolByName($_SESSION['school']);
            $UserManager = new UserManager();
            $user = $UserManager->getOneById($_SESSION['id']);
            $ForumCategoryManager = new ForumCategoryManager();
            $categoryInfo = $ForumCategoryManager->getCategory(intval($_GET['idCategory']), $user);

            if ($categoryInfo && !empty($categoryInfo['category']) && $_SESSION['grade'] === "admin" 
            && ($_SESSION['school'] === ALL_SCHOOL || $school->getId() === $categoryInfo['category']->getIdSchool()))
            {
                if ($_SESSION['school'] === ALL_SCHOOL) {
                    $school = $SchoolManager->getOneById($categoryInfo['category']->getIdSchool());
                }

                $ForumCategoryManager->deleteCategory($categoryInfo, $categoryInfo['category']->getIdSchool());
                header('Location: indexAdmin.php?action=manageForum&school=' . $school->getName());
            } else {
                $this->accessDenied();
            }
        } else {
            $this->incorrectInformation();
        }
    }

    public function editCategory()
    {
        if (!empty($_POST['idCategory']) && !empty($_SESSION['id']) && !empty($_POST['title']) && $this->checkForScriptInsertion($_POST)) {
            $SchoolManager = new SchoolManager();
            $school = $SchoolManager->getSchoolByName($_SESSION['school']);
            $UserManager = new UserManager();
            $user = $UserManager->getOneById($_SESSION['id']);
            $ForumCategoryManager = new ForumCategoryManager();
            $category = $ForumCategoryManager->getCategory(intval($_POST['idCategory']), $user, false);

            if (!empty($category) && ($_SESSION['school'] === ALL_SCHOOL || $school->getId() === $category->getIdSchool())) {
                if ($_SESSION['school'] === ALL_SCHOOL) {
                    $school = $SchoolManager->getOneById($category->getIdSchool());
                }

                $authorizedGroupsToSee = $ForumCategoryManager->getAuthorizedGroupsFromFormForCategory($_POST['editedAuthorizedGroupsToSee'],$_POST['listEditedAuthorizedGroupsToSee']);
                $authorizedGroupsToPost = $ForumCategoryManager->getAuthorizedGroupsFromFormForCategory($_POST['editedAuthorizedGroupsToPost'], $_POST['listEditedAuthorizedGroupsToPost']);

                $ForumCategoryManager->updateCategory($_POST['idCategory'], $_POST['title'], $_POST['content'], $authorizedGroupsToSee, $authorizedGroupsToPost);
                header('Location: indexAdmin.php?action=manageForum&school=' . $school->getName());
            }
        }
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- FUNCTION AJAX ------------------------------------
    -------------------------------------------------------------------------------------*/

    /*------------------------------ moderat website ------------------------------*/
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
            $response = [];
            $ProfileContentManager = new ProfileContentManager();
            // delete unused img on profileContent
            $response['profileContent'] = $ProfileContentManager->deleteUnusedImg();
            // delete unused img on temp folder
            $response['tempFolder'] = $this->deleteImgOnTempFolder();
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

    public function addWarning()
    {
        $UserManager = new UserManager();
        if (!empty($_SESSION) && $_SESSION['school'] === ALL_SCHOOL && isset($_POST['idUser'])) {
            $WarningManager = new WarningManager();
            $user = $UserManager->getOneById(intval($_POST['idUser']));
            $WarningManager->warn($user, $_POST['reasonWarn'], $UserManager);
            echo 'true';
        } else {
            echo 'false';
        }
    }

    /*------------------------------ school history ------------------------------*/
    public function getSchoolHistory()
    {
        if (!empty($_GET['school']) && ($_SESSION['school'] === ALL_SCHOOL || (!empty($_GET['schoolName']) && $_GET['schoolName'] === $_SESSION['school']))) {
            $HistoryManager = new HistoryManager();
            $SchoolManager = new SchoolManager();
            if ($SchoolManager->exists(intval($_GET['school']))) {
                echo json_encode($HistoryManager->getSchoolHistory($_GET));
            } else {
                echo 'false';
            }
        } else {
            echo 'false';
        }
    }

    /*------------------------------ report ------------------------------*/
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
        $arrAcceptedValue = array('profile', 'post', 'comment', 'other');
        if (!empty($_GET['elem']) && in_array($_GET['elem'], $arrAcceptedValue)) {
            $ReportManager = new ReportManager();
            echo json_encode($ReportManager->getCount($_GET['elem']));
        } else {
            echo 'false';
        }
    }

    public function deleteReport()
    {
        $ReportManager = new ReportManager();
        $arrAcceptedValue = array('profile', 'post', 'comment', 'other');
        if (!empty($_GET['elem']) && in_array($_GET['elem'], $arrAcceptedValue) 
        && !empty($_GET['idElem']) && intval($_GET['idElem']) > 0 && isset($_GET['idUser'])) {
            $ReportManager->deleteReport($_GET['elem'], $_GET['idElem'], $_GET['idUser']);
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function deleteReportsFromElem()
    {
        $ReportManager = new ReportManager();
        $arrAcceptedValue = array('profile', 'post', 'comment');
        if (!empty($_GET['elem']) && in_array($_GET['elem'], $arrAcceptedValue) && !empty($_GET['idElem']) && intval($_GET['idElem']) > 0) {
            $ReportManager->deleteReportsFromElem($_GET['elem'], $_GET['idElem']);
            echo 'true';
        } else {
            echo 'false';
        }
    }

    /*------------------------------ school groups ------------------------------*/
    public function createGroup()
    {
        if (!empty($_GET['group']) && trim(strtolower($_GET['group'])) !== 'none' && !empty($_GET['schoolName']) 
        && ($_GET['schoolName'] === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL)) {
            $SchoolManager = new SchoolManager();
            echo $SchoolManager->createGroup($_GET);
        } else {
            echo 'false';
        }
    }

    public function getGroup()
    {
        if (!empty($_GET['schoolName']) && ($_GET['schoolName'] === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL)) {
            $SchoolManager = new SchoolManager();
            if ($school = $SchoolManager->getSchoolByName($_GET['schoolName'])) {
                echo json_encode($school->getListSchoolGroups());
            } else {
                echo 'false';
            }
        }
    }

    public function setGroup()
    {
        if (!empty($_GET['userName']) && !empty($_GET['group'])) {
            $SchoolManager = new SchoolManager();
            $UserManager = new UserManager();
            $user = $UserManager->getUserByPseudo($_GET['userName']);
            if ($user && ($user->getSchool() === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL) 
            && $SchoolManager->setGroup($_GET, $user, $UserManager)) {
                echo 'true';
            } else {
                echo 'false';
            }
        } else {
            echo 'false';
        }
    }

    public function setNewCategory()
    {
        if (!empty($_SESSION['school']) && !empty($_POST['schoolName']) && !empty($_POST['newCategoryName']) && $this->checkForScriptInsertion($_POST)
        && ($_SESSION['school'] === $_POST['schoolName'] || $_SESSION['school'] === ALL_SCHOOL))
        {
            $SchoolManager = new SchoolManager();
            $ForumCategoryManager = new ForumCategoryManager();
            $authorizedGroupsToSee = $ForumCategoryManager->getAuthorizedGroupsFromFormForCategory($_POST['authorizedGroupsToSee'],$_POST['listAuthorizedGroupsToSee']);
            $authorizedGroupsToPost = $ForumCategoryManager->getAuthorizedGroupsFromFormForCategory($_POST['authorizedGroupsToPost'], $_POST['listAuthorizedGroupsToPost']);
            
            if ($school = $SchoolManager->getSchoolByName($_POST['schoolName'])) {
                echo $ForumCategoryManager->setCategory($school->getId(), $_POST['newCategoryName'], $_POST['newCategoryDescription'], $authorizedGroupsToSee, $authorizedGroupsToPost);
            } else {
                echo 'false';
            }
        } else {
            echo 'false';
        }
    }

    public function changCategoryOrder()
    {
        $acceptedValue = ['up', 'down'];

        if (!empty($_GET['schoolName']) && !empty($_GET['value']) && in_array($_GET['value'], $acceptedValue) && !empty($_GET['currentOrder'])
        && ($_GET['schoolName'] === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL)) 
        {
            $SchoolManager = new SchoolManager();
            $school = $SchoolManager->getSchoolByName($_GET['schoolName']);
            $ForumCategoryManager = new ForumCategoryManager();

            echo $ForumCategoryManager->changCategoryOrder($_GET['value'], $school->getId(), intval($_GET['currentOrder']));
        } else {
            echo 'false';
        }
    }

    public function changTopicOrder()
    {
        ['user' => $user, 'school' => $school] = $this->accessTheForum();
        $acceptedValue = ['up', 'down'];

        if ($user && $school && !empty($_GET['idCategory']) && !empty($_GET['value']) 
        && in_array($_GET['value'], $acceptedValue) && !empty($_GET['currentOrder'])) 
        {
            $ForumCategoryManager = new ForumCategoryManager();
            $category = $ForumCategoryManager->getCategory($_GET['idCategory'], $user, false);

            if ($category && ($category->getIdSchool() === $school->getId() || $_SESSION['school'] === ALL_SCHOOL)) {
                echo $ForumCategoryManager->changTopicOrder($_GET['value'], $category->getId(), intval($_GET['currentOrder']));
            } else {
                echo 'false';
            }
        } else {
            echo 'false';
        }
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- FUNCTION PRIVATE ------------------------------------
    -------------------------------------------------------------------------------------*/

    private function addSchoolPostOnFolder(PostsManager $PostsManager, User $user, School $school)
    {
        $folder = $PostsManager->getOneById($_GET['folder']);
        if ($folder && $folder->getPostType() === "schoolPost" && $PostsManager->canPostOnFolder($folder, $user, $school)) {
            if (!$folder->getIsPrivate()) {
                // post on folder on profile
                RenderView::render(
                    'template.php', 'backend/addSchoolPostOnPublicFolderView.php', 
                    ['option' => ['addPost', 'tinyMCE']]
                ); 
            } elseif ($folder->getIsPrivate() && $user->getIsActive()) {
                // post on private folder
                $authorizedGroups = $PostsManager->getListAuthorizedGroups($school->getListSchoolGroups(), $folder->getListAuthorizedGroups(), true);
                RenderView::render(
                    'template.php', 'backend/addSchoolPostOnPrivateFolderView.php', 
                    ['groups' => $authorizedGroups, 'option' => ['addPost', 'tinyMCE']]
                );
            }
        } else {
            $this->incorrectInformation();
        }
    }

    /*------------------------------ images upload ------------------------------*/
    private function uploadBanner(array $GET, School $school, string $finalPath)
    {
        $validBannerValue = array('true', 'false');
        if ($school && !empty($GET['noBanner']) && in_array($GET['noBanner'], $validBannerValue)) {
            $SchoolManager = new SchoolManager();
            $this->deleteFile($school->getProfileBanner());
            $infos = $finalPath . ' ' . $GET['noBanner'];
            $SchoolManager->updateByName($school->getName(), 'profileBannerInfo', $infos);
        } else {
            $this->incorrectInformation();
        }
    }

    private function uploadProfilePicture(array $GET, School $school, string $finalPath)
    {
        $validSizeValue = array('smallPicture', 'mediumPicture', 'bigPicture');
        if ($school && !empty($GET['size']) && in_array($GET['size'], $validSizeValue)) {
            $SchoolManager = new SchoolManager();
            $this->deleteFile($school->getProfilePicture());
            $infos = $finalPath . ' ' . $GET['size'];
            $SchoolManager->updateByName($school->getName(), 'profilePictureInfo', $infos);
        } else {
            $this->incorrectInformation();
        }
    }

    /*------------------------------ update profile ------------------------------*/
    private function updateProfileBanner(School $school, SchoolManager $SchoolManager)
    {
        if (isset($_GET['noBanner'], $_GET['value'])) {
            if (strpos($_GET['value'], $school->getProfileBanner()) === false) {
                $this->deleteFile($school->getProfileBanner());
            }
            
            $infos = $_GET['value'] . ' ' . $_GET['noBanner'];
            $SchoolManager->updateByName($_GET['school'], 'profileBannerInfo', $infos);
        } else {
			$this->incorrectInformation();
        }
    }

    private function updateProfilePicture(School $school, SchoolManager $SchoolManager)
    {
        if (isset($_GET['size'], $_GET['value'])) {
            if (strpos($_GET['value'], $school->getProfilePicture()) === false) {
                $this->deleteFile($school->getProfilePicture());
            }
            $infos = $_GET['value'] . ' ' . $_GET['size'];
            $SchoolManager->updateByName($_GET['school'], 'profilePictureInfo', $infos);
        } else {
			$this->incorrectInformation();
        }
    }

    private function updateProfileText(SchoolManager $SchoolManager)
    {
        if (isset($_GET['block'], $_GET['school'], $_GET['schoolPos'])) {
            $infos = $_GET['block'] . ' ' . $_GET['schoolPos'];
            $SchoolManager->updateByName($_GET['school'], 'profileTextInfo', $infos);
        } else {
			$this->incorrectInformation();
        }
    }

    /*------------------------------ moderat admin ------------------------------*/
    private function adminCreateNewModerator(UserManager $UserManager, SchoolManager $SchoolManager)
    {
        if ($_SESSION['school'] === $_POST['schoolName'] || $_SESSION['school'] === ALL_SCHOOL) {
            $arrCanAddModerator = $SchoolManager->canAddModerator($_POST, $UserManager);
            if ($arrCanAddModerator['canAdd']) {
                return $SchoolManager->addModerator($_POST, $UserManager, new HistoryManager());
            } else {
                return $arrCanAddModerator['message'];
            }
        } else {
            $this->accessDenied();
        }
    }

    /*------------------------------ report view ------------------------------*/
    private function getIdElemForRedirection(string $elem, int $idElem)
    {
        switch ($elem) {
            case 'profile':
                return $idElem;
            break;

            case 'post':
                return $idElem;
            break;

            case 'comment':
                $CommentsManager = new CommentsManager();
                if ($comment = $CommentsManager->getOneById($idElem)) {
                    return $comment->getIdPost();
                } else {
                    $this->incorrectInformation();
                }
            break;

            default :
                $this->incorrectInformation();
        }
    }

    /*-------------------------- moderat website ---------------------------*/
    private function deleteImgOnTempFolder()
    {
        $nbDeletedFile = 0;
        $dir = 'public/images/temp';
        if (is_dir($dir) && $folder = opendir($dir)) {
            while (($file = readdir($folder)) !== false) {
                $filePath = $dir . '/' . $file;
                if (!is_dir($filePath)) {
                    ['atime' => $atime] = stat($filePath);
                    if ((time() - $atime) >= 3600) {
                        $this->deleteFile($filePath);
                        $nbDeletedFile++;
                    }
                }
            }
            closedir($folder);
            return $nbDeletedFile;
        } else {
            return 'fail to open the folder';
        }
    }
}
