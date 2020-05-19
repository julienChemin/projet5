<?php
namespace Chemin\ArtSchool\Model;
use Chemin\ArtSchool\Model\Controller;

class Frontend extends Controller
{
	public static $SIDE = 'frontend';
	public static $INDEX = 'index.php';
	public static $COLOR = 'white';

	public function __construct()
	{
		$this->verifyInformation();
		if (isset($_SESSION['grade'])) {
			switch ($_SESSION['grade']) {
				case ADMIN :
					static::$COLOR = '#CF8B3F';
				break;
				case MODERATOR :
					static::$COLOR = '#FFC652';
				break;
				case STUDENT :
					static::$COLOR = '#3498BF';
				break;
				case USER :
					static::$COLOR = '#3498BF';
				break;
				default :
					static::$COLOR = '#555';
			}
		}
	}

	public function verifyInformation()
	{
		if (isset($_SESSION['pseudo'])) {
			//user is connect, verify SESSION info
			$SchoolManager = new SchoolManager();
			$UserManager = new UserManager();
			if((!$SchoolManager->nameExists($_SESSION['school']) && !($_SESSION['school'] === ALL_SCHOOL)) || !$UserManager->nameExists($_SESSION['pseudo'])) {
				//if user name don't exist or if school name don't exist and isn't "allSchool"
				$this->forceDisconnect();
			} else {
				//user exist, check his group
				$user = $UserManager->getUserByName($_SESSION['pseudo']);
				if ($_SESSION['group'] !== $user->getSchoolGroup()) {
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
		RenderView::render('template.php', 'frontend/indexView.php');
	}

	public function signUp()
	{
		if (empty($_SESSION)) {
			$message = null;
			if (!empty($_POST)){
				$UserManager = new UserManager();
				$SchoolManager = new SchoolManager();
				$HistoryManager = new HistoryManager();
				$message = $UserManager->signUp($_POST, $SchoolManager, $HistoryManager);
			}
			RenderView::render('template.php', 'frontend/signUpView.php', ['option' => ['signUp'], 'message' => $message]);
		} else {header('Location: index.php');}
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
				} else {$message = 'L\'identifiant ou le mot de passe est incorrecte';}
			//if user try to get back his password
			} else if (isset($_POST['postMail'])) {
				if ($UserManager->mailExists($_POST['postMail'])) {
					$UserManager->mailTemporaryPassword($UserManager->getUserByMail($_POST['postMail']));
					$message = "Un mail vient de vous être envoyé pour réinitialiser votre mot de passe";
				} else {$message = "l'adresse mail renseignée ne correspond à aucun utilisateur";}
			}
			RenderView::render('template.php', 'frontend/signInView.php', ['option' => ['forgetPassword', 'signIn'], 'message' => $message]);
		} else {header('Location: index.php');}
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
				} else {$this->invalidLink();}
			} else {$this->incorrectInformation();}
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
					} else {$this->invalidLink();}
				} else {$this->incorrectInformation();}
			} else {header('Location: index.php?action=resetPassword&key=' . $_POST['key'] . '&id=' . $_POST['id'] . '&wrongPassword=1');}
		} else {$this->invalidLink();}	
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
			} else {$this->invalidLink();}
		} else {$this->incorrectInformation();}
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
			} else {$this->invalidLink();}
		} else {$this->incorrectInformation();}
	}

	public function updateProfile()
	{
		$UserManager = new UserManager();
		if (!empty($_GET['userId']) && !empty($_GET['elem']) && $UserManager->exists($_GET['userId']) && $_GET['userId'] === $_SESSION['id']) {
			switch ($_GET['elem']) {
				case 'profileBanner' :
					if (isset($_GET['noBanner'], $_GET['value'])) {
						$infos = $_GET['value'] . ' ' . $_GET['noBanner'];
						$UserManager->updateById($_GET['userId'], 'profileBannerInfo', $infos);
					}
				break;
				case 'profilePicture' :
					if (isset($_GET['orientation'], $_GET['size'], $_GET['value'])) {
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
		} else {$this->incorrectInformation();}
	}

	public function upload()
	{
		if (!empty($_GET['elem'])) {
			$arrAcceptedExtention = array("jpeg", "jpg", "png", "gif");
			require('view/upload.php');
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
			} else {throw new \Exception("Le fichier n'est pas conforme");}
		} else {$this->incorrectInformation();}
	}

	public function uploadBanner(array $GET, string $finalPath)
	{
		$validBannerValue = array('true', 'false');
		if (!empty($GET['noBanner']) && in_array($GET['noBanner'], $validBannerValue)) {
			$UserManager = new UserManager();
			$infos = $finalPath . ' ' . $GET['noBanner'];
			$UserManager->updateById($_SESSION['id'], 'profileBannerInfo', $infos);
		} else {$this->incorrectInformation();}
	}

	public function uploadProfilePicture(array $GET, string $finalPath)
	{
		$validOrientationValue = array('highPicture', 'widePicture');
		$validSizeValue = array('smallPicture', 'mediumPicture', 'bigPicture');
		if (!empty($GET['orientation']) && in_array($GET['orientation'], $validOrientationValue)
		&& !empty($GET['size']) && in_array($GET['size'], $validSizeValue)) {
			$UserManager = new UserManager();	
			$infos = $finalPath . ' ' . $GET['orientation'] . ' ' . $GET['size'];
			$UserManager->updateById($_SESSION['id'], 'profilePictureInfo', $infos);
		} else {$this->incorrectInformation();}
	}

	public function post()
	{
		RenderView::render('template.php', 'frontend/postView.php');
	}

	public function addPost()
	{
		RenderView::render('template.php', 'frontend/addPostView.php', ['option' => ['addPost', 'tinyMCE']]);
	}

	public function uploadPost()
	{
		$PostsManager = new PostsManager();
		$TagsManager = new TagsManager();
		if (isset($_SESSION['id'], $_POST)) {
			if ($PostsManager->canUploadPost($_POST, $TagsManager)) {
				if ($PostsManager->uploadPost($_POST)) {
					if (!empty($_POST['listTags'])) {
						$TagsManager->checkForNewTag($_POST['listTags']);
					}
					header('Location: index.php?action=userProfile&userId=' . $_SESSION['id']);
				} else {throw new \Exception("Le fichier n'est pas conforme");}
			} else {$this->incorrectInformation();}
		} else {$this->accessDenied();}
	}

	public function getTags()
	{
		$TagsManager = new TagsManager();
		$listTags = $TagsManager->getAll();
		$arrTags = [];
		for ($i=0; $i<count($listTags); $i++) {
			$arrTags[$i] = $listTags[$i]['name'];
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
			$arrPosts = [];
			if (count($posts) > 0) {
				foreach ($posts as $post) {
					$arrPosts[] = $PostsManager->toArray($post);
				}
				echo json_encode($arrPosts);
			} else {echo false;}
		} else {echo false;}
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
			} else {echo false;}
		} else {echo false;}
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
			} else {echo false;}
		} else {echo false;}
	}
}
