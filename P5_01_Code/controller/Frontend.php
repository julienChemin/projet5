<?php
namespace Chemin\ArtSchool\Model;
use Chemin\ArtSchool\Model\Controller;

class Frontend extends Controller
{
    public static $SIDE = 'frontend';
    public static $INDEX = 'index.php';


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
        } elseif (isset($_COOKIE['artSchoolId']) || isset($_COOKIE['artSchoolAdminId'])) {
            //user is not connect, looking for cookie
            $this->useCookieToSignIn();
        }
    }

    public function home()
    {
        $PostsManager = new PostsManager();
        //check url
        $url = explode('/', $_SERVER['PHP_SELF']);
        $url = $url[count($url) - 1];
        if ($url !== static::$INDEX) {
            //$url is school name or user name
            $UserManager = new UserManager();
            $SchoolManager = new SchoolManager();
            if ($UserManager->nameExists($url)) {
                $user = $UserManager->getUserByName($url);
                header('Location: ../index.php?action=userProfile&userId=' . $user->getId());
            } elseif ($SchoolManager->nameExists($url)) {
                header('Location: ../index.php?action=schoolProfile&school=' . $url);
            } else {
                RenderView::render('template.php', 'frontend/indexView.php');
            }
        } else {
            $SchoolManager = new SchoolManager();
            $TagsManager = new TagsManager();
            $posts = $PostsManager->getPostsForHome($SchoolManager, $TagsManager);
            RenderView::render('template.php', 'frontend/indexView.php', ['posts' => $posts, 'option' => ['home']]);    
        }
    }

    public function signUp()
    {
        if (empty($_SESSION)) {
            $message = null;
            if (!empty($_POST)) {
                $UserManager = new UserManager();
                $SchoolManager = new SchoolManager();
                $HistoryManager = new HistoryManager();
                $message = $UserManager->signUp($_POST, $SchoolManager, $HistoryManager);
            }
            RenderView::render('template.php', 'frontend/signUpView.php', ['option' => ['signUp'], 'message' => $message]);
        } else {
			header('Location: index.php');
        }
    }

    public function signIn()
    {
        if (empty($_SESSION)) {
            $UserManager = new UserManager();
            $message = null;
            //if user is not connected and try to connect
            if (!empty($_POST['ConnectPseudo']) && !empty($_POST['ConnectPassword'])) {
                if ($UserManager->canConnect($_POST['ConnectPseudo'], $_POST['ConnectPassword'])) {
                    $user = $UserManager->getUserByName($_POST['ConnectPseudo']);
                    if (isset($_POST['stayConnect'])) {
                        //if user want to stay connect
                        $this->setCookie($user);
                    }
                    $this->connect($user);
                    header('Location: index.php');
                } else {
					$message = 'L\'identifiant ou le mot de passe est incorrecte';
                }
                //if user try to get back his password
            } else if (isset($_POST['postMail'])) {
                if ($UserManager->mailExists($_POST['postMail'])) {
                    $UserManager->mailTemporaryPassword($UserManager->getUserByMail($_POST['postMail']));
                    $message = "Un mail vient de vous être envoyé pour réinitialiser votre mot de passe";
                } else {
					$message = "l'adresse mail renseignée ne correspond à aucun utilisateur";
                }
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
        //form to reset password
        if (!empty($_GET['key']) && !empty($_GET['id'])) {
            if ($UserManager->exists($_GET['id'])) {
                $user = $UserManager->getOneById($_GET['id']);
                if ($user->getTemporaryPassword() === $_GET['key'] && $user->getBeingReset()) {
                    $message = $UserManager->checkWrongPasswordMessage($_GET['wrongPassword']);
                    RenderView::render('template.php', 'frontend/resetPasswordView.php', ['user' => $user, 'message' => $message]);
                } else {
					$this->invalidLink();
                }
            } else {
				$this->incorrectInformation();
            }
            // check form data
        } else if (isset($_POST['newPassword']) && isset($_POST['confirmNewPassword'])) {
            if ($_POST['newPassword'] === $_POST['confirmNewPassword']) {
                if ($UserManager->exists($_POST['id'])) {
                    $user = $UserManager->getOneById($_POST['id']);
                    if ($user->getTemporaryPassword() === $_POST['key'] && $user->getBeingReset()) {
                        if (!password_verify($_POST['newPassword'], $user->getPassword())) {
                               //new password is correct
                               $UserManager->updateById($user->getId(), 'password', password_hash($_POST['newPassword'], PASSWORD_DEFAULT));
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
        } else {
			$this->invalidLink();
        }    
    }

    public function search()
    {
        $PostsManager = new PostsManager();
        $SchoolManager = new SchoolManager();
        $TagsManager = new TagsManager();
        if (!empty($_POST['keyWord'])) {
            //search by key word
            $result = $this->searchForKeyWord($_POST['keyWord']);
            RenderView::render('template.php', 'frontend/searchView.php', ['PostsManager' => $PostsManager, 'result' => $result]);
        } elseif (!empty($_GET['sortBy'])) {
            //sort by last posted / most liked / post on school $school / tag $tag
            $nbPostsByPage = 12;
            !empty($_GET['offset']) && $_GET['offset'] % $nbPostsByPage === 0 ? $offset = $_GET['offset'] : $offset = 0;
            switch ($_GET['sortBy']) {
            case 'lastPosted' :
                $items = $PostsManager->getLastPosted($nbPostsByPage, $offset);
                $nbPage = ceil($PostsManager->getCountReferencedPosts() / $nbPostsByPage);
                break;
            case 'mostLiked' :
                $items = $PostsManager->getMostLikedPosts($nbPostsByPage, $offset);
                $nbPage = ceil($PostsManager->getCountReferencedPosts() / $nbPostsByPage);
                break;
            case 'school' :
                if (empty($_GET['school'])) {
                    $items = $SchoolManager->getSchoolByName(ALL_SCHOOL);
                    $nbPage = 0;
                } else {
                    if ($SchoolManager->nameExists($_GET['school']) && $_GET['school'] !== NO_SCHOOL) {
                        $items = $PostsManager->getPostsBySchool($_GET['school'], false, $offset, $nbPostsByPage);
                        $nbPage = ceil($PostsManager->getCountPostsBySchool($_GET['school']) / $nbPostsByPage);
                    } else {
						$this->incorrectInformation();
                    }
                }
                break;
            case 'tag' :
                if (!empty($_GET['tag']) && $TagsManager->exists($_GET['tag'])) {
                    $items = $PostsManager->getPostsBytag($_GET['tag'], $nbPostsByPage, $offset);
                    $nbPage = ceil($PostsManager->getCountPostsByTag($_GET['tag']) / $nbPostsByPage);
                } else {
					$this->incorrectInformation();
                }
                break;
            default :
                $this->redirection('index.php?action=search');
            }
            RenderView::render('template.php', 'frontend/searchView.php', ['nbPage' => $nbPage, 'nbPostsByPage' => $nbPostsByPage, 'items' => $items]);
        } else {
			RenderView::render('template.php', 'frontend/searchView.php');
        }
    }

    public function searchForKeyWord($word)
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
            RenderView::render('template.php', 'frontend/advancedSearchView.php', ['nbPage' => $nbPage, 'nbPostsByPage' => $nbPostsByPage, 'posts' => $posts, 'option' => ['advancedSearch']]);
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
        if (!empty($_GET['userId']) && $_GET['userId'] !== '1') {
            $UserManager = new UserManager();
            if ($UserManager->exists($_GET['userId'])) {
                $ProfileContentManager = new ProfileContentManager();
                $user = $UserManager->getOneById($_GET['userId']);
                $profileContent = $ProfileContentManager->getByProfileId($user->getId());
                RenderView::render('template.php', 'frontend/userProfileView.php', ['user' => $user, 'profileContent' => $profileContent, 'option' => ['userProfile', 'tinyMCE']]);
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
            if ($SchoolManager->nameExists($_GET['school'])) {
                $ProfileContentManager = new ProfileContentManager();
                $school = $SchoolManager->getSchoolByName($_GET['school']);
                $profileContent = $ProfileContentManager->getByProfileId($school->getId(), true);
                RenderView::render('template.php', 'frontend/schoolProfileView.php', ['school' => $school, 'profileContent' => $profileContent, 'option' => ['schoolProfile']]);
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
        if (!empty($_GET['userId']) && !empty($_GET['elem']) && $UserManager->exists($_GET['userId']) && $_GET['userId'] === $_SESSION['id']) {
            $user = $UserManager->getOneById($_SESSION['id']);
            switch ($_GET['elem']) {
            case 'profileBanner' :
                if (isset($_GET['noBanner'], $_GET['value'])) {
                    if (strpos($_GET['value'], $user->getProfileBanner()) === false && file_exists($user->getProfileBanner())) {
                        unlink($user->getProfileBanner());
                    }
                    $infos = $_GET['value'] . ' ' . $_GET['noBanner'];
                    $UserManager->updateById($_GET['userId'], 'profileBannerInfo', $infos);
                }
                break;
            case 'profilePicture' :
                if (isset($_GET['orientation'], $_GET['size'], $_GET['value'])) {
                    if (strpos($_GET['value'], $user->getProfilePicture()) === false && file_exists($user->getProfilePicture())) {
                        unlink($user->getProfilePicture());
                    }
                    $infos = $_GET['value'] . ' ' . $_GET['orientation'] . ' ' . $_GET['size'];
                    $UserManager->updateById($_GET['userId'], 'profilePictureInfo', $infos);
                }
                break;
            case 'profileText' :
                if (isset($_GET['block'], $_GET['pseudo'], $_GET['school'])) {
                    $infos = $_GET['block'] . ' ' . $_GET['pseudo'] . ' ' . $_GET['school'];
                    $UserManager->updateById($_GET['userId'], 'profileTextInfo', $infos);
                }
                break;
            case 'content' :
                $ProfileContentManager = new ProfileContentManager();
                $ProfileContentManager->updateProfileContent($_POST);
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

    public function uploadBanner(array $GET, string $finalPath)
    {
        $validBannerValue = array('true', 'false');
        if (!empty($GET['noBanner']) && in_array($GET['noBanner'], $validBannerValue)) {
            $UserManager = new UserManager();
            $user = $UserManager->getOneById($_SESSION['id']);
            if (file_exists($user->getProfileBanner())) {
                unlink($user->getProfileBanner());
            }
            $infos = $finalPath . ' ' . $GET['noBanner'];
            $UserManager->updateById($_SESSION['id'], 'profileBannerInfo', $infos);
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
            $UserManager = new UserManager();
            $user = $UserManager->getOneById($_SESSION['id']);
            if ($user->getProfilePicture() !== 'public/images/question-mark.png' && file_exists($user->getProfilePicture())) {
                unlink($user->getProfilePicture());
            }
            $infos = $finalPath . ' ' . $GET['orientation'] . ' ' . $GET['size'];
            $UserManager->updateById($_SESSION['id'], 'profilePictureInfo', $infos);
        } else {
			$this->incorrectInformation();
        }
    }

    public function post()
    {
        $UserManager = new UserManager();
        $PostsManager = new PostsManager();
        $TagsManager = new TagsManager();
        if (!empty($_GET['id']) && $PostsManager->exists($_GET['id'])) {
            $post = $PostsManager->getOneById($_GET['id']);
            $asidePosts = $PostsManager->getAsidePosts($post, $TagsManager);
            if ($UserManager->exists($post->getIdAuthor())) {
                $author = $UserManager->getOneById($post->getIdAuthor());
            } else {
				$author = null;
            }
            if (!empty($_SESSION)) {
                $user = $UserManager->getOneById($_SESSION['id']);
            } else {
				$user = null;
            }
            if ($post->getIsPrivate()) {
                if (!empty($_SESSION) && ($post->getSchool() === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL) && ($post->getIdAuthor() === intval($_SESSION['id']) || $_SESSION['grade'] === MODERATOR || $_SESSION['grade'] === ADMIN || $post->getListAuthorizedGroups() === null || in_array($_SESSION['group'], $post->getListAuthorizedGroups()))) {
                    if ($post->getFileType() === 'folder') {
                        RenderView::render('template.php', 'frontend/folderView.php', ['asidePosts' => $asidePosts, 'post' => $post, 'user' => $user, 'author' => $author, 'option' => ['folderView']]);
                    } else {
                        RenderView::render('template.php', 'frontend/postView.php', ['asidePosts' => $asidePosts, 'post' => $post, 'user' => $user, 'author' => $author, 'option' => ['postView']]);
                    }
                } else {
					$this->accessDenied();
                }
            } else {
                if ($post->getFileType() === 'folder') {
                    RenderView::render('template.php', 'frontend/folderView.php', ['asidePosts' => $asidePosts, 'post' => $post, 'user' => $user, 'author' => $author, 'option' => ['folderView']]);
                } else {
                    RenderView::render('template.php', 'frontend/postView.php', ['asidePosts' => $asidePosts, 'post' => $post, 'user' => $user, 'author' => $author, 'option' => ['postView']]);
                }
            }
        } else {
			$this->invalidLink();
        }
    }

    public function addPost()
    {
        if (!empty($_SESSION['id']) && $_SESSION['school'] !== ALL_SCHOOL) {
            //if user try to post on folder and folder is a schoolPost -> display input to upload other type file (zip rar)
            if (!empty($_GET['folder'])) {
                $PostsManager = new PostsManager();
                if ($PostsManager->exists($_GET['folder'])) {
                    $folder = $PostsManager->getOneById($_GET['folder']);
                    if ($folder->getPostType() === 'schoolPost') {
                        RenderView::render('template.php', 'backend/addSchoolPostView.php', ['option' => ['addPost', 'tinyMCE']]);
                    }  else {
						RenderView::render('template.php', 'frontend/addPostView.php', ['option' => ['addPost', 'tinyMCE']]);
                    }
                } else {
					$this->incorrectInformation();
                }
            } else {
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
                $_POST['postType'] === 'schoolPost' ? $isSchoolPost = true : $isSchoolPost = false;
                if ($isSchoolPost) {
                    //user post on school folder
                    $_POST['uploadType'] === 'private';
                    if ($PostsManager->uploadPost($response, true, 'none')) {
                        header('Location: index.php?action=schoolProfile&school=' . $_SESSION['school']);
                    } else {
						throw new \Exception("Le fichier n'est pas conforme");
                    }
                } else {
                    //userPost
                    if ($PostsManager->uploadPost($response)) {
                        if (!empty($_POST['listTags'])) {
                                  $TagsManager->checkForNewTag($_POST['listTags'], $PostsManager->getLastInsertId());
                        }
                        header('Location: index.php?action=userProfile&userId=' . $_SESSION['id']);
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

    public function deletePost()
    {
        $PostsManager = new PostsManager();
        $TagsManager = new TagsManager();
        if (isset($_GET['id'], $_SESSION['id']) && $PostsManager->exists($_GET['id'])) {
            $post = $PostsManager->getOneById($_GET['id']);
            if ($post->getIdAuthor() === intval($_SESSION['id']) || $_SESSION['school'] === ALL_SCHOOL) {
                if ($post->getFileType() === 'folder') {
                    $PostsManager->deleteFolder($post->getId());
                } else {
                    $PostsManager->deletePost($post->getId());
                }
                //at the same time, delete unused tags
                $TagsManager->deleteUselessTags();
                if ($post->getPostType() === 'schoolPost') {
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

    public function getTags()
    {
        $TagsManager = new TagsManager();
        $listTags = $TagsManager->get();
        $arrTags = [];
        for ($i=0; $i<count($listTags); $i++) {
            $arrTags[$i] = $listTags[$i]['name'];
        }
        echo json_encode($arrTags);
    }

    /*public function addSchool()        @TODO view for user who want to add his school
    {
    $message = null; 
    if (empty($_SESSION) || $_SESSION['school'] === NO_SCHOOL) {
    if (!empty($_POST['adminPassword']) && !empty($_GET['option']) && $_GET['option'] === 'add') {
                //if form to add school is filled
                $SchoolManager = new SchoolManager();
                $HistoryManager = new HistoryManager();
                $UserManager = new UserManager();
                $arrCanAddSchool = $SchoolManager->canAddSchool($_POST, $UserManager);
                if ($arrCanAddSchool['canAdd']) {
                    //add school and school administrator
                    $message = $SchoolManager->addSchool($_POST, $UserManager, $HistoryManager);
                } else {$message = $arrCanAddSchool['message'];}
    }
    RenderView::render('template.php', 'backend/addSchoolView.php', ['option' => ['addSchool'], 'message' => $message]);
    } else {header('Location: index.php');}
    }*/

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
				echo 'false';
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
			echo false;
        }
    }

    public function deleteComment()
    {
        $CommentsManager = new CommentsManager();
        if (isset($_GET['id'], $_SESSION['id']) && $CommentsManager->exists($_GET['id'])) {
            $comment = $CommentsManager->getOneById($_GET['id']);
            if ($comment->getIdAuthor() === $_SESSION['id'] || $_SESSION['school'] === ALL_SCHOOL) {
                $CommentsManager->delete($comment->getId());
                echo true;
            } else {
				echo false;
            }
        } else {
			echo false;
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

    public function report()
    {
        if (!empty($_SESSION) && !empty($_GET['elem']) && !empty($_GET['id'])) {
            $ReportManager = new ReportManager();
            $reportExists = $ReportManager->reportExists($_GET['elem'], $_GET['id'], $_SESSION['id']);
            switch ($_GET['elem']) {
            case 'post' :
                $PostsManager = new PostsManager();
                $elemExists = $PostsManager->exists($_GET['id']);
                break;
            case 'comment' :
                $CommentsManager = new CommentsManager();
                $elemExists = $CommentsManager->exists($_GET['id']);
                break;
            default : $this->incorrectInformation();
            }
            if ($elemExists) {
                RenderView::render('template.php', 'frontend/reportView.php', ['reportExists' => $reportExists, 'option' => ['tinyMCE']]);
            } else {
				$this->incorrectInformation();
            }
        } else {
			$this->incorrectInformation();
        }
    }

    public function setReport()
    {
        $arrAcceptedElem = array('post', 'comment');
        if (!empty($_SESSION) && !empty($_POST['elem']) && in_array($_POST['elem'], $arrAcceptedElem) && !empty($_POST['idElem']) && intval($_POST['idElem']) > 0 && !empty($_POST['tinyMCEtextarea'])) {
            $ReportManager = new ReportManager();
            $ReportManager->setReport($_POST['elem'], intval($_POST['idElem']), $_SESSION['id'], $_POST['tinyMCEtextarea']);
            if (!empty($_POST['idPost']) && intval($_POST['idPost']) > 0) {
                header('Location: index.php?action=post&id=' . $_POST['idPost']);
            } else {
				$this->redirection();
            }
        } else {
			$this->incorrectInformation();
        }
    }

    public function faq()
    {

    }
}
