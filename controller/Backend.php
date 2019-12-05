<?php

namespace Chemin\ArtSchool\Model;

class Backend
{
	public static $isConnected = false;

	public function __construct()
	{
		if (isset($_SESSION['grade']) && ($_SESSION['grade'] === 'admin'  || $_SESSION['grade'] === 'moderator')) {
			static::$isConnected = true;
		} elseif (isset($_COOKIE['artSchoolAdminId'])) {
			$UserManager = new UserManager();
			$user = $UserManager->getOneById($_COOKIE['artSchoolAdminId']);

			$_SESSION['id'] = $user->getId();
			$_SESSION['pseudo'] = $user->getName();
			$_SESSION['school'] = $user->getSchool();
			if ($user->getIsAdmin()) {
				$_SESSION['grade'] = 'admin';
			} elseif ($user->getIsModerator()) {
				$_SESSION['grade'] = 'moderator';
			}
			static::$isConnected = true;
		} else {
			if (isset($_GET['action']) && $_GET['action'] != 'resetPassword') {
				header('Location: indexAdmin.php');
			}
		}
	}

	public function home()
	{
		//if user is not connected and try to connect
		if (isset($_POST['ConnectPseudoAdmin']) && isset($_POST['ConnectPasswordAdmin'])) {
			$UserManager = new UserManager();
			$userExist = $UserManager->nameExists($_POST['ConnectPseudoAdmin']);

			if ($userExist) {
				$user = $UserManager->getUserByName($_POST['ConnectPseudoAdmin']);
				$passwordIsOk = $UserManager->checkPassword($user, $_POST['ConnectPasswordAdmin']);

				if ($passwordIsOk) {
					$isAdmin = $user->getIsAdmin();
					$isModerator = $user->getIsModerator();

					if ($isAdmin) {
						if (isset($_POST['stayConnect'])) {
							//if user want to stay connect
							setcookie('artSchoolAdminId', $user->getId(), time()+(365*24*3600), null, null, false, true);
						}
						$_SESSION['id'] = $user->getId();
						$_SESSION['pseudo'] = $user->getName();
						$_SESSION['school'] = $user->getSchool();
						$_SESSION['grade'] = 'admin';
						header('Location: indexAdmin.php');
					} elseif ($isModerator){
						if (isset($_POST['stayConnect'])) {
							//if user want to stay connect
							setcookie('artSchoolAdminId', $user->getId(), time()+(365*24*3600), null, null, false, true);
						}
						$_SESSION['id'] = $user->getId();
						$_SESSION['pseudo'] = $user->getName();
						$_SESSION['school'] = $user->getSchool();
						$_SESSION['grade'] = 'moderator';
						header('Location: indexAdmin.php');
					} else {
						$message = 'Vous devez etre administrateur ou modérateur pour accéder a cette espace';
					}
				} else {
					$message = 'Le mot de passe est incorrecte';
				}
			} else {
				$message = 'L\'identifiant est incorrecte';
			}
		//if user try to get back his password
		} else if (isset($_POST['postMail'])) {
			$UserManager = new UserManager();

			$mailExist = $UserManager->mailExists($_POST['postMail']);
			if ($mailExist) {
				$user = $UserManager->getUserByMail($_POST['postMail']);

				$temporaryPassword = password_hash($user->getName() . time(), PASSWORD_DEFAULT);
				$UserManager->setTemporaryPassword($temporaryPassword, $user->getId());

				$subject = 'Recuperation de mot de passe';
				$content = 'Bonjour ' . $user->getName() . ', vous avez demande a reinitialiser votre mot de passe.
					En suivant ce lien vous serez redirige vers une page pour modifier votre mot de passe : 
					http://julienchemin.fr/projet5/indexAdmin.php?action=resetPassword&key=' . $temporaryPassword . '&id=' . $user->getId();
				$content = wordwrap($content, 70, "\r\n");
				$headers = array('From' => '"Art-School"<julchemin@orange.fr>', 
					'Content-Type' => 'text/html; charset=utf-8');
				
				ini_set("sendmail_from","julchemin@orange.fr");
				mail($user->getMail(), $subject, $content, $headers);
				
				$message = "Un mail vient de vous être envoyé pour réinitialiser votre mot de passe.";
			} else {
				$message = "l'adresse mail renseignée ne correspond à aucun utilisateur";
			}
		}

		if (isset($message)) {
			RenderView::render('template.php', 'backend/indexAdminView.php', ['option' => ['forgetPassword'], 'message' => $message]);
		} else {
			RenderView::render('template.php', 'backend/indexAdminView.php', ['option' => ['forgetPassword']]);
		}
	}

	public function resetPassword()
	{
		//form for reset password
		if (isset($_GET['key']) && isset($_GET['id'])) {
			$UserManager = new UserManager();
			$user = $UserManager->getOneById($_GET['id']);

			if (isset($_GET['wrongPassword'])) {
				$message = "Vous devez entrer deux mot de passe identiques";
			}

			if (! $user->getBeingReset()) {
				$message = "Pour réinitialiser votre mot de passe, vous devez passer directement par le lien qui vous a été envoyé par mail";
			}

			if (isset($message)) {
				RenderView::render('template.php', 'backend/resetPasswordView.php', ['user' => $user, 'message' => $message]);
			} else {
				RenderView::render('template.php', 'backend/resetPasswordView.php', ['user' => $user]);
			}
		// check form data
		} else if (isset($_POST['newPassword']) && isset($_POST['confirmNewPassword'])) {
			if ($_POST['newPassword'] === $_POST['confirmNewPassword']) {
				//new password is ok
				$UserManager = new UserManager();

				$UserManager->setPassword(password_hash($_POST['newPassword'], PASSWORD_DEFAULT), $_POST['id']);

				$message = "Le mot de passe a bien été modifié.";
			} else {
				//new password is wrong
				header('Location: indexAdmin.php?action=resetPassword&key=' . $_POST['key'] . '&id=' . $_POST['id'] . '&wrongPassword=true');
			}
			RenderView::render('template.php', 'backend/resetPasswordView.php', ['message' => $message]);
		} else {
			throw new Exception("Pour réinitialiser votre mot de passe, vous devez passer directement par le lien qui vous a été envoyé par mail");
		}
		
	}

	public function disconnect()
	{
		require('view/backend/disconnect.php');

		header('Location: indexAdmin.php');
	}

	public function addSchool()
	{
		if (isset($_GET['option']) && $_GET['option'] === 'add') {	
			//if form to add school is filled
			if (isset($_POST['adminPassword'])) {
				$SchoolManager = new SchoolManager();
				$UserManager = new UserManager();

				//verify if user name is already use
				if ($UserManager->nameExists($_POST['adminName'])) {
					$userNameIsOk = false;
					$message = "Ce nom d'utilisateur existe déja";
				} else {
					$userNameIsOk = true;
				}

				//verify if school name is already use
				if ($SchoolManager->nameExists($_POST['schoolName'])) {
					$schoolNameIsOk = false;
					$message = "Ce nom d'établissement existe déja";
				} else {
					$schoolNameIsOk = true;
				}

				if ($schoolNameIsOk && $userNameIsOk) {
					//add school and school administrator
					if ($_POST['adminPassword'] === $_POST['adminConfirmPassword']) {
						$UserManager->add(new User([
							"name" => htmlspecialchars($_POST['adminName']), 
							"mail" => htmlspecialchars($_POST['adminMail']), 
							"password" => password_hash($_POST['adminPassword'], PASSWORD_DEFAULT), 
							"school" => htmlspecialchars($_POST['schoolName']), 
							"isAdmin" => true, 
							"isModerator" => false]));

						$deadline = date('Y/m/d H:m:s', strtotime('+' . $_POST['schoolDuration'] . 'month', time()));

						$SchoolManager->add(new School([
							'idAdmin' => $SchoolManager->getLastInsertId(), 
							'name' => htmlspecialchars($_POST['schoolName']), 
							'nameAdmin' => htmlspecialchars($_POST['adminName']), 
							'code' => htmlspecialchars($_POST['schoolCode']), 
							'nbEleve' => htmlspecialchars($_POST['schoolNbEleve']), 
							'dateDeadline' => $deadline, 
							'logo' => 'public/images/question-mark.png']));

						$message = "L'établissement a bien été ajouté";
					} else {
						$message = "Les mots de passe ne correspondent pas";
					}
				}
			}
		}

		if (isset($message)) {
			RenderView::render('template.php', 'backend/addSchoolView.php', ['option' => ['addSchool'], 'message' => $message]);
		} else {
			RenderView::render('template.php', 'backend/addSchoolView.php', ['option' => ['addSchool']]);
		}
	}

	public function editSchool()
	{
		//edit school information
		$SchoolManager = new SchoolManager();
		$UserManager = new UserManager();

		if (isset($_GET['elem'])) {
			if (isset($_POST['id']) && $_POST['id'] > 0) {
				//editing school information
				switch ($_GET['elem']) {
					case 'name' :
						$SchoolManager->update($_POST['id'], 'name', $_POST['editName']);

						$message = "Le nom de l'établissement à été modifié";
					break;
					case 'admin' :
						if ($UserManager->nameExists($_POST['editAdmin'])) {
							$newAdmin = $UserManager->getUserByName($_POST['editAdmin']);
							$school = $SchoolManager->getOneById($_POST['id']);

							if ($school->getName() === $newAdmin->getSchool()) {
								$UserManager->setIsAdminByElem('id', $_POST['idAdmin'], false);
								$UserManager->setIsAdminByElem('name', $newAdmin->getName(), true);

								$SchoolManager->update($_POST['id'], 'idAdmin', $newAdmin->getId());
								$SchoolManager->update($_POST['id'], 'nameAdmin', $newAdmin->getName());

								$message = "L'administrateur de l'établissement à été modifié";
							} else {
								$message = "Cette personne ne fait pas parti de cet établissement";
							}
						} else {
							$message = "Ce nom d'utilisateur ne correspond à aucun compte éxistant";
						}
					break;
					case 'code' :
						$SchoolManager->update($_POST['id'], 'code', $_POST['editCode']);

						$message = "Le code d'affiliation a été modifié";
					break;
					case 'nbEleve' :
						$SchoolManager->update($_POST['id'], 'nbEleve', $_POST['editNbEleve']);

						$message = "Le nombre d'élèves a été modifié<br><br>Important : Le forfait à payer chaque mois étant basé sur le nombre de comptes actifs, si le nombre d'élèves indiqué est inférieur au nombre de comptes actifs, pensez à faire désactiver les comptes des élèves ne faisant plus parti de votre établissement.";
					break;
					case 'logo' :
						$SchoolManager->update($_POST['id'], 'logo', $_POST['editLogo']);

						$message = "Le logo de votre établissement a été modifié";
					break;
					case 'dateDeadline' :
						$school = $SchoolManager->getOneById($_POST['id']);
						$dateDeadline = \DateTime::createFromFormat("d/m/Y", $school->getDateDeadline());
						$strDeadline = $dateDeadline->format('Y/m/d');
						$newDeadline = date('Y/m/d H:m:s', strtotime('+' . $_POST['editDateDeadline'] . 'month', strtotime($strDeadline)));

						$SchoolManager->update($_POST['id'], 'dateDeadline', $newDeadline);

						$message = "La date d'échéance a été modifié";
					break;
				}
			}
		} else {
			//consulting school information
			$schools = $SchoolManager->getAll();
		}

		if (isset($schools)) {
			RenderView::render('template.php', 'backend/editSchoolView.php', ['option' => ['editSchool'], 'schools' => $schools]);
		} elseif (isset($message)) {
			RenderView::render('template.php', 'backend/editSchoolView.php', ['option' => ['editSchool'], 'message' => $message]);
		} else {
			RenderView::render('template.php', 'backend/editSchoolView.php', ['option' => ['editSchool']]);
		}
	}

	public function error(string $error_msg)
	{
		RenderView::render('template.php', 'frontend/errorView.php', ['error_msg' => $error_msg]);
	}
}
