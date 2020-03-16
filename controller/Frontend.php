<?php

namespace Chemin\ArtSchool\Model;

class Frontend
{
	public static $color = 'white';

	public function __construct()
	{
		if (isset($_SESSION['grade'])) {
			switch ($_SESSION['grade']) {
				case ADMIN :
					static::$color = '#CF8B3F';
				break;
				case MODERATOR :
					static::$color = '#FFC652';
				break;
				case STUDENT :
					static::$color = '#3498BF';
				break;
				case USER :
					static::$color = '#3498BF';
				break;
				default :
					static::$color = '#555';
			}
		}
	}

	public function verifyInformation()
	{
		if (isset($_SESSION['pseudo'])) {
			//user is connect, verif SESSION info
			$SchoolManager = new SchoolManager();
			$UserManager = new UserManager();
			if((!$SchoolManager->nameExists($_SESSION['school']) && !($_SESSION['school'] === ALL_SCHOOL))
			|| !$UserManager->nameExists($_SESSION['pseudo'])) {
				//if user name don't exist or if school name don't exist and isn't "allSchool" 
				session_destroy();
				if (isset($_COOKIE['artSchoolId']) || isset($_COOKIE['artSchoolAdminId'])) {
					$this->useCookieToSignIn();
				} else {
					throw new \Exception("Certaines informations lié a votre compte ne sont plus valide,
					 veuillez vous reconnecter pour mettre à jour ces informations.
					 Cocher la case 'rester connecté' lors de la connection peu vous éviter ce genre de désagrément");
				}
			}
		} elseif (isset($_COOKIE['artSchoolId']) || isset($_COOKIE['artSchoolAdminId'])) {
			//user is not connect, looking for cookie
			$this->useCookieToSignIn();
		}
	}

	public function useCookieToSignIn()
	{
		if (isset($_COOKIE['artSchoolAdminId'])) {
			$cookie = explode("-", $_COOKIE['artSchoolAdminId']);
		} elseif (isset($_COOKIE['artSchoolId'])) {
			$cookie = explode("-", $_COOKIE['artSchoolId']);
		}

		if (count($cookie) === 2) {
			$UserManager = new UserManager();

			$userId = $cookie[0];
			$userPassword = $cookie[1];

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
		} elseif ($_SESSION['school'] !== NO_SCHOOL) {
			$_SESSION['grade'] = STUDENT;
		} else {
			$_SESSION['grade'] = USER;
		}
	}

	public function disconnect()
	{
		if (isset($_SESSION)) {
			session_destroy();
		}

		$this->cookieDestroy();

		header('Location: index.php');
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
		$PostsManager = new PostsManager();

		RenderView::render('template.php', 'frontend/indexView.php', ['slide' => true]);
	}

	public function signUp()
	{
		if (empty($_SESSION)) {
			if (isset($_POST['pseudo'])){
				if ($_POST['confirmPassword'] === $_POST['password']) {
					$UserManager = new UserManager();
					if (!$UserManager->nameExists($_POST['pseudo'])) {
						if (!$UserManager->mailExists($_POST['mail'])) {
							if (!empty($_POST['affiliationCode'])) {
								//with affiliation code
								$SchoolManager = new SchoolManager();
								$result = $SchoolManager->affiliationCodeExists($_POST['affiliationCode']);
								$codeExist = $result['exist'];

								if ($codeExist) {
									$schoolName = $result['name'];
									//check nb active account
									$school = $SchoolManager->getSchoolByName($schoolName);

									if ($school->getNbActiveAccount() < $school->getNbEleve()) {
										$SchoolManager->updateByName($schoolName, 'nbActiveAccount', $school->getNbActiveAccount() + 1);

										$UserManager->add(new User([
											'name' => $_POST['pseudo'], 
											'mail' => $_POST['mail'], 
											'password' => password_hash($_POST['password'], PASSWORD_DEFAULT), 
											'school' => $schoolName,
											'isAdmin' => false, 
											'isModerator' => false]));

										//add history entry
										$HistoryManager = new HistoryManager();
										$HistoryManager->addEntry(new HistoryEntry([
											'idSchool' => $school->getId(),
											'category' => 'account',
											'entry' => $_POST['pseudo'] . ' a créé un compte affilié à votre établissement']));

										$message = "Le compte à bien été créé, vous pouvez maintenant <a href='index.php?action=signIn'>vous connecter</a>";
									} else {
										$message = "Il est impossible de créer un compte pour le moment, le nombre maximum de compte utilisateur pour cet établissement a été atteint. Veuillez contacter un responsable de cet établissement pour plus d'informations";
									}
								} else {
									$message = "Le code d'affiliation est incorrect";
								}
							} else {
								//without affiliation code
								$UserManager->add(new User([
									'name' => $_POST['pseudo'], 
									'mail' => $_POST['mail'], 
									'password' => password_hash($_POST['password'], PASSWORD_DEFAULT), 
									'school' => NO_SCHOOL,
									'isAdmin' => false, 
									'isModerator' => false]));

								$message = "Le compte à bien été créé, vous pouvez maintenant <a href='index.php?action=signIn'>vous connecter</a>";
							}
						} else {
							$message = "Cette adresse mail est déja lié a un compte";
						}
					} else {
						$message = "Ce nom d'utilisateur est déja utilisé";
					}
				} else {
					$message = "Vous devez entrer deux mot de passe identiques";
				}
			}
			
			if (isset($message)) {
				RenderView::render('template.php', 'frontend/signUpView.php', ['option' => ['signUp'], 'message' => $message]);
			} else {
				RenderView::render('template.php', 'frontend/signUpView.php', ['option' => ['signUp']]);
			}
		} else {
			header('Location: index.php');
		}
	}

	public function signIn()
	{
		if (empty($_SESSION)) {
			//if user is not connected and try to connect
			if (isset($_POST['ConnectPseudo']) && isset($_POST['ConnectPassword'])) {
				$UserManager = new UserManager();
				$userExist = $UserManager->nameExists($_POST['ConnectPseudo']);

				if ($userExist) {
					$user = $UserManager->getUserByName($_POST['ConnectPseudo']);
					$passwordIsOk = $UserManager->checkPassword($user, $_POST['ConnectPassword']);

					if ($passwordIsOk) {
						if (isset($_POST['stayConnect'])) {
							//if user want to stay connect
							setcookie('artSchoolId', $user->getId() . '-' . $user->getPassword(), time()+(365*24*3600), null, null, false, true);

							if ($user->getIsAdmin() || $user->getIsModerator()) {
								setcookie('artSchoolAdminId', $user->getId() . '-' . $user->getPassword(), time()+(365*24*3600), null, null, false, true);
							}
						}
						$this->connect($user);
						
						header('Location: index.php');
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
					$content = "Bonjour " . $user->getName() . ", vous avez demande a reinitialiser votre mot de passe.<br><br>
						En suivant <a style='text-decoration: underline;' href='http://julienchemin.fr/projet5/index.php?action=resetPassword&key=" . $temporaryPassword . "&id=" . $user->getId() . "'>ce lien</a> vous serez redirige vers une page pour modifier votre mot de passe.<br><br>
						Si le lien ne fonctionne pas, rendez vous a l'adresse suivante : <br>http://julienchemin.fr/projet5/index.php?action=resetPassword&key=" . $temporaryPassword . "&id=" . $user->getId() . "<br><br>L'equipe d'ArtSchool vous remercie.";
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
				RenderView::render('template.php', 'frontend/signInView.php', ['option' => ['forgetPassword', 'signIn'], 'message' => $message]);
			} else {
				RenderView::render('template.php', 'frontend/signInView.php', ['option' => ['forgetPassword', 'signIn']]);
			}
		} else {
			header('Location: index.php');
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
							RenderView::render('template.php', 'frontend/resetPasswordView.php', ['user' => $user, 'message' => $message]);
						} else {
							RenderView::render('template.php', 'frontend/resetPasswordView.php', ['user' => $user]);
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
								$UserManager->setPassword(password_hash($_POST['newPassword'], PASSWORD_DEFAULT), $user->getId());

								$message = "Le mot de passe a bien été modifié.";
								
								RenderView::render('template.php', 'frontend/resetPasswordView.php', ['message' => $message]);
							} else {
								//new password is the same as the old one
								header('Location: index.php?action=resetPassword&key=' . $_POST['key'] . '&id=' . $_POST['id'] . '&wrongPassword=2');
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
				header('Location: index.php?action=resetPassword&key=' . $_POST['key'] . '&id=' . $_POST['id'] . '&wrongPassword=1');
			}
		} else {
			throw new \Exception("Ce lien pour réinitialiser votre mot de passe n'est pas valide");
		}	
	}

	public function error(string $error_msg)
	{
		RenderView::render('template.php', 'frontend/errorView.php', ['error_msg' => $error_msg]);
	}	
}
