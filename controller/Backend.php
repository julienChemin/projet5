<?php

namespace Chemin\ArtSchool\Model;

class Backend
{
	public function __construct()
	{
		if (isset($_SESSION['grade']) && ($_SESSION['grade'] === 'admin'  || $_SESSION['grade'] === 'moderator')) {
			$SchoolManager = new SchoolManager();
			$UserManager = new UserManager();
			if(!($SchoolManager->nameExists($_SESSION['school'])) || !($UserManager->exists($_SESSION['id']))) {
				session_destroy();
				if (isset($_COOKIE['artSchoolAdminId'])) {
					$this->useCookieToSignIn();
				} else {
					throw new \Exception('Veuillez vous reconnecter pour mettre à jour vos informations. Cocher la case "rester connecté" lors de la connection peu vous éviter ce genre de désagrément');
				}
			}
		} elseif (isset($_COOKIE['artSchoolAdminId'])) {
			$this->useCookieToSignIn();
		} else {
			if (isset($_GET['action']) && $_GET['action'] != 'resetPassword') {
				header('Location: indexAdmin.php');
			}
		}
	}

	public function useCookieToSignIn()
	{
		$UserManager = new UserManager();
		if ($UserManager->exists($_COOKIE['artSchoolAdminId'])) {
			$user = $UserManager->getOneById($_COOKIE['artSchoolAdminId']);

			$_SESSION['id'] = $user->getId();
			$_SESSION['pseudo'] = $user->getName();
			$_SESSION['school'] = $user->getSchool();
			if ($user->getIsAdmin()) {
				$_SESSION['grade'] = 'admin';
			} elseif ($user->getIsModerator()) {
				$_SESSION['grade'] = 'moderator';
			}
		} else {
			if (isset($_SESSION)) {
				session_destroy();
			}
			if (isset($_COOKIE['artSchoolAdminId'])) {
				setcookie('artSchoolAdminId', '', time()-3600, null, null, false, true);
			}
			header('Location: indexAdmin.php');
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
		if (isset($_SESSION)) {
			session_destroy();
		}

		if (isset($_COOKIE['artSchoolId'])) {
			setcookie('artSchoolId', '', time()-3600, null, null, false, true);
		}

		if (isset($_COOKIE['artSchoolAdminId'])) {
			setcookie('artSchoolAdminId', '', time()-3600, null, null, false, true);
		}

		header('Location: indexAdmin.php');
	}

	public function addSchool()
	{
		if ($_SESSION['school'] === 'allSchool') {
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

					//verify if user mail is already use
					if ($UserManager->mailExists($_POST['adminMail'])) {
						$userMailIsOk = false;
						$message = "Il existe déja un compte associé a cette adresse mail";
					} else {
						$userMailIsOk = true;
					}

					//verify if school name is already use
					if ($SchoolManager->nameExists($_POST['schoolName']) || $_POST['schoolName'] === 'allSchool') {
						$schoolNameIsOk = false;
						$message = "Ce nom d'établissement existe déja";
					} else {
						$schoolNameIsOk = true;
					}

					//verify if school code is already use
					$codeExist = $SchoolManager->affiliationCodeExists($_POST['schoolCode']);
					if ($codeExist['exist']) {
						$schoolCodeIsOk = false;
						$message = "Ce code d'affiliation ne peut pas être utilisé";
					} else {
						$schoolCodeIsOk = true;
					}

					//verify 'nbEleve'
					$_POST['schoolNbEleve'] = intval($_POST['schoolNbEleve']);
					if ($_POST['schoolNbEleve'] <= 0) {
						$nbEleveIsOk = false;
						$message = "Le nombre d'élève doit être supérieur a 0";
					} else {
						$nbEleveIsOk = true;
					}

					if ($userNameIsOk && $userMailIsOk && $schoolNameIsOk && $schoolCodeIsOk && $nbEleveIsOk) {
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
		} else {
			header('Location: indexAdmin.php');
		}
	}

	public function moderatSchool()
	{
		if ($_SESSION['grade'] === 'admin') {
			$SchoolManager = new SchoolManager();

			$schools = $SchoolManager->getSchoolByName($_SESSION['school']);

			if (isset($schools)) {
				RenderView::render('template.php', 'backend/moderatSchoolView.php', ['option' => ['moderatSchool'], 'schools' => $schools]);
			} else {
				RenderView::render('template.php', 'backend/moderatSchoolView.php', ['option' => ['moderatSchool']]);
			}
		} else {
			header('Location: indexAdmin.php');
		}
	}

	public function editSchool()
	{
		if ($_SESSION['grade'] === 'admin' 
		&& ($_SESSION['school'] === 'allSchool' || (!empty($_POST['schoolName']) && $_POST['schoolName'] === $_SESSION['school']))) {
			$UserManager = new UserManager();
			$SchoolManager = new SchoolManager();

			if (!empty($_POST['elem'])) {
				//editing school information
				switch ($_POST['elem']) {
					case 'name' :
						if ($_POST['editName'] !== 'allSchool' && !$SchoolManager->nameExists($_POST['editName'])) {
							$schoolName = htmlspecialchars($_POST['schoolName']);
							$newSchoolName = htmlspecialchars($_POST['editName']);

							//edit school name in user info
							$users = $UserManager->getUsersBySchool($schoolName);
							foreach ($users as $user) {
								$UserManager->updateById($user->getId(), 'school', $newSchoolName);
							}

							//edit school name
							$SchoolManager->updateByName($schoolName, 'name', $newSchoolName);

							$message = "Le nom de l'établissement à été modifié";
						} else {
							$message = "Ce nom est déja utilisé";
						}
					break;
					case 'admin' :
						if ($UserManager->nameExists($_POST['editAdmin'])) {
							$newAdmin = $UserManager->getUserByName($_POST['editAdmin']);
							$school = $SchoolManager->getSchoolByName($_POST['schoolName']);

							if ($school->getName() === $newAdmin->getSchool()) {
								$UserManager->updateByName($newAdmin->getName(), 'grade', ['isAdmin' => true, 'isModerator' => false]);

								$SchoolManager->updateByName($_POST['schoolName'], 'idAdmin', $newAdmin->getId())
											->updateByName($_POST['schoolName'], 'nameAdmin', $newAdmin->getName());

								$message = "L'administrateur de l'établissement à été modifié";
							} else {
								$message = "Cette personne ne fait pas parti de cet établissement";
							}
						} else {
							$message = "Ce nom d'utilisateur ne correspond à aucun compte éxistant";
						}
					break;
					case 'code' :
						$SchoolManager->updateByName($_POST['schoolName'], 'code', $_POST['editCode']);

						$message = "Le code d'affiliation a été modifié";
					break;
					case 'nbEleve' :
						$SchoolManager->updateByName($_POST['schoolName'], 'nbEleve', $_POST['editNbEleve']);

						$message = "Le nombre d'élèves a été modifié<br><br>Important : Le forfait à payer chaque mois étant basé sur le nombre de comptes actifs, si le nombre d'élèves indiqué est inférieur au nombre de comptes actifs, pensez à faire désactiver les comptes des élèves ne faisant plus parti de votre établissement.";
					break;
					case 'logo' :
						$SchoolManager->updateByName($_POST['schoolName'], 'logo', $_POST['editLogo']);

						$message = "Le logo de votre établissement a été modifié";
					break;
					case 'dateDeadline' :
						$school = $SchoolManager->getSchoolByName($_POST['schoolName']);
						$dateDeadline = \DateTime::createFromFormat("d/m/Y", $school->getDateDeadline());
						$strDeadline = $dateDeadline->format('Y/m/d');
						$newDeadline = date('Y/m/d H:m:s', strtotime('+' . $_POST['editDateDeadline'] . 'month', strtotime($strDeadline)));

						$SchoolManager->updateByName($_POST['schoolName'], 'dateDeadline', $newDeadline);

						$message = "La date d'échéance a été modifié";
					break;
					default :
						$message = "Les informations renseignées sont incorrectes";
				}
			}

			if (isset($message)) {
				RenderView::render('template.php', 'backend/editSchoolView.php', ['message' => $message]);
			} else {
				RenderView::render('template.php', 'backend/editSchoolView.php');
			}
		} else {
			throw new \Exception("Vous ne pouvez pas accéder a cette page");
		}
	}

	public function moderatAdmin()
	{
		if ($_SESSION['grade'] === 'admin') {
			$UserManager = new UserManager();
			$users = $UserManager->getUsersBySchool($_SESSION['school'], 'admin');

			$SchoolManager = new SchoolManager();
			$schools = $SchoolManager->getSchoolByName($_SESSION['school']);


			if ($_SESSION['school'] === 'allSchool') {
				//order users by school
				$arrUsersBySchool = [];

				foreach ($users as $user) {
					if ($user->getSchool() !== 'allSchool') {
						$arrUsersBySchool[$user->getSchool()][] = $user;
					}
				}

				//count nb moderator order by school
				foreach ($schools as $school) {
					$nbModerator = 0;

					foreach ($arrUsersBySchool[$school->getName()] as $user) {
						if ($user->getIsModerator()) {
							$nbModerator++;
						}
					}
					$arrNbModerator[$school->getName()] = $nbModerator;
				}
				RenderView::render('template.php', 'backend/moderatAdminView.php', 
					['users' => $arrUsersBySchool, 'schools' => $schools, 'nbModerator' => $arrNbModerator, 'option' => ['modal']]);
			} elseif ($_SESSION['grade'] === 'admin') {
				$nbModerator = 0;

				foreach ($users as $user) {
					if ($user->getIsModerator()) {
						$nbModerator++;
					}
				}
				RenderView::render('template.php', 'backend/moderatAdminView.php', 
					['users' => $users, 'schools' => $schools, 'nbModerator' => $nbModerator, 'option' => ['modal']]);
			}
		} else {
			throw new \Exception("Cette page est réservé aux administrateurs");
		}
	}

	public function moderatUsers()
	{
		if (isset($_SESSION['grade']) && ($_SESSION['grade'] === 'admin' || $_SESSION['grade'] === 'moderator')) {
			$UserManager = new UserManager();
			$users = $UserManager->getUsersBySchool($_SESSION['school'], 'user');

			$SchoolManager = new SchoolManager();
			$schools = $SchoolManager->getSchoolByName($_SESSION['school']);

			if ($_SESSION['school'] === 'allSchool') {
				//order users by school
				$arrUsersBySchool = [];
				$arrIsActive = [];

				foreach ($users as $user) {
					if ($user->getSchool() !== 'allSchool') {
						if ($user->getIsActive()) {
							$arrUsersBySchool[$user->getSchool()]['active'] = $user;
							
						} else {
							$arrUsersBySchool[$user->getSchool()]['inactive'] = $user;
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
				RenderView::render('template.php', 'backend/moderatUsersView.php', 
					['users' => $arrUsersBySchool, 'schools' => $schools, 'isActive' => $arrIsActive, 'option' => ['modal']]);
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


				RenderView::render('template.php', 'backend/moderatUsersView.php', 
					['users' => $users, 'schools' => $schools, 'isActive' => $arrIsActive, 'option' => ['modal']]);
			}
		} else {
			throw new \Exception("Cette page est réservé aux administrateurs / modérateurs");
		}
	}

	public function editGrade()
	{
		if (isset($_GET['userName'], $_GET['schoolName'], $_GET['isAdmin'], $_GET['isModerator'])) {
			if ($_GET['isAdmin'] === 'true') {
				$isAdmin = true;
			} else {
				$isAdmin = false;
			}

			if ($_GET['isModerator'] === 'true') {
				$isModerator = true;
			} else {
				$isModerator = false;
			}

			if (!($isAdmin && $isModerator)) {
				$UserManager = new UserManager();

				if ($isModerator) {
					$user = $UserManager->getUserByName($_GET['userName']);
					
					if (!$user->getIsAdmin()) {
						//normal user to moderator, nb active account -1
						$SchoolManager = new SchoolManager();
						$school = $SchoolManager->getSchoolByName($_GET['schoolName']);
						$nbActiveAccount = $school->getNbActiveAccount() - 1;
						$SchoolManager->updateByName($_GET['schoolName'], 'nbActiveAccount', $nbActiveAccount);
					}
				} elseif (!$isModerator && !$isAdmin) {
					//moderator to normal user, nb active account +1
					$SchoolManager = new SchoolManager();
					$school = $SchoolManager->getSchoolByName($_GET['schoolName']);
					$nbActiveAccount = $school->getNbActiveAccount();
					if ($nbActiveAccount < $school->getNbEleve()) {
						$SchoolManager->updateByName($_GET['schoolName'], 'nbActiveAccount', $nbActiveAccount + 1);
					} else {
						throw new \Exception("Il est impossible d'effectuer cette action, vous avez atteint le nombre maximum de compte utilisateur actif");
					}
				}

				$UserManager->updateByName($_GET['userName'], 'grade', ['isAdmin' => $isAdmin, 'isModerator' => $isModerator]);
			} else {
				throw new \Exception("Un utilisateur ne peut pas être à la fois modérateur et administrateur");
			}
		} else {
			throw new \Exception("Les informations renseignées sont incorrectes");
		}

		if (isset($_SERVER['HTTP_REFERER'])) {
			header('Location: ' . $_SERVER['HTTP_REFERER']);
		} else {
			header('Location: indexAdmin.php');
		}
	}

	public function toggleIsActive()
	{
		if (!empty($_GET['userName']) && !empty($_GET['schoolName'])) {
			if ($_SESSION['school'] === 'allSchool' || $_SESSION['school'] === $_GET['schoolName']) {
				$SchoolManager = new SchoolManager();
				$UserManager = new UserManager();

				$schoolExist = $SchoolManager->nameExists($_GET['schoolName']);
				$userExist = $UserManager->nameExists($_GET['userName']);

				if ($schoolExist && $userExist) {
					$school = $SchoolManager->getSchoolByName($_GET['schoolName']);
					$nbActiveAccount = $school->getNbActiveAccount();
					$nbEleve = $school->getNbEleve();

					if ($nbActiveAccount < $nbEleve) {
						$user = $UserManager->getUserByName($_GET['userName']);

						if ($user->getIsActive()) {
							//account is active
							$UserManager->updateByName($_GET['userName'], 'isActive', false);
							//nb active account - 1
							$SchoolManager->updateByName($_GET['schoolName'], 'nbActiveAccount', $nbActiveAccount - 1);
						} else {
							//account is inactive
							$UserManager->updateByName($_GET['userName'], 'isActive', true);
							//nb active account + 1
							$SchoolManager->updateByName($_GET['schoolName'], 'nbActiveAccount', $nbActiveAccount + 1);
						}

						if (isset($_SERVER['HTTP_REFERER'])) {
							header('Location: ' . $_SERVER['HTTP_REFERER']);
						} else {
							header('Location: indexAdmin.php');
						}
					} else {
						throw new \Exception("Il est impossible d'effectuer cette action, vous avez atteint le nombre maximum de compte utilisateur actif");
					}
				} else {
					throw new \Exception("Les informations renseignées sont incorrectes");
				}
			} else {
				throw new \Exception("Vous ne pouvez pas modifié les informations d'un compte qui ne fait pas parti de votre établissement");
			}
		} else {
			throw new \Exception("Les informations renseignées sont incorrectes");
		}
	}

	public function delete()
	{
		if  (!empty($_GET['elem'])) {
			switch ($_GET['elem']) {
				case 'school' :
				break;
				case 'user' :
					if (!empty($_GET['userName']) && !empty($_GET['schoolName'])) {
						if ($_SESSION['school'] === 'allSchool' || $_SESSION['school'] === $_GET['schoolName']) {
							$SchoolManager = new SchoolManager();
							$UserManager = new UserManager();

							$schoolExist = $SchoolManager->nameExists($_GET['schoolName']);
							$userExist = $UserManager->nameExists($_GET['userName']);

							if ($schoolExist && $userExist) {
								$user = $UserManager->getUserByName($_GET['userName']);
								if (!$user->getIsAdmin() && !$user->getIsModerator()) {
									//delete reports related to this account

									//delete content publish by this account

									//if account is active, nb active account -1
									if ($user->getIsActive()) {
										$school = $SchoolManager->getSchoolByName($_GET['schoolName']);
										$SchoolManager->updateByName($_GET['schoolName'], 'nbActiveAccount', $school->getNbActiveAccount() - 1);
									}
									//delete account
									$UserManager->delete($user->getId());
								} else {
									throw new \Exception("Vous ne pouvez pas supprimer un compte administrateur / modérateur");
								}
							} else {
								throw new \Exception("Les informations renseignées sont incorrectes");
							}
						} else {
							throw new \Exception("Vous ne pouvez pas modifié les informations d'un compte qui ne fait pas parti de votre établissement");
						}
					} else {
						throw new \Exception("Les informations renseignées sont incorrectes");
					}
				break;
				default :
					throw new \Exception("Les informations renseignées sont incorrectes");
			}

		}
	}

	public function error(string $error_msg)
	{
		RenderView::render('template.php', 'frontend/errorView.php', ['error_msg' => $error_msg]);
	}
}
