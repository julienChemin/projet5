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
			//user is not connect, looking for cookie
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
				} else {$this->accessDenied();}
			} else {$message = 'L\'identifiant ou le mot de passe est incorrecte';}
		//if user try to get back his password
		} else if (isset($_POST['postMail'])) {
			if ($UserManager->mailExists($_POST['postMail'])) {
				$UserManager->mailTemporaryPassword($UserManager->getUserByMail($_POST['postMail']));
				$message = "Un mail vient de vous être envoyé pour réinitialiser votre mot de passe.";
			} else {$message = "l'adresse mail renseignée ne correspond à aucun utilisateur";}
		}
		if (!empty($_SESSION) && $_SESSION['school'] === ALL_SCHOOL) {
			RenderView::render('template.php', 'backend/indexAdminView.php');
		} else {
			RenderView::render('template.php', 'backend/indexAdminView.php', ['option' => ['forgetPassword', 'signIn', 'homeAdmin'], 'message' => $message]);
		}
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
				} else {$message = $arrCanAddSchool['message'];}
			}
			RenderView::render('template.php', 'backend/addSchoolView.php', ['option' => ['addSchool'], 'message' => $message]);
		} else {header('Location: indexAdmin.php');}
	}

	public function moderatSchool()
	{
		if ($_SESSION['grade'] === ADMIN) {
			$SchoolManager = new SchoolManager();
			$schools = $SchoolManager->getSchoolByName($_SESSION['school']);
			if (!empty($schools)) {
				RenderView::render('template.php', 'backend/moderatSchoolView.php', ['option' => ['buttonToggleSchool', 'moderatSchool'], 'schools' => $schools]);
			} else {
				RenderView::render('template.php', 'backend/moderatSchoolView.php', ['option' => ['buttonToggleSchool', 'moderatSchool']]);
			}
		} else {header('Location: indexAdmin.php');}
	}

	public function editSchool()
	{
		if ($_SESSION['grade'] === ADMIN 
		&& ($_SESSION['school'] === ALL_SCHOOL || (!empty($_POST['schoolName']) && $_POST['schoolName'] === $_SESSION['school']))) {
			$UserManager = new UserManager();
			$SchoolManager = new SchoolManager();
			$HistoryManager = new HistoryManager();
			$message = null;
			if (!empty($_POST['elem'])) {
				//consulting form to edit school information
				if ($SchoolManager->nameExists($_POST['schoolName'])) {
					$message = $SchoolManager->editSchool($_POST, $UserManager, $HistoryManager);
				} else {$this->incorrectInformation();}
			}
			RenderView::render('template.php', 'backend/editSchoolView.php', ['message' => $message]);
		} else {$this->accessDenied();}
	}

	public function moderatAdmin()
	{
		if ($_SESSION['grade'] === ADMIN) {
			$UserManager = new UserManager();
			$SchoolManager = new SchoolManager();
			$HistoryManager = new HistoryManager();
			$message = null;
			if (isset($_GET['option'], $_POST['schoolName']) && $_GET['option'] === 'addModerator') {
				//add new moderator
				if ($_SESSION['school'] === $_POST['schoolName'] || $_SESSION['school'] === ALL_SCHOOL) {
					$arrCanAddModerator = $SchoolManager->canAddModerator($_POST, $UserManager);
					if ($arrCanAddModerator['canAdd']) {
						$message = $SchoolManager->addModerator($_POST, $UserManager, $HistoryManager);
					} else {$message = $arrCanAddModerator['message'];}
				} else {$this->accessDenied();}
			}
			$schools = $SchoolManager->getSchoolByName($_SESSION['school']);
			$sorting = $UserManager->moderatAdminSorting($UserManager->getUsersBySchool($_SESSION['school'], 'admin'));
			RenderView::render('template.php', 'backend/moderatAdminView.php', ['users' => $sorting['users'], 'schools' => $schools, 'nbModerator' => $sorting['nbModerator'], 'message' => $message, 'option' => ['moderatAdmin', 'buttonToggleSchool']]);
		} else {$this->accessDenied();}
	}

	public function moderatUsers()
	{
		if ($_SESSION['grade'] === ADMIN || $_SESSION['grade'] === MODERATOR) {
			$UserManager = new UserManager();
			$SchoolManager = new SchoolManager();
			$users = $UserManager->getUsersBySchool($_SESSION['school'], 'user');
			$schools = $SchoolManager->getSchoolByName($_SESSION['school']);
			$sorting = $UserManager->moderatUsersSorting($users, $schools);
			RenderView::render('template.php', 'backend/moderatUsersView.php', 
				['users' => $sorting['users'], 'schools' => $schools, 'isActive' => $sorting['isActive'], 'option' => ['moderatUsers', 'buttonToggleSchool']]);
		} else {$this->accessDenied();}
	}

	public function moderatReports()
	{
		if ($_SESSION['school'] === ALL_SCHOOL) {
			$arrAcceptedValue = array('post', 'comment');
			if (!empty($_GET['elem']) && in_array($_GET['elem'], $arrAcceptedValue) && !empty($_GET['idElem']) && intval($_GET['idElem']) > 0) {
				switch ($_GET['elem']) {
					case 'post':
						$PostsManager = new PostsManager();
						if ($PostsManager->exists(intval($_GET['idElem']))) {
							$elem = $PostsManager->getOneById($_GET['idElem']);
						} else {$this->incorrectInformation();}
					break;
					case 'comment':
						$CommentsManager = new CommentsManager();
						if ($CommentsManager->exists(intval($_GET['idElem']))) {
							$elem = $CommentsManager->getOneById($_GET['idElem']);
						} else {$this->incorrectInformation();}
					break;
				}
				RenderView::render('template.php', 'backend/moderatReportsView.php', ['reportsFromElem' => $elem, 'option' => ['moderatReports']]);
			} else {
				RenderView::render('template.php', 'backend/moderatReportsView.php', ['option' => ['moderatReports']]);
			}
		} else {$this->accessDenied();}
	}

	public function getReports()
	{
		if (!empty($_GET['elem']) && isset($_GET['offset']) && intval($_GET['offset']) >= 0) {
			$ReportManager = new ReportManager();
			echo json_encode($ReportManager->getReports($_GET['elem'], true, $_GET['offset']));
		} else {echo 'false';}
	}

	public function getReportsFromElem()
	{
		if (!empty($_GET['elem']) && isset($_GET['idElem']) && intval($_GET['idElem']) > 0) {
			$ReportManager = new ReportManager();
			echo json_encode($ReportManager->getReportsFromElem($_GET['elem'], $_GET['idElem']));
		} else {echo 'false';}
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
			} else {echo false;}
		} else {echo false;}
	}

	public function deleteReportsFromElem()
	{
		$ReportManager = new ReportManager();
		$arrAcceptedValue = array('post', 'comment');
		if (!empty($_GET['elem']) && in_array($_GET['elem'], $arrAcceptedValue) && !empty($_GET['idElem']) && intval($_GET['idElem']) > 0) {
			$ReportManager->deleteReportsFromElem($_GET['elem'], $_GET['idElem']);
			echo true;
		} else {echo false;}
	}

	public function createGroup()
	{
		if (!empty($_GET['group']) && !empty($_GET['schoolName']) && ($_GET['schoolName'] === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL)) {
			$SchoolManager = new SchoolManager();
			echo $SchoolManager->createGroup($_GET);
		} else {echo false;}
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
				} else {echo false;}
			} else {echo false;}
		} else {echo false;}
	}

	public function deleteGroup()
	{
		if (!empty($_GET['group']) && !empty($_GET['schoolName']) && ($_GET['schoolName'] === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL)) {
			$SchoolManager = new SchoolManager();
			$UserManager = new UserManager();
			if ($SchoolManager->deleteGroup($_GET, $UserManager)) {
				$this->redirection();
			} else {$this->incorrectInformation();}
		} else {$this->incorrectInformation();}
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
				} else {$this->incorrectInformation();}
			} else {throw new \Exception("Un utilisateur ne peut pas être à la fois modérateur et administrateur");}
		} else {$this->incorrectInformation();}
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
					} else {throw new \Exception("Il est impossible d'effectuer cette action, vous avez atteint le nombre maximum de compte utilisateur actif");}
				} else {$this->incorrectInformation();}
			} else {$this->accessDenied();}
		} else {$this->incorrectInformation();}
	}

	public function delete()
	{
		if (!empty($_GET['elem'])) {
			$SchoolManager = new SchoolManager();
			$UserManager = new UserManager();
			switch ($_GET['elem']) {
				case 'user' :
					if (!empty($_GET['userName']) && !empty($_GET['schoolName']) && $UserManager->nameExists($_GET['userName']) 
					&& ($_SESSION['school'] === ALL_SCHOOL || $_SESSION['school'] === $_GET['schoolName'])) {
						if ($UserManager->deleteUser($_GET, $SchoolManager)) {
							$this->redirection();
						} else {throw new \Exception("Vous ne pouvez pas supprimer un compte administrateur");}
					} else {$this->incorrectInformation();}
				break;
				default :
					$this->incorrectInformation();
			}
		}
	}

	public function schoolProfile()
	{
		if (!empty($_GET['school']) && ($_GET['school'] === $_SESSION['school'] || $_SESSION['school'] === ALL_SCHOOL) && ($_SESSION['grade'] === ADMIN || $_SESSION['grade'] === MODERATOR)) {
			$SchoolManager = new SchoolManager();
			if ($SchoolManager->nameExists($_GET['school'])) {
				$ProfileContentManager = new ProfileContentManager();
				$school = $SchoolManager->getSchoolByName($_GET['school']);
				$profileContent = $ProfileContentManager->getByProfileId($school->getId(), true);
				RenderView::render('template.php', 'backend/schoolProfileView.php', ['school' => $school, 'profileContent' => $profileContent, 'option' => ['schoolProfile', 'tinyMCE']]);
			} else {$this->invalidLink();}
		} else {$this->incorrectInformation();}
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
		} else {$this->incorrectInformation();}
	}

	public function upload()
	{
		if (!empty($_GET['elem'])) {
			$arrAcceptedExtention = array("jpeg", "jpg", "png", "gif");
			require('view/upload.php');
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
			$SchoolManager = new SchoolManager();
			$school = $SchoolManager->getSchoolByName($_SESSION['school']);
			if (file_exists($school->getProfileBanner())) {
				unlink($school->getProfileBanner());
			}	
			$infos = $finalPath . ' ' . $GET['noBanner'];
			$SchoolManager->updateByName($_SESSION['school'], 'profileBannerInfo', $infos);
		} else {$this->incorrectInformation();}
	}

	public function uploadProfilePicture(array $GET, string $finalPath)
	{
		$validOrientationValue = array('highPicture', 'widePicture');
		$validSizeValue = array('smallPicture', 'mediumPicture', 'bigPicture');
		if (!empty($GET['orientation']) && in_array($GET['orientation'], $validOrientationValue)
		&& !empty($GET['size']) && in_array($GET['size'], $validSizeValue)) {
			$SchoolManager = new SchoolManager();
			$school = $SchoolManager->getSchoolByName($_SESSION['school']);
			if (file_exists($school->getProfilePicture())) {
				unlink($school->getProfilePicture());
			}
			$infos = $finalPath . ' ' . $GET['orientation'] . ' ' . $GET['size'];
			$SchoolManager->updateByName($_SESSION['school'], 'profilePictureInfo', $infos);
		} else {$this->incorrectInformation();}
	}

	public function schoolHistory()
	{
		$HistoryManager = new HistoryManager();
		$SchoolManager = new SchoolManager();
		if ($_SESSION['school'] === ALL_SCHOOL) {
			$schools = $SchoolManager->getSchoolByName($_SESSION['school']);
			RenderView::render('template.php', 'backend/schoolHistoryView.php', ['schools' => $schools, 'option' => ['schoolHistory']]);
		} else {
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
			if ($SchoolManager->exists($_GET['school'])) {
				echo json_encode($HistoryManager->getSchoolHistory($_GET));
			} else {$this->incorrectInformation();}
		}
	}

	public function addSchoolPost()
	{
		if ($_SESSION['school'] !== ALL_SCHOOL) {
			$SchoolManager = new SchoolManager();
			$school = $SchoolManager->getSchoolByName($_SESSION['school']);
			RenderView::render('template.php', 'backend/addSchoolPostView.php', ['groups' => $school->getListSchoolGroups(), 'option' => ['addPost', 'tinyMCE']]);
		} else {$this->incorrectInformation();}
	}

	public function uploadSchoolPost()
	{
		if (!empty($_POST['fileTypeValue']) && !empty($_POST['uploadType'])) {
			$PostsManager = new PostsManager();
			$TagsManager = new TagsManager();
			//check upload type
			$_POST['listGroup'] === "all" ? $authorizedGroups = null : $authorizedGroups = $_POST['listAuthorizedGroups'];
			if (isset($_SESSION['id'], $_POST)) {
				if ($PostsManager->canUploadPost($_POST, $TagsManager)) {
					if ($PostsManager->uploadPost($_POST, true, $authorizedGroups)) {
						if (!empty($_POST['listTags'])) {
							$TagsManager->checkForNewTag($_POST['listTags']);
						}
						header('Location: indexAdmin.php?action=schoolProfile&school=' . $_SESSION['school']);
					} else {throw new \Exception("Le fichier n'est pas conforme");}
				} else {$this->incorrectInformation();}
			} else {$this->accessDenied();}
		}
		header('Location: indexAdmin.php?action=schoolProfile&school=' . $_SESSION['school']);
	}

	public function addWarning() {
		$UserManager = new UserManager();
		if (!empty($_SESSION) && $_SESSION['school'] === ALL_SCHOOL && isset($_GET['idUser']) && $UserManager->exists(intval($_GET['idUser']))) {
			$user = $UserManager->getOneById(intval($_GET['idUser']));
			if (($user->getNbWarning() + 1) >= 3) {
				$UserManager->updateById(intval($_GET['idUser']), 'isBan', true, true);
				$UserManager->updateById(intval($_GET['idUser']), 'dateBan', date('Y-m-d H:i:s'));
				$UserManager->updateById(intval($_GET['idUser']), 'nbWarning', 0);
			} else {
				$UserManager->updateById(intval($_GET['idUser']), 'nbWarning', ($user->getNbWarning() + 1));
			}
			echo 'true';
		} else {echo 'false';}
	}
}
