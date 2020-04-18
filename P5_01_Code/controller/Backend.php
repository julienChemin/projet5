<?php

namespace Chemin\ArtSchool\Model;

class Backend
{
	public function verifyInformation()
	{
		if (isset($_SESSION['grade']) && ($_SESSION['grade'] === ADMIN  || $_SESSION['grade'] === MODERATOR)) {
			//user is connect as admin or moderator
			$SchoolManager = new SchoolManager();
			$UserManager = new UserManager();
			if((!$SchoolManager->nameExists($_SESSION['school']) && !($_SESSION['school'] === ALL_SCHOOL))
			|| !$UserManager->nameExists($_SESSION['pseudo'])) {
				//if user name don't exist or if school name don't exist and isn't "allSchool" 
				session_destroy();
				if (isset($_COOKIE['artSchoolAdminId'])) {
					$this->useCookieToSignIn();
				} else {
					throw new \Exception("Certaines informations lié a votre compte ne sont plus valide,
					 veuillez vous reconnecter pour mettre à jour ces informations.
					 Cocher la case 'rester connecté' lors de la connection peu vous éviter ce genre de désagrément");
				}
			}
		} elseif (isset($_SESSION['grade']) && $_SESSION['grade'] !== ADMIN  && $_SESSION['grade'] !== MODERATOR) {
			//user is connect but not as admin or moderator
			header('Location: index.php');
		} elseif (isset($_COOKIE['artSchoolAdminId'])) {
			//user is not connect, looking for cookie
			$this->useCookieToSignIn();
		} else {
			//home
			if (isset($_GET['action']) && $_GET['action'] != 'resetPassword') {
				header('Location: indexAdmin.php');
			}
		}
	}

	public function useCookieToSignIn()
	{
		$cookie = explode("-", $_COOKIE['artSchoolAdminId']);

		if (count($cookie) === 2) {
			$UserManager = new UserManager();

			$userId = htmlspecialchars($cookie[0]);
			$userPassword = htmlspecialchars($cookie[1]);

			if ($UserManager->exists($userId)) {
				$user = $UserManager->getOneById($userId);

				if ($user->getPassword() === $userPassword) {
					$SchoolManager = new SchoolManager();

					if(!$SchoolManager->nameExists($user->getSchool()) && !($user->getSchool() === ALL_SCHOOL)) {
						//if school name don't exist and isn't "allSchool"
						$this->cookieDestroy();
						throw new \Exception("Le nom de l'établissement scolaire auquel vous êtes affilié n'existe pas / plus.
							Un message d'erreur a été envoyé à un administrateur du site et sera traité dans les plus brefs délais.
							Merci de votre compréhension");
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

	public function connect(User $user)
	{
		$_SESSION['id'] = $user->getId();
		$_SESSION['pseudo'] = $user->getName();
		$_SESSION['school'] = $user->getSchool();

		if ($user->getIsAdmin()) {
			$_SESSION['grade'] = ADMIN;
		} elseif ($user->getIsModerator()) {
			$_SESSION['grade'] = MODERATOR;
		}
	}

	public function disconnect()
	{
		if (isset($_SESSION)) {
			session_destroy();
		}

		$this->cookieDestroy();

		header('Location: indexAdmin.php');
	}

	public function cookieDestroy()
	{
		if (isset($_COOKIE['artSchoolId'])) {
			setcookie('artSchoolId', '', time()-3600, null, null, false, true);
		}

		if (isset($_COOKIE['artSchoolAdminId'])) {
			setcookie('artSchoolAdminId', '', time()-3600, null, null, false, true);
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
					if ($user->getIsAdmin() || $user->getIsModerator()) {
						if (isset($_POST['stayConnect'])) {
							//if user want to stay connect
							setcookie('artSchoolAdminId', $user->getId() . '-' . $user->getPassword(), time()+(365*24*3600), null, null, false, true);
						}
						$this->connect($user);
						
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
				$UserManager->updateById($user->getId(), 'temporaryPassword', $temporaryPassword);

				$subject = 'Recuperation de mot de passe';
				$content = "Bonjour " . $user->getName() . ", vous avez demande a reinitialiser votre mot de passe.<br><br>
					En suivant <a style='text-decoration: underline;' href='http://julienchemin.fr/projet5/indexAdmin.php?action=resetPassword&key=" . $temporaryPassword . "&id=" . $user->getId() . "'>ce lien</a> vous serez redirige vers une page pour modifier votre mot de passe.<br><br>
					Si le lien ne fonctionne pas, rendez vous a l'adresse suivante : <br>http://julienchemin.fr/projet5/indexAdmin.php?action=resetPassword&key=" . $temporaryPassword . "&id=" . $user->getId() . "<br><br>L'equipe d'ArtSchool vous remercie.";
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
			RenderView::render('template.php', 'backend/indexAdminView.php', ['option' => ['forgetPassword', 'signIn'], 'message' => $message]);
		} else {
			RenderView::render('template.php', 'backend/indexAdminView.php', ['option' => ['forgetPassword', 'signIn']]);
		}
	}

	public function resetPassword()
	{
		//form for reset password
		if (isset($_GET['key']) && isset($_GET['id'])) {
			$UserManager = new UserManager();

			if ($UserManager->exists($_GET['id'])) {
				$user = $UserManager->getOneById($_GET['id']);
				$temporaryPassword = $user->getTemporaryPassword();

				if ($temporaryPassword === $_GET['key']) {
					if ($user->getBeingReset()) {
						if (isset($_GET['wrongPassword'])) {
							switch ($_GET['wrongPassword']) {
								case 1 :
									$message = "Vous devez entrer deux mot de passe identiques";
								break;
								case 2 :
									$message = "Le nouveau mot de passe doit être différent de l'ancien";
								break;
								default :
									$message = "Il y a eu une erreur au niveau de mot de passe";
							}
						}

						if (isset($message)) {
							RenderView::render('template.php', 'backend/resetPasswordView.php', ['user' => $user, 'message' => $message]);
						} else {
							RenderView::render('template.php', 'backend/resetPasswordView.php', ['user' => $user]);
						}
					} else {
						throw new \Exception("Pour réinitialiser votre mot de passe, vous devez passer directement par le lien qui vous a été envoyé par mail");
					}
				} else {
					throw new \Exception("Les informations renseignées sont incorrectes");
				}
			} else {
				throw new \Exception("Les informations renseignées sont incorrectes");
			}
		// check form data
		} else if (isset($_POST['newPassword']) && isset($_POST['confirmNewPassword'])) {
			if ($_POST['newPassword'] === $_POST['confirmNewPassword']) {
				$UserManager = new UserManager();

				if ($UserManager->exists($_POST['id'])) {
					$user = $UserManager->getOneById($_POST['id']);
					$temporaryPassword = $user->getTemporaryPassword();

					if ($temporaryPassword === $_POST['key']) {
						if ($user->getBeingReset()) {
							if (!password_verify($_POST['newPassword'], $user->getPassword())) {
								//new password is correct
								$UserManager->updateById($user->getId(), 'password', password_hash($_POST['newPassword'], PASSWORD_DEFAULT));

								$message = "Le mot de passe a bien été modifié.";

								RenderView::render('template.php', 'backend/resetPasswordView.php', ['message' => $message]);
							} else {
								//new password is the same as the old one
								header('Location: indexAdmin.php?action=resetPassword&key=' . $_POST['key'] . '&id=' . $_POST['id'] . '&wrongPassword=2');
							}
						} else {
							throw new \Exception("Ce lien pour réinitialiser votre mot de passe n'est pas valide");
						}
					} else {
						throw new \Exception("Les informations renseignées sont incorrectes");
					}
				} else {
					throw new \Exception("Les informations renseignées sont incorrectes");
				}
			} else {
				//new password is wrong
				header('Location: indexAdmin.php?action=resetPassword&key=' . $_POST['key'] . '&id=' . $_POST['id'] . '&wrongPassword=1');
			}
		} else {
			throw new \Exception("Ce lien pour réinitialiser votre mot de passe n'est pas valide");
		}
	}

	public function addSchool()
	{
		if ($_SESSION['school'] === ALL_SCHOOL) {
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
					if ($SchoolManager->nameExists($_POST['schoolName']) || $_POST['schoolName'] === ALL_SCHOOL) {
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
							$formatedDateDeadline = date('d/m/Y', strtotime('+' . $_POST['schoolDuration'] . 'month', time()));

							$SchoolManager->add(new School([
								'idAdmin' => $SchoolManager->getLastInsertId(), 
								'name' => htmlspecialchars($_POST['schoolName']), 
								'nameAdmin' => htmlspecialchars($_POST['adminName']), 
								'code' => htmlspecialchars($_POST['schoolCode']), 
								'nbEleve' => htmlspecialchars($_POST['schoolNbEleve']), 
								'dateDeadline' => $deadline, 
								'logo' => 'public/images/question-mark.png']));

							//first history entry
							$HistoryManager = new HistoryManager();
							$HistoryManager->addEntry(new HistoryEntry([
								'idSchool' => $SchoolManager->getLastInsertId(),
								'category' => 'activityPeriod',
								'entry' => "Bienvenue sur ArtSchool ! Vous vous êtes inscrit pour une période de " . $_POST['schoolDuration'] . " mois, avec " . $_POST['schoolNbEleve'] . " compte(s) affiliés a votre établissement. L'abonnement prendra fin le " . $formatedDateDeadline]));

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
		if ($_SESSION['grade'] === ADMIN) {
			$SchoolManager = new SchoolManager();

			$schools = $SchoolManager->getSchoolByName($_SESSION['school']);

			if (isset($schools)) {
				RenderView::render('template.php', 'backend/moderatSchoolView.php', ['option' => ['buttonToggleSchool', 'moderatSchool'], 'schools' => $schools]);
			} else {
				RenderView::render('template.php', 'backend/moderatSchoolView.php', ['option' => ['buttonToggleSchool', 'moderatSchool']]);
			}
		} else {
			header('Location: indexAdmin.php');
		}
	}

	public function editSchool()
	{
		if ($_SESSION['grade'] === ADMIN 
		&& ($_SESSION['school'] === ALL_SCHOOL || (!empty($_POST['schoolName']) && $_POST['schoolName'] === $_SESSION['school']))) {
			$UserManager = new UserManager();
			$SchoolManager = new SchoolManager();

			if (!empty($_POST['elem'])) {
				//consulting form to edit school information
				if ($SchoolManager->nameExists($_POST['schoolName'])) {
					switch ($_POST['elem']) {
						case 'name' :
							if ($_POST['editName'] !== ALL_SCHOOL && !$SchoolManager->nameExists($_POST['editName'])) {
								//edit school name in user info
								$users = $UserManager->getUsersBySchool($_POST['schoolName']);
								foreach ($users as $user) {
									$UserManager->updateById($user->getId(), 'school', $_POST['editName']);
								}

								//edit school name
								$SchoolManager->updateByName($_POST['schoolName'], 'name', $_POST['editName']);

								//MAJ school name in session
								if ($_SESSION['school'] !== ALL_SCHOOL) {
									$_SESSION['school'] = $_POST['editName'];
								}

								//add history entry
								$school = $SchoolManager->getSchoolByName($_POST['editName']);
								$HistoryManager = new HistoryManager();
								$HistoryManager->addEntry(new HistoryEntry([
									'idSchool' => $school->getId(),
									'category' => 'profil',
									'entry' => $_SESSION['pseudo'] . ' a modifié le nom de votre établissement en : ' . $_POST['editName']]));

								$message = "Le nom de l'établissement a été modifié";
							} else {
								$message = "Ce nom est déja utilisé";
							}
						break;
						case 'admin' :
							if ($UserManager->nameExists($_POST['editAdmin'])) {
								$newAdmin = $UserManager->getUserByName($_POST['editAdmin']);
								$school = $SchoolManager->getSchoolByName($_POST['schoolName']);

								if ($school->getName() === $newAdmin->getSchool()) {
									if ($newAdmin->getIsActive()) {
										$UserManager->updateByName($newAdmin->getName(), 'grade', ['isAdmin' => true, 'isModerator' => false]);

										$SchoolManager->updateByName($_POST['schoolName'], 'idAdmin', $newAdmin->getId())
													->updateByName($_POST['schoolName'], 'nameAdmin', $newAdmin->getName());

										//add history entry
										$HistoryManager = new HistoryManager();
										$HistoryManager->addEntry(new HistoryEntry([
											'idSchool' => $school->getId(),
											'category' => 'profil',
											'entry' => $_SESSION['pseudo'] . ' a remplacé l\'administrateur principal par : ' . $newAdmin->getName()]));

										$message = "L'administrateur de l'établissement a été modifié";
									} else {
										$message = "Ce compte est inactif";
									}
								} else {
									$message = "Cette personne ne fait pas parti de cet établissement";
								}
							} else {
								$message = "Ce nom d'utilisateur ne correspond à aucun compte éxistant";
							}
						break;
						case 'code' :
							$codeExist = $SchoolManager->affiliationCodeExists($_POST['editCode'])["exist"];
							if (!$codeExist) {
								$SchoolManager->updateByName($_POST['schoolName'], 'code', $_POST['editCode']);

								//add history entry
								$school = $SchoolManager->getSchoolByName($_POST['schoolName']);
								$HistoryManager = new HistoryManager();
								$HistoryManager->addEntry(new HistoryEntry([
									'idSchool' => $school->getId(),
									'category' => 'profil',
									'entry' => $_SESSION['pseudo'] . ' a modifié le code d\'affiliation en : ' . $_POST['editCode']]));

								$message = "Le code d'affiliation a été modifié";
							} else {
								$message = "Veuillez choisir un autre code";
							}
						break;
						case 'nbEleve' :
							if ($_SESSION['school'] === ALL_SCHOOL) {
								$school = $SchoolManager->getSchoolByName($_POST['schoolName']);

								if (intval($school->getNbActiveAccount()) <= $_POST['editNbEleve']) {
									var_dump($_POST['editNbEleve']);
									$SchoolManager->updateByName($_POST['schoolName'], 'nbEleve', intval($_POST['editNbEleve']));

									//add history entry
									$HistoryManager = new HistoryManager();
									$HistoryManager->addEntry(new HistoryEntry([
										'idSchool' => $school->getId(),
										'category' => 'profil',
										'entry' => 'Le nombre maximum de compte affilié à votre établissement est passé à ' . $_POST['editNbEleve']]));

									$message = "Le nombre d'élèves a été modifié";
								} else {
									$message = "Le nombre de compte(s) actif(s) pour cette établissement est supérieur au nombre de compte(s) disponible(s) que vous annoncez";
								}
							} else {
								$message = "Vous n'avez pas accès à cette option";
							}
						break;
						case 'logo' :
							if (!empty($_POST['editLogo'])) {
								$SchoolManager->updateByName($_POST['schoolName'], 'logo', $_POST['editLogo']);

								//add history entry
								$school = $SchoolManager->getSchoolByName($_POST['schoolName']);
								$HistoryManager = new HistoryManager();
								$HistoryManager->addEntry(new HistoryEntry([
									'idSchool' => $school->getId(),
									'category' => 'profil',
									'entry' => $_SESSION['pseudo'] . ' a modifié le logo de l\'établissement']));

								$message = "Le logo de votre établissement a été modifié";
							} elseif (!empty($_FILES['uploadLogo'])) {
								$schoolName = $_POST['schoolName'];

								require('view/upload.php');

								if (!empty($final_path)) {
									$SchoolManager->updateByName($schoolName, 'logo', $final_path);

									//add history entry
									$school = $SchoolManager->getSchoolByName($schoolName);
									$HistoryManager = new HistoryManager();
									$HistoryManager->addEntry(new HistoryEntry([
										'idSchool' => $school->getId(),
										'category' => 'profil',
										'entry' => $_SESSION['pseudo'] . ' a modifié le logo de l\'établissement']));
								}

								header('Location: indexAdmin.php?action=moderatSchool');
							} else {
								if (isset($_SERVER['HTTP_REFERER'])) {
									header('Location: ' . $_SERVER['HTTP_REFERER']);
								} else {
									header('Location: indexAdmin.php?action=moderatSchool');
								}
							}
						break;
						case 'dateDeadline' :
							$school = $SchoolManager->getSchoolByName($_POST['schoolName']);
							$dateDeadline = \DateTime::createFromFormat("d/m/Y", $school->getDateDeadline());
							$strDeadline = $dateDeadline->format('Y/m/d');
							$newDeadline = date('Y/m/d H:m:s', strtotime('+' . $_POST['editDateDeadline'] . 'month', strtotime($strDeadline)));
							$formatedNewDeadline = date('d/m/Y', strtotime('+' . $_POST['editDateDeadline'] . 'month', strtotime($strDeadline)));

							$SchoolManager->updateByName($_POST['schoolName'], 'dateDeadline', $newDeadline);

							//add history entry
							$HistoryManager = new HistoryManager();
							$HistoryManager->addEntry(new HistoryEntry([
								'idSchool' => $school->getId(),
								'category' => 'activityPeriod',
								'entry' => 'La date de fin d\'abonnement a été repoussé jusqu\'au ' . $formatedNewDeadline]));

							$message = "La date d'échéance a été modifié";
						break;
						case 'toActive' :
							if ($_SESSION['school'] === ALL_SCHOOL) {
								$users = $UserManager->getUsersBySchool($_POST['schoolName'], 'admin');

								foreach ($users as $user) {
									$UserManager->updateByName($user->getName(), 'isActive', true);
								}

								$SchoolManager->updateByName($_POST['schoolName'], 'isActive', true)
											->updateByName($_POST['schoolName'], 'nbEleve', intval($_POST['editToActive']));

								//add history entry
								$school = $SchoolManager->getSchoolByName($_POST['schoolName']);
								$HistoryManager = new HistoryManager();
								$HistoryManager->addEntry(new HistoryEntry([
									'idSchool' => $school->getId(),
									'category' => 'activityPeriod',
									'entry' => 'L\'établissement a été activé']));

								$message = "L'établissement a été activé, avec " . $_POST['editToActive'] . " compte affilié maximum";
							} else {
								throw new \Exception("Vous ne pouvez pas accéder a cette page");
							}
						break;
						case 'toInactive' :
							if ($_SESSION['school'] === ALL_SCHOOL) {
								$users = $UserManager->getUsersBySchool($_POST['schoolName']);

								foreach ($users as $user) {
									$UserManager->updateByName($user->getName(), 'isActive', false);
								}

								$SchoolManager->updateByName($_POST['schoolName'], 'isActive', false)
											->updateByName($_POST['schoolName'], 'nbEleve', 0)
											->updateByName($_POST['schoolName'], 'nbActiveAccount', 0);

								//add history entry
								$school = $SchoolManager->getSchoolByName($_POST['schoolName']);
								$HistoryManager = new HistoryManager();
								$HistoryManager->addEntry(new HistoryEntry([
									'idSchool' => $school->getId(),
									'category' => 'activityPeriod',
									'entry' => 'L\'établissement a été désactivé']));

								$message = "L'établissement a été désactivé";
							} else {
								throw new \Exception("Vous ne pouvez pas accéder a cette page");
							}
						break;
						default :
							$message = "Les informations renseignées sont incorrectes";
					}
				} else {
					throw new \Exception("Le nom de l'établissement renseigné n'existe pas");
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
		if ($_SESSION['grade'] === ADMIN) {
			$UserManager = new UserManager();
			$SchoolManager = new SchoolManager();

			if (isset($_GET['option'], $_POST['schoolName']) && $_GET['option'] === 'addModerator') {
				//add new moderator
				if ($_SESSION['school'] === $_POST['schoolName'] || $_SESSION['school'] === ALL_SCHOOL) {
					//verify if school is active
					$school = $SchoolManager->getSchoolByName($_POST['schoolName']);

					if ($school->getIsActive()) {
						$schoolIsActive = true;
					} else {
						$schoolIsActive = false;
						$message = "Vous ne pouvez pas ajouter de modérateur, cet établissement n'est pas actif";
					}

					//verify if user name is already use
					if ($UserManager->nameExists($_POST['moderatorName'])) {
						$userNameIsOk = false;
						$message = "Ce nom d'utilisateur existe déja";
					} else {
						$userNameIsOk = true;
					}

					//verify if user mail is already use
					if ($UserManager->mailExists($_POST['moderatorMail'])) {
						$userMailIsOk = false;
						$message = "Il existe déja un compte associé a cette adresse mail";
					} else {
						$userMailIsOk = true;
					}

					if ($userNameIsOk && $userMailIsOk && $schoolIsActive) {
						if ($_POST['moderatorPassword'] === $_POST['moderatorConfirmPassword']) {
							$UserManager->add(new User([
								"name" => $_POST['moderatorName'], 
								"mail" => $_POST['moderatorMail'], 
								"password" => password_hash($_POST['moderatorPassword'], PASSWORD_DEFAULT), 
								"school" => $_POST['schoolName'], 
								"isAdmin" => false, 
								"isModerator" => true]));

							//add history entry
							$HistoryManager = new HistoryManager();
							$HistoryManager->addEntry(new HistoryEntry([
								'idSchool' => $school->getId(),
								'category' => 'account',
								'entry' => $_SESSION['pseudo'] . ' a créé un compte modérateur : ' . $_POST['moderatorName']]));

							$message = "Le modérateur a bien été ajouté";
						} else {
							$message = "Les mots de passe ne correspondent pas";
						}
					}
				} else {
					$message = "Une erreur s'est glissée dans le formulaire, veuillez réessayer";
				}
			}

			$users = $UserManager->getUsersBySchool($_SESSION['school'], 'admin');
			$schools = $SchoolManager->getSchoolByName($_SESSION['school']);

			if ($_SESSION['school'] === ALL_SCHOOL) {
				//order users by school
				$arrUsersBySchool = [];

				foreach ($users as $user) {
					if ($user->getSchool() !== ALL_SCHOOL) {
						$arrUsersBySchool[$user->getSchool()][] = $user;

						//count nb moderator order by school
						if (!isset($arrNbModerator[$user->getSchool()])) {
							$arrNbModerator[$user->getSchool()] = 0;
						}

						if ($user->getIsModerator()) {
							$arrNbModerator[$user->getSchool()] += 1;
						}
					}
				}

				if (isset($message)) {
					RenderView::render('template.php', 'backend/moderatAdminView.php', 
						['users' => $arrUsersBySchool, 'schools' => $schools, 'nbModerator' => $arrNbModerator, 'message' => $message,
						 'option' => ['moderatAdmin', 'buttonToggleSchool']]);
				} else {
					RenderView::render('template.php', 'backend/moderatAdminView.php', 
						['users' => $arrUsersBySchool, 'schools' => $schools, 'nbModerator' => $arrNbModerator, 'option' => ['moderatAdmin', 'buttonToggleSchool']]);
				}
			} elseif ($_SESSION['grade'] === ADMIN) {
				$nbModerator = 0;

				foreach ($users as $user) {
					if ($user->getIsModerator()) {
						$nbModerator++;
					}
				}
				
				if (isset($message)) {
					RenderView::render('template.php', 'backend/moderatAdminView.php', 
						['users' => $users, 'schools' => $schools, 'nbModerator' => $nbModerator, 'message' => $message, 'option' => ['moderatAdmin', 'buttonToggleSchool']]);
				} else {
					RenderView::render('template.php', 'backend/moderatAdminView.php', 
						['users' => $users, 'schools' => $schools, 'nbModerator' => $nbModerator, 'option' => ['moderatAdmin', 'buttonToggleSchool']]);
				}
			}
		} else {
			throw new \Exception("Cette page est réservé aux administrateurs");
		}
	}

	public function moderatUsers()
	{
		if (isset($_SESSION['grade']) && ($_SESSION['grade'] === ADMIN || $_SESSION['grade'] === MODERATOR)) {
			$UserManager = new UserManager();
			$users = $UserManager->getUsersBySchool($_SESSION['school'], 'user');

			$SchoolManager = new SchoolManager();
			$schools = $SchoolManager->getSchoolByName($_SESSION['school']);

			if ($_SESSION['school'] === ALL_SCHOOL) {
				//order users by school
				$arrUsersBySchool = [];
				$arrIsActive = [];

				foreach ($users as $user) {
					if ($user->getSchool() !== ALL_SCHOOL) {
						if ($user->getIsActive()) {
							$arrUsersBySchool[$user->getSchool()]['active'][] = $user;
							
						} else {
							$arrUsersBySchool[$user->getSchool()]['inactive'][] = $user;
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
					['users' => $arrUsersBySchool, 'schools' => $schools, 'isActive' => $arrIsActive, 'option' => ['moderatUsers', 'buttonToggleSchool']]);
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
					['users' => $users, 'schools' => $schools, 'isActive' => $arrIsActive, 'option' => ['moderatUsers', 'buttonToggleSchool']]);
			}
		} else {
			throw new \Exception("Cette page est réservé aux administrateurs / modérateurs");
		}
	}

	public function editGrade()
	{
		if (isset($_GET['userName'], $_GET['schoolName'], $_GET['toAdmin'], $_GET['toModerator'])) {
			if ($_GET['toAdmin'] === 'true') {
				$toAdmin = true;
			} else {
				$toAdmin = false;
			}

			if ($_GET['toModerator'] === 'true') {
				$toModerator = true;
			} else {
				$toModerator = false;
			}

			if (!($toAdmin && $toModerator)) {
				$SchoolManager = new SchoolManager();

				if ($SchoolManager->nameExists($_GET['schoolName'])) {
					$UserManager = new UserManager();

					if ($UserManager->nameExists($_GET['userName'])) {
						$school = $SchoolManager->getSchoolByName($_GET['schoolName']);
						$user = $UserManager->getUserByName($_GET['userName']);

						if ($toModerator) {
							if (!$user->getIsAdmin()) {
								//normal user to moderator, nb active account -1
								$nbActiveAccount = $school->getNbActiveAccount() - 1;
								$SchoolManager->updateByName($school->getName(), 'nbActiveAccount', $nbActiveAccount);
							}
						} elseif (!$toModerator && !$toAdmin) {
							//moderator to normal user, nb active account +1
							$nbActiveAccount = $school->getNbActiveAccount();
							if ($nbActiveAccount < $school->getNbEleve()) {
								$SchoolManager->updateByName($school->getName(), 'nbActiveAccount', $nbActiveAccount + 1);
							} else {
								throw new \Exception("Il est impossible d'effectuer cette action, vous avez atteint le nombre maximum de compte utilisateur actif");
							}
						}

						$UserManager->updateByName($user->getName(), 'grade', ['isAdmin' => $toAdmin, 'isModerator' => $toModerator]);

						//add history entry
						if ($toAdmin) {
							$grade = "administrateur";
						} elseif ($toModerator) {
							$grade = "modérateur";
						} else {
							$grade = "utilisateur";
						}

						$HistoryManager = new HistoryManager();
						$HistoryManager->addEntry(new HistoryEntry([
							'idSchool' => $school->getId(),
							'category' => 'account',
							'entry' => $_SESSION['pseudo'] . ' a passé ' . $user->getName() . ' au grade : ' . $grade]));
					} else {
						throw new \Exception("Les informations renseignées sont incorrectes");
					}
				} else {
					throw new \Exception("Les informations renseignées sont incorrectes");
				}
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

	public function toggleUserIsActive()
	{
		if (!empty($_GET['userName']) && !empty($_GET['schoolName'])) {
			if ($_SESSION['school'] === ALL_SCHOOL || $_SESSION['school'] === $_GET['schoolName']) {
				$SchoolManager = new SchoolManager();
				$UserManager = new UserManager();

				$schoolExist = $SchoolManager->nameExists($_GET['schoolName']);
				$userExist = $UserManager->nameExists($_GET['userName']);

				if ($schoolExist && $userExist) {
					$user = $UserManager->getUserByName($_GET['userName']);
					$school = $SchoolManager->getSchoolByName($_GET['schoolName']);
					$nbActiveAccount = $school->getNbActiveAccount();
					$nbEleve = $school->getNbEleve();

					if ($user->getIsActive()) {
						//account is active
						$UserManager->updateByName($_GET['userName'], 'isActive', false);
						//nb active account - 1
						$SchoolManager->updateByName($_GET['schoolName'], 'nbActiveAccount', $nbActiveAccount - 1);
						//add history entry
						$HistoryManager = new HistoryManager();
						$HistoryManager->addEntry(new HistoryEntry([
							'idSchool' => $school->getId(),
							'category' => 'account',
							'entry' => $_SESSION['pseudo'] . ' a désactivé le compte de ' . $user->getName()]));
					} else {
						//account is inactive
						if ($nbActiveAccount < $nbEleve) {
							$UserManager->updateByName($_GET['userName'], 'isActive', true);
							//nb active account + 1
							$SchoolManager->updateByName($_GET['schoolName'], 'nbActiveAccount', $nbActiveAccount + 1);
							//add history entry
							$HistoryManager = new HistoryManager();
							$HistoryManager->addEntry(new HistoryEntry([
								'idSchool' => $school->getId(),
								'category' => 'account',
								'entry' => $_SESSION['pseudo'] . ' a activé le compte de ' . $user->getName()]));
						} else {
							throw new \Exception("Il est impossible d'effectuer cette action, 
								vous avez atteint le nombre maximum de compte utilisateur actif");
						}
					}

					if (isset($_SERVER['HTTP_REFERER'])) {
						header('Location: ' . $_SERVER['HTTP_REFERER']);
					} else {
						header('Location: indexAdmin.php');
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
		if (!empty($_GET['elem'])) {
			switch ($_GET['elem']) {
				case 'school' :
				break;
				case 'user' :
					if (!empty($_GET['userName']) && !empty($_GET['schoolName'])) {
						if ($_SESSION['school'] === ALL_SCHOOL || $_SESSION['school'] === $_GET['schoolName']) {
							$SchoolManager = new SchoolManager();
							$UserManager = new UserManager();

							$schoolExist = $SchoolManager->nameExists($_GET['schoolName']);
							$userExist = $UserManager->nameExists($_GET['userName']);

							if ($schoolExist && $userExist) {
								$user = $UserManager->getUserByName($_GET['userName']);
								$school = $SchoolManager->getSchoolByName($_GET['schoolName']);
								if (!$user->getIsAdmin()) {
									//delete reports related to this account

									//delete content publish by this account and reports related to this content

									//if account is active and not moderator, nb active account -1
									if ($user->getIsActive() && !$user->getIsModerator()) {
										$SchoolManager->updateByName($_GET['schoolName'], 'nbActiveAccount', $school->getNbActiveAccount() - 1);
									}

									//delete account
									$UserManager->delete($user->getId());

									//add history entry
									$HistoryManager = new HistoryManager();
									$HistoryManager->addEntry(new HistoryEntry([
										'idSchool' => $school->getId(),
										'category' => 'account',
										'entry' => $_SESSION['pseudo'] . ' a supprimé le compte de ' . $user->getName()]));

									if (isset($_SERVER['HTTP_REFERER'])) {
										header('Location: ' . $_SERVER['HTTP_REFERER']);
									} else {
										header('Location: indexAdmin.php');
									}
								} else {
									throw new \Exception("Vous ne pouvez pas supprimer un compte administrateur");
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
				if (isset($_GET['offset'])) {
					$offset = intval($_GET['offset']);
				} else {
					$offset = 0;
				}
					
				if (!empty($_GET['sortBy']) && !empty($_GET['sortValue'])) {
					if (!empty($_GET['thirdSortValue'])) {
						//sort by category and date
						$entries = $HistoryManager->getBySchool($_GET['school'], $offset, $_GET['sortBy'], 
							$_GET['sortValue'], [$_GET['secondSortValue'], $_GET['thirdSortValue']]);
					} elseif (!empty($_GET['secondSortValue'])) {
						//sort by date
						$entries = $HistoryManager->getBySchool($_GET['school'], $offset, $_GET['sortBy'], 
							[$_GET['sortValue'], $_GET['secondSortValue']]);
					} else {
						//sort by category
						$entries = $HistoryManager->getBySchool($_GET['school'], $offset, $_GET['sortBy'], 
							$_GET['sortValue']);
					}
				} else {
					//no sorting
					$entries = $HistoryManager->getBySchool($_GET['school'], $offset);
				}

				$arrEntries = [];
				for ($i=0; $i<count($entries); $i++) {
					$arrEntries[$i][] = $entries[$i]->getDateEntry();
					$arrEntries[$i][] = $entries[$i]->getEntry();
				}
				
				echo json_encode($arrEntries);
			}
		}
	}

	public function error(string $error_msg)
	{
		RenderView::render('template.php', 'backend/errorView.php', ['error_msg' => $error_msg]);
	}
}
