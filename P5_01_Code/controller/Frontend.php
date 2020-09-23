<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\Controller;

class Frontend extends Controller
{
    public static $SIDE = 'frontend';
    public static $INDEX = 'index.php';

    /*-------------------------------------------------------------------------------------
    ----------------------------------- FUNCTION PUBLIC ------------------------------------
    -------------------------------------------------------------------------------------*/

    public function verifyInformation()
    {
        if (isset($_SESSION['pseudo'])) {
            //user is connect, verify SESSION info
            $SchoolManager = new SchoolManager();
            $UserManager = new UserManager();
            if ((!$SchoolManager->nameExists($_SESSION['school']) && !($_SESSION['school'] === ALL_SCHOOL)) || !$UserManager->nameExists($_SESSION['pseudo'])) {
                //if user name don't exist or if school name don't exist and isn't "allSchool"
                $this->forceDisconnect();
            } else {
                //user exist, check his group and if user is ban
                $user = $UserManager->getUserByName($_SESSION['pseudo']);
                if ($user->getIsBan()) {
                    $this->disconnect();
                } elseif ($_SESSION['group'] !== $user->getSchoolGroup()) {
                    $this->forceDisconnect();
                }
            }
        } elseif (isset($_COOKIE['artSchoolsId']) || isset($_COOKIE['artSchoolsAdminId'])) {
            //user is not connect but there is a cookie to sign in
            $this->useCookieToSignIn();
        }
    }

    public function home()
    {
        //check url
        $url = explode('/', $_SERVER['PHP_SELF']);
        $url = $url[count($url) - 1];
        if ($url !== static::$INDEX) {
            //$url is school name or user name, try to redirect to school/user profile
            $this->homeRedirection($url);
        } else {
            //home
            $PostsManager = new PostsManager();
            $posts = $PostsManager->getPostsForHome(new SchoolManager(), new TagsManager());
            RenderView::render('template.php', 'frontend/indexView.php', ['posts' => $posts, 'option' => ['home']]);    
        }
    }

    public function signUp()
    {
        if (empty($_SESSION)) {
            $message = null;
            if (!empty($_POST)) {
                $UserManager = new UserManager();
                $canSignUp = $this->canSignUp($_POST, $UserManager);
                if ($canSignUp['value']) {
                    $message = $UserManager->signUp($_POST, new SchoolManager(), new HistoryManager());
                } else {
                    $message = $canSignUp['msg'];
                }
            }
            RenderView::render('template.php', 'frontend/signUpView.php', ['option' => ['signUp'], 'message' => $message]);
        } else {
			header('Location: index.php');
        }
    }

    public function signIn()
    {
        if (empty($_SESSION)) {
            $message = null;
            if (!empty($_POST['ConnectPseudo']) && !empty($_POST['ConnectPassword'])) {
                // user try to connect
                !empty($_POST['stayConnect']) ? $stayConnect = $_POST['stayConnect'] : $stayConnect = null;
                $message = $this->tryToConnect($_POST['ConnectPseudo'], $_POST['ConnectPassword'], new UserManager(), false, $stayConnect);
            } else if (isset($_POST['postMail'])) {
                // user try to get back his password
                $message = $this->tryRecoverPassword($_POST['postMail'], new UserManager());
            }
            RenderView::render('template.php', 'frontend/signInView.php', ['option' => ['forgetPassword', 'signIn'], 'message' => $message]);
        } else {
			header('Location: index.php');
        }
    }

    public function resetPassword()
    {
        $UserManager = new UserManager();
        $message = null;
        if (!empty($_GET['key']) && !empty($_GET['id'])) {
            // form to reset password
            if ($user = $UserManager->getOneById($_GET['id'])) {
                if ($user->getTemporaryPassword() === $_GET['key'] && $user->getBeingReset()) {
                    // account being reset and key is Ok
                    if (!empty($_GET['wrongPassword'])) {
                        $message = $UserManager->checkWrongPasswordMessage($_GET['wrongPassword']);
                    }
                    RenderView::render('template.php', 'frontend/resetPasswordView.php', ['user' => $user, 'message' => $message]);
                } else {
					$this->invalidLink();
                }
            } else {
				$this->incorrectInformation();
            }
        } else if (isset($_POST['newPassword']) && isset($_POST['confirmNewPassword'])) {
            // check form data
            $this->checkFormResetPassword($UserManager);
        } else {
			$this->invalidLink();
        }    
    }

    public function settings()
    {
        if (!empty($_SESSION)) {
            $UserManager = new UserManager();
            $user = $UserManager->getUserByName($_SESSION['pseudo']);
            $contractInfo = $this->getUserContractInfo($user, new ContractManager('user', $UserManager));
            RenderView::render('template.php', 'frontend/settingsView.php', ['user' => $user, 'contractInfo' => $contractInfo]);
        } else {
            $this->redirection('index.php?action=signUp');
        }
    }

    public function search()
    {
        if (!empty($_POST['keyWord'])) {
            // search by key word
            $result = $this->searchForKeyWord($_POST['keyWord']);
            RenderView::render('template.php', 'frontend/searchView.php', ['result' => $result]);
        } elseif (!empty($_GET['sortBy'])) {
            // search sort by last posted / most liked / post on school $school / tag $tag
            $result = $this->searchSortBy();
            RenderView::render(
                'template.php', 'frontend/searchView.php', 
                ['nbPage' => $result['nbPage'], 'nbPostsByPage' => $result['nbPostsByPage'], 'items' => $result['items']]
            );
        } else {
			RenderView::render('template.php', 'frontend/searchView.php');
        }
    }

    public function advancedSearch()
    {
        if (empty($_POST)) {
            $SchoolManager = new SchoolManager();
            $schools = $SchoolManager->getSchoolByName(ALL_SCHOOL);
            RenderView::render('template.php', 'frontend/advancedSearchView.php', ['schools' => $schools, 'option' => ['advancedSearch']]);
        } else {
            $PostsManager = new PostsManager();
            $nbPostsByPage = 12;
            $posts = $PostsManager->advancedSearch($_POST, $nbPostsByPage);
            $nbPage = ceil($posts['count'] / $nbPostsByPage);
            RenderView::render(
                'template.php', 'frontend/advancedSearchView.php', 
                ['nbPage' => $nbPage, 'nbPostsByPage' => $nbPostsByPage, 'posts' => $posts, 'option' => ['advancedSearch']]
            );
        }
    }

    public function listTags()
    {
        $TagsManager = new TagsManager();
        $tags = $TagsManager->sortByAlphabeticalOrder($TagsManager->get(null, 0, 'name'));
        RenderView::render('template.php', 'frontend/listTagsView.php', ['tags' => $tags]);
    }

    public function listSchools()
    {
        $SchoolManager = new SchoolManager();
        $schools = $SchoolManager->getSchoolByName(ALL_SCHOOL);
        RenderView::render('template.php', 'frontend/listSchoolsView.php', ['schools' => $schools, 'option' => ['listSchools']]);
    }

    public function userProfile()
    {
        if (!empty($_GET['userId'])) {
            $UserManager = new UserManager();
            if ($user = $UserManager->getOneById($_GET['userId'])) {
                if ($user->getSchool() !== ALL_SCHOOL) {
                    $ProfileContentManager = new ProfileContentManager();
                    $profileContent = $ProfileContentManager->getByProfileId($user->getId());
                    RenderView::render(
                        'template.php', 'frontend/userProfileView.php', 
                        ['user' => $user, 'profileContent' => $profileContent, 'option' => ['userProfile', 'tinyMCE']]
                    );
                } else {
                    $this->incorrectInformation();
                }
            } else {
				$this->invalidLink();
            }
        } else {
			$this->incorrectInformation();
        }
    }

    public function schoolProfile()
    {
        if (!empty($_GET['school']) && $_GET['school'] !== ALL_SCHOOL && $_GET['school'] !== NO_SCHOOL) {
            $SchoolManager = new SchoolManager();
            if ($school = $SchoolManager->getSchoolByName($_GET['school'])) {
                $ProfileContentManager = new ProfileContentManager();
                $profileContent = $ProfileContentManager->getByProfileId($school->getId(), true);
                $contractInfo = $this->getSchoolContractInfo($school, new ContractManager('school', $SchoolManager), true);
                RenderView::render(
                    'template.php', 'frontend/schoolProfileView.php', 
                    ['school' => $school, 'profileContent' => $profileContent, 'contractInfo' => $contractInfo, 'option' => ['schoolProfile']]);
            } else {
				$this->invalidLink();
            }
        } else {
			$this->incorrectInformation();
        }
    }

    public function updateProfile()
    {
        $UserManager = new UserManager();
        if (!empty($_GET['userId']) && !empty($_GET['elem']) && intval($_GET['userId']) === $_SESSION['id'] 
        && $user = $UserManager->getOneById(intval($_GET['userId']))) {
            switch ($_GET['elem']) {
                case 'profileBanner' :
                    $this->updateProfileBanner($user, $UserManager);
                    break;
                case 'profilePicture' :
                    $this->updateProfilePicture($user, $UserManager);
                    break;
                case 'profileText' :
                    $this->updateProfileText($UserManager);
                    break;
                case 'content' :
                    $ProfileContentManager = new ProfileContentManager();
                    $ProfileContentManager->updateProfileContent($_POST);
                    //$this->redirection();
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
            $UserManager = new UserManager();
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

    public function post()
    {
        $UserManager = new UserManager();
        $PostsManager = new PostsManager();
        !empty($_SESSION) ? $user = $UserManager->getOneById($_SESSION['id']) : $user = null;
        if (!empty($_GET['id']) && $post = $PostsManager->getOneById($_GET['id'])) {
            if ($PostsManager->userCanSeePost($user, $post)) {
                $asidePosts = $PostsManager->getAsidePosts($post, new TagsManager());
                $UserManager->exists($post->getIdAuthor()) ? $author = $UserManager->getOneById($post->getIdAuthor()) : $author = null;
                if ($post->getIsPrivate()) {
                    $this->privatePost($post, $asidePosts, $user, $author);
                } else {
                    $this->publicPost($post, $asidePosts, $user, $author);
                }
            } else {
                $this->accessDenied();
            }  
        } else {
			$this->invalidLink();
        }
    }

    public function addPost()
    {
        if (!empty($_SESSION['id']) && $_SESSION['school'] !== ALL_SCHOOL) {
            if (!empty($_GET['folder'])) {
                // add post on folder
                $this->addPostOnFolder(new PostsManager());
            } else {
                // add public post
				RenderView::render('template.php', 'frontend/addPostView.php', ['option' => ['addPost', 'tinyMCE']]);
            }
        } else {
			$this->accessDenied();
        }
    }

    public function uploadPost()
    {
        $PostsManager = new PostsManager();
        $TagsManager = new TagsManager();
        if (isset($_SESSION['id'], $_POST)) {
            if ($response = $PostsManager->canUploadPost($_POST, $TagsManager)) {
                if ($_POST['postType'] === 'schoolPost') {
                    $this->uploadOnSchoolFolder($response, $PostsManager);
                } else {
                    $this->uploadUserPost($response, $PostsManager, $TagsManager);
                }
            } else {
				$this->incorrectInformation();
            }
        } else {
			$this->accessDenied();
        }
    }

    public function deletePost()
    {
        $PostsManager = new PostsManager();
        $TagsManager = new TagsManager();
        if (isset($_GET['id'], $_SESSION['id']) && $post = $PostsManager->getOneById($_GET['id'])) {
            if ($post->getIdAuthor() === intval($_SESSION['id']) || $_SESSION['school'] === ALL_SCHOOL) {
                if ($post->getFileType() === 'folder') {
                    $PostsManager->deleteFolder($post->getId());
                } else {
                    $PostsManager->deletePost($post->getId());
                }
                //at the same time, delete unused tags
                $TagsManager->deleteUnusedTags();
                if ($_SESSION['school'] === ALL_SCHOOL) {
                    header('Location: index.php');
                } elseif ($post->getPostType() === 'schoolPost') {
                    header('Location: index.php?action=schoolProfile&school=' . $_SESSION['school']);
                } else {
					header('Location: index.php?action=userProfile&userId=' . $_SESSION['id']);
                }
            } else {
				$this->accessDenied();
            }
        } else {
			$this->incorrectInformation();
        }
    }

    public function report()
    {
        if (!empty($_SESSION) && !empty($_GET['elem'])) {
            switch ($_GET['elem']) {
                case 'post' :
                    $this->reportPost($_GET['id']);
                    break;
                case 'comment' :
                    $this->reportComment($_GET['id']);
                    break;
                case 'other' :
                    $this->reportOther();
                break;
                default : 
                    $this->incorrectInformation();
            }
        } else {
			$this->incorrectInformation();
        }
    }

    public function setReport()
    {
        $arrAcceptedElem = array('post', 'comment', 'other');
        if (!empty($_SESSION) && !empty($_POST['elem']) && in_array($_POST['elem'], $arrAcceptedElem) 
        && !empty($_POST['tinyMCEtextarea'])) {
            $ReportManager = new ReportManager();
            !empty($_POST['idElem']) ? $idElem = intval($_POST['idElem']) : $idElem = null;
            $ReportManager->setReport($_POST['elem'], $_POST['tinyMCEtextarea'], $idElem, $_SESSION['id']);
            if (!empty($_POST['idPost']) && intval($_POST['idPost']) > 0) {
                header('Location: index.php?action=post&id=' . $_POST['idPost']);
            } else {
				$this->redirection('index.php', true);
            }
        } else {
			$this->incorrectInformation();
        }
    }

    public function faq()
    {
        RenderView::render('template.php', 'frontend/faqView.php', ['option' => ['faq']]);
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- FUNCTION AJAX ------------------------------------
    -------------------------------------------------------------------------------------*/
    public function getTags()
    {
        $TagsManager = new TagsManager();
        $listTags = $TagsManager->get();
        $arrTags = [];
        if (!empty($listTags)) {
            for ($i=0; $i<count($listTags); $i++) {
                $arrTags[$i] = $listTags[$i]['name'];
            }
        }
        echo json_encode($arrTags);
    }

    public function getSchools()
    {
        $SchoolManager = new SchoolManager();
        $schools = $SchoolManager->getSchoolByName(ALL_SCHOOL);
        $arrSchoolName = [];
        foreach ($schools as $school) {
            if ($school->getName() !== NO_SCHOOL) {
                $arrSchoolName[] = $school->getName();
            }
        }
        echo json_encode($arrSchoolName);
    }

    public function getPostsBySchool()
    {
        $SchoolManager = new SchoolManager();
        $PostsManager = new PostsManager();
        if (!empty($_GET['school']) && $SchoolManager->nameExists($_GET['school'])) {
            empty($_GET['offset']) ? $offset = 0 : $offset = $_GET['offset'];
            empty($_GET['limit']) ? $limit = null : $limit = $_GET['limit'];
            empty($_GET['withFolder']) ? $withFolder = false : $withFolder = true;
            $posts = $PostsManager->getPostsBySchool($_GET['school'], $withFolder, $offset, $limit);
            if (count($posts) > 0) {
                echo json_encode($PostsManager->toArray($posts));
            } else {
				echo 'false';
            }
        } else {
			echo 'false';
        }
    }

    public function getUsersBySchool()
    {
        $SchoolManager = new SchoolManager();
        $UserManager = new UserManager();
        if (!empty($_GET['school']) && $SchoolManager->nameExists($_GET['school'])) {
            $users = $UserManager->getUsersBySchool($_GET['school']);
            if (count($users) > 0) {
                echo json_encode($UserManager->sortByGrade($users));
            } else {
				echo 'false';
            }
        } else {
			echo 'false';
        }
    }

    public function getUserPosts()
    {
        $UserManager = new UserManager();
        $PostsManager = new PostsManager();
        if (!empty($_GET['id']) && $UserManager->exists($_GET['id'])) {
            $posts = $PostsManager->getPostsByAuthor($_GET['id']);
            if (count($posts) > 0) {
                $arrSortedPosts = $PostsManager->sortForProfile($posts);
                echo json_encode($arrSortedPosts);
            } else {
				echo 'false';
            }
        } else {
			echo 'false';
        }
    }

    public function getSchoolPosts()
    {
        $SchoolManager = new SchoolManager();
        $PostsManager = new PostsManager();
        if (!empty($_GET['school']) && $SchoolManager->nameExists($_GET['school'])) {
            $posts = $PostsManager->getSchoolPosts($_GET['school']);
            if (count($posts) > 0) {
                $arrSortedPosts = $PostsManager->sortForProfile($posts);
                echo json_encode($arrSortedPosts);
            } else {
				echo 'false';
            }
        } else {
			echo 'false';
        }
    }

    public function getProfilePosts()
    {
        $PostsManager = new PostsManager();
        if (!empty($_GET['idFolder']) && intval($_GET['idFolder']) > 0) {
            $posts = $PostsManager->getPostsOnFolder(intval($_GET['idFolder']));
            if (count($posts) > 0) {
                echo json_encode($PostsManager->sortForProfile($posts));
            } else {
				echo json_encode($PostsManager->sortForProfile([]));
            }
        } else {
			echo 'false';
        }
    }

    public function getLastPosted()
    {
        if (!empty($_GET['limit']) && !empty($_GET['offset'])) {
            $PostsManager = new PostsManager();
            $lastPosts = $PostsManager->getLastPosted($_GET['limit'], $_GET['offset']);
            if (!empty($lastPosts)) {
                echo json_encode($PostsManager->toArray($lastPosts));
            } else {
				echo 'false';
            }
        } else {
			echo 'false';
        }
    }

    public function getMostLikedPosts()
    {
        if (!empty($_GET['limit']) && !empty($_GET['offset'])) {
            $PostsManager = new PostsManager();
            $mostLikedPosts = $PostsManager->getMostLikedPosts($_GET['limit'], $_GET['offset']);
            if (!empty($mostLikedPosts)) {
                echo json_encode($PostsManager->toArray($mostLikedPosts));
            } else {
				echo 'false';
            }
        } else {
			echo 'false';
        }
    }

    public function getPostsByTag()
    {
        $PostsManager = new PostsManager();
        $TagsManager = new TagsManager();
        if (!empty($_GET['tag']) && $TagsManager->exists($_GET['tag'])) {
            empty($_GET['offset']) ? $offset = 0 : $offset = $_GET['offset'];
            empty($_GET['limit']) ? $limit = null : $limit = $_GET['limit'];
            $posts = $PostsManager->getPostsByTag($_GET['tag'], $limit, $offset);
            if (count($posts) > 0) {
                echo json_encode($PostsManager->toArray($posts));
            } else {
				echo 'false';
            }
        } else {
			echo 'false';
        }
    }

    public function setComment()
    {
        $CommentsManager = new CommentsManager();
        $UserManager = new UserManager();
        if (isset($_POST['idPost'], $_SESSION['id']) && $_POST['idPost'] > 0 && !empty($_POST['commentContent'])) {
            $user = $UserManager->getOneById($_SESSION['id']);
            echo json_encode($CommentsManager->setComment($_POST, $user));
        } else {
			echo 'false';
        }
    }

    public function deleteComment()
    {
        $CommentsManager = new CommentsManager();
        if (isset($_GET['id'], $_SESSION['id']) && $comment = $CommentsManager->getOneById($_GET['id'])) {
            if ($comment->getIdAuthor() === $_SESSION['id'] || $_SESSION['school'] === ALL_SCHOOL) {
                $CommentsManager->delete($comment->getId());
                echo 'true';
            } else {
				echo 'false';
            }
        } else {
			echo 'false';
        }
    }

    public function userAlreadyLikePost()
    {
        $PostsManager = new PostsManager();
        if (!empty($_SESSION['id']) && !empty($_GET['idPost']) && $PostsManager->exists($_GET['idPost']) && $PostsManager->userAlreadyLikePost(intval($_SESSION['id']), intval($_GET['idPost']))) {
            echo 'true';
        } else {
			echo 'false';
        }
    }

    public function toggleLikePost()
    {
        $PostsManager = new PostsManager();
        if (!empty($_SESSION['id']) && !empty($_GET['idPost']) && $PostsManager->exists($_GET['idPost'])) {
            $PostsManager->toggleLikePost(intval($_SESSION['id']), intval($_GET['idPost']));
            echo 'true';
        } else {
			echo 'false';
        }
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- FUNCTION PRIVATE ------------------------------------
    -------------------------------------------------------------------------------------*/

    /*------------------------------ images upload ------------------------------*/
    private function uploadBanner(array $GET, string $finalPath)
    {
        $UserManager = new UserManager();
        $validBannerValue = array('true', 'false');
        if (!empty($GET['noBanner']) && in_array($GET['noBanner'], $validBannerValue) && $user = $UserManager->getOneById($_SESSION['id'])) {
            $this->deleteFile($user->getProfileBanner());
            $infos = $finalPath . ' ' . $GET['noBanner'];
            $UserManager->updateById($_SESSION['id'], 'profileBannerInfo', $infos);
        } else {
			$this->incorrectInformation();
        }
    }

    private function uploadProfilePicture(array $GET, string $finalPath)
    {
        $UserManager = new UserManager();
        $validOrientationValue = array('highPicture', 'widePicture');
        $validSizeValue = array('smallPicture', 'mediumPicture', 'bigPicture');
        if (!empty($GET['orientation']) && in_array($GET['orientation'], $validOrientationValue)
        && !empty($GET['size']) && in_array($GET['size'], $validSizeValue) && $user = $UserManager->getOneById($_SESSION['id'])) {
            if ($user->getProfilePicture() !== 'public/images/question-mark.png') {
                $this->deleteFile($user->getProfilePicture());
            }
            $infos = $finalPath . ' ' . $GET['orientation'] . ' ' . $GET['size'];
            $UserManager->updateById($_SESSION['id'], 'profilePictureInfo', $infos);
        } else {
			$this->incorrectInformation();
        }
    }

    /*------------------------------ update profile ------------------------------*/
    private function updateProfileBanner(User $user, UserManager $UserManager)
    {
        if (isset($_GET['noBanner'], $_GET['value'])) {
            if (strpos($_GET['value'], $user->getProfileBanner()) === false) {
                $this->deleteFile($user->getProfileBanner());
            }
            $infos = $_GET['value'] . ' ' . $_GET['noBanner'];
            $UserManager->updateById($_GET['userId'], 'profileBannerInfo', $infos);
        } else {
			$this->incorrectInformation();
        }
    }

    private function updateProfilePicture(User $user, UserManager $UserManager)
    {
        if (isset($_GET['orientation'], $_GET['size'], $_GET['value'])) {
            if (strpos($_GET['value'], $user->getProfilePicture()) === false) {
                $this->deleteFile($user->getProfilePicture());
            }
            $infos = $_GET['value'] . ' ' . $_GET['orientation'] . ' ' . $_GET['size'];
            $UserManager->updateById($_GET['userId'], 'profilePictureInfo', $infos);
        } else {
			$this->incorrectInformation();
        }
    }

    private function updateProfileText(UserManager $UserManager)
    {
        if (isset($_GET['block'], $_GET['pseudo'], $_GET['school'])) {
            $infos = $_GET['block'] . ' ' . $_GET['pseudo'] . ' ' . $_GET['school'];
            $UserManager->updateById($_GET['userId'], 'profileTextInfo', $infos);
        } else {
			$this->incorrectInformation();
        }
    }

    /*------------------------------ home / sign in ------------------------------*/
    private function homeRedirection(string $url)
    {
        $UserManager = new UserManager();
        $SchoolManager = new SchoolManager();
        if ($user = $UserManager->getUserByName($url)) {
            header('Location: ../index.php?action=userProfile&userId=' . $user->getId());
        } elseif ($SchoolManager->nameExists($url)) {
            header('Location: ../index.php?action=schoolProfile&school=' . $url);
        } else {
            header('Location: ../index.php');
        }
    }

    private function checkFormResetPassword(UserManager $UserManager)
    {
        if ($_POST['newPassword'] === $_POST['confirmNewPassword']) {
            if ($user = $UserManager->getOneById($_POST['id'])) {
                if ($user->getTemporaryPassword() === $_POST['key'] && $user->getBeingReset()) {
                    if (!password_verify($_POST['newPassword'], $user->getPassword())) {
                           //new password is correct
                           $UserManager->updateById($user->getId(), 'password', password_hash($_POST['newPassword'], PASSWORD_DEFAULT))
                                ->updateById($user->getId(), 'beingReset', false, true);
                           $message = "Le mot de passe a bien été modifié.";
                           RenderView::render('template.php', 'frontend/resetPasswordView.php', ['message' => $message]);
                    } else {
                        //new password is the same as the old one
                        header('Location: index.php?action=resetPassword&key=' . $_POST['key'] . '&id=' . $_POST['id'] . '&wrongPassword=2');
                    }
                } else {
                    $this->invalidLink();
                }
            } else {
                $this->incorrectInformation();
            }
        } else {
            header('Location: index.php?action=resetPassword&key=' . $_POST['key'] . '&id=' . $_POST['id'] . '&wrongPassword=1');
        }
    }

    /*------------------------------ sign up ------------------------------*/
    private function canSignUp(array $POST, UserManager $UserManager)
    {

        if ($UserManager->checkForScriptInsertion($_POST)) {
            if ($POST['confirmPassword'] === $POST['password']) {
                if (!$UserManager->nameExists($POST['signUpPseudo'])) {
                    if (!$UserManager->mailExists($POST['signUpMail'])) {
                        return ['value' => true];
                    } else {
                        return ['value' => false, 'msg' => "Cette adresse mail est déja lié a un compte"];
                    }
                } else {
                    return ['value' => false, 'msg' => "Ce nom d'utilisateur est déja utilisé"];
                }
            } else {
                return ['value' => false, 'msg' => "Vous devez entrer deux mot de passe identiques"];
            }
        } else {
            return ['value' => false, 'msg' => "Les informations renseignées sont incorrectes"];
        }
    }

    /*------------------------------ search ------------------------------*/
    private function searchSortBy(int $nbPostsByPage = 12)
    {
        $PostsManager = new PostsManager();
        $SchoolManager = new SchoolManager();
        $TagsManager = new TagsManager();
        !empty($_GET['offset']) && $_GET['offset'] % $nbPostsByPage === 0 ? $offset = $_GET['offset'] : $offset = 0;
        switch ($_GET['sortBy']) {
            case 'lastPosted' :
                $result['nbPostsByPage'] = $nbPostsByPage;
                $result['items'] = $PostsManager->getLastPosted($nbPostsByPage, $offset);
                $result['nbPage'] = ceil($PostsManager->getCountReferencedPosts() / $nbPostsByPage);
                break;
            case 'mostLiked' :
                $result['nbPostsByPage'] = $nbPostsByPage;
                $result['items'] = $PostsManager->getMostLikedPosts($nbPostsByPage, $offset);
                $result['nbPage'] = ceil($PostsManager->getCountReferencedPosts() / $nbPostsByPage);
                break;
            case 'school' :
                $result = $this->searchSortBySchool($nbPostsByPage, $offset, $PostsManager, $SchoolManager);
                break;
            case 'tag' :
                $result = $this->searchSortByTag($nbPostsByPage, $offset, $PostsManager, $TagsManager);
                break;
            default :
                $this->redirection('index.php?action=search');
        }
        return $result;
    }

    private function searchSortBySchool(int $nbPostsByPage, int $offset, PostsManager $PostsManager, SchoolManager $SchoolManager)
    {
        if (empty($_GET['school'])) {
            $result['nbPostsByPage'] = $nbPostsByPage;
            $result['items'] = $SchoolManager->getSchoolByName(ALL_SCHOOL);
            $result['nbPage'] = 0;
        } else {
            if ($SchoolManager->nameExists($_GET['school']) && $_GET['school'] !== NO_SCHOOL) {
                $result['nbPostsByPage'] = $nbPostsByPage;
                $result['items'] = $PostsManager->getPostsBySchool($_GET['school'], false, $offset, $nbPostsByPage);
                $result['nbPage'] = ceil($PostsManager->getCountPostsBySchool($_GET['school']) / $nbPostsByPage);
            } else {
                $this->incorrectInformation();
            }
        }
        return $result;
    }

    private function searchSortByTag(int $nbPostsByPage, int $offset, PostsManager $PostsManager, TagsManager $TagsManager)
    {
        if (!empty($_GET['tag']) && $TagsManager->exists($_GET['tag'])) {
            $result['nbPostsByPage'] = $nbPostsByPage;
            $result['items'] = $PostsManager->getPostsBytag($_GET['tag'], $nbPostsByPage, $offset);
            $result['nbPage'] = ceil($PostsManager->getCountPostsByTag($_GET['tag']) / $nbPostsByPage);
        } else {
            $this->incorrectInformation();
        }
        return $result;
    }

    private function searchForKeyWord($word)
    {
        $SchoolManager = new SchoolManager();
        $UserManager = new UserManager();
        $PostsManager = new PostsManager();
        $TagsManager = new TagsManager();
        $result = [];
        if ($PostsManager->checkForScriptInsertion([$word])) {
            $result['school'] = $SchoolManager->searchForKeyWord($word);
            $result['user'] = $UserManager->searchForKeyWord($word);
            $result['post'] = $PostsManager->searchForKeyWord($word);
            $result['tag'] = $TagsManager->searchForKeyWord($word);
        }
        //$result have to be empty if all array on it are empty
        $check = 0;
        foreach ($result as $arr) {
            if (empty($arr)) {
                $check++;
            }
        }
        if ($check === count($result)) {
            $result = [];
        }
        return $result;
    }

    /*------------------------------ post view ------------------------------*/
    private function privatePost(Post $post, array $asidePosts, User $user, User $author)
    {
        if ($post->getFileType() === 'folder') {
            // consulting private folder
            $userInfo = $this->getFolderViewInfo($user, $post);
            RenderView::render(
                'template.php', 'frontend/folderView.php', 
                ['asidePosts' => $asidePosts, 'post' => $post, 'user' => $user, 'author' => $author, 
                    'userInfo' => $userInfo, 'option' => ['folderView']]
            );
        } else {
            // consulting private post
            RenderView::render('template.php', 'frontend/postView.php', ['asidePosts' => $asidePosts, 'post' => $post, 'user' => $user, 'author' => $author, 'option' => ['postView']]);
        }
    }

    private function publicPost(Post $post, array $asidePosts, $user, User $author)
    {
        if ($post->getFileType() === 'folder') {
            // consulting public folder
            $userInfo = $this->getFolderViewInfo($user, $post);
            RenderView::render(
                'template.php', 'frontend/folderView.php', 
                ['asidePosts' => $asidePosts, 'post' => $post, 'user' => $user, 'author' => $author, 
                    'userInfo' => $userInfo, 'option' => ['folderView']]
            );
        } else {
            //consulting public post
            RenderView::render('template.php', 'frontend/postView.php', ['asidePosts' => $asidePosts, 'post' => $post, 'user' => $user, 'author' => $author, 'option' => ['postView']]);
        }
    }

    private function getFolderViewInfo($user, Post $post)
    {
        !empty($user) && $post->getIdAuthor() === intval($user->getId()) ? $userIsAuthor = true : $userIsAuthor = false;
        !empty($_SESSION['grade']) && $_SESSION['grade'] === ADMIN ? $userIsAdmin = true : $userIsAdmin = false;
        !empty($_SESSION['grade']) && $_SESSION['grade'] === MODERATOR ? $userIsModerator = true : $userIsModerator = false;
        return ['userIsAuthor' => $userIsAuthor, 'userIsAdmin' => $userIsAdmin, 'userIsModerator' => $userIsModerator];
    }

    /*------------------------------ add post ------------------------------*/
    private function addPostOnFolder(PostsManager $PostsManager)
    {
        if ($folder = $PostsManager->getOneById($_GET['folder'])) {
            if ($_SESSION['grade'] === STUDENT) {
                $isStudent = 'true';
                $urlForm = 'index.php?action=uploadPost';
                $uploadType = 'private';
            } else {
                $isStudent = 'false';
                $urlForm = 'indexAdmin.php?action=uploadSchoolPost';
                $uploadType = 'public';
            }
            if ($folder->getFileType() === 'folder' && $folder->getPostType() === 'schoolPost') {
                // folder is a school post -> display input to upload other type of file (zip rar)
                RenderView::render(
                    'template.php', 'backend/addSchoolPostView.php', 
                    ['isStudent' => $isStudent, 'urlForm' => $urlForm, 'uploadType' => $uploadType, 
                        'option' => ['addPost', 'tinyMCE']]);
            }  else {
                // folder is public
                RenderView::render('template.php', 'frontend/addPostView.php', ['option' => ['addPost', 'tinyMCE']]);
            }
        } else {
            $this->incorrectInformation();
        }
    }

    private function uploadUserPost($response, PostsManager $PostsManager, TagsManager $TagsManager)
    {
        // upload user post
        if ($PostsManager->uploadPost($response)) {
            if (!empty($_POST['listTags'])) {
                      $TagsManager->checkForNewTag($_POST['listTags'], $PostsManager->getLastInsertId());
            }
            header('Location: index.php?action=userProfile&userId=' . $_SESSION['id']);
        } else {
            throw new \Exception("Le fichier n'est pas conforme");
        }
    }

    private function uploadOnSchoolFolder($response, PostsManager $PostsManager)
    {
        // upload user post on private school folder
        $_POST['uploadType'] === 'private';
        if ($PostsManager->uploadPost($response, true, 'none')) {
            header('Location: index.php?action=schoolProfile&school=' . $_SESSION['school']);
        } else {
            throw new \Exception("Le fichier n'est pas conforme");
        }
    }

    /*------------------------------ report ------------------------------*/
    private function reportPost(int $elemId)
    {
        $PostsManager = new PostsManager();
        if ($elemId > 0 && $PostsManager->exists($_GET['id'])) {
            $ReportManager = new ReportManager();
            if (!$ReportManager->reportExists('post', $elemId, $_SESSION['id'])) {
                RenderView::render('template.php', 'frontend/reportView.php', ['option' => ['tinyMCE']]);
            } else {
                $this->error('Vous avez déja signalé ce contenu');
            }
        } else {
            $this->incorrectInformation();
        }
    }

    private function reportComment(int $elemId)
    {
        $CommentsManager = new CommentsManager();
        if ($elemId > 0 && $CommentsManager->exists($_GET['id'])) {
            $ReportManager = new ReportManager();
            if (!$ReportManager->reportExists('comment', $elemId, $_SESSION['id'])) {
                RenderView::render('template.php', 'frontend/reportView.php', ['option' => ['tinyMCE']]);
            } else {
                $this->error('Vous avez déja signalé ce contenu');
            }
        } else {
            $this->incorrectInformation();
        }
    }

    private function reportOther()
    {
        RenderView::render('template.php', 'frontend/reportOtherView.php', ['option' => ['tinyMCE']]);
    }
}
