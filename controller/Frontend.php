<?php

namespace Chemin\ArtSchool\Model;

class Frontend
{
	public static $color = 'white';

	public function __construct()
	{
		if (isset($_SESSION['pseudo'])) {
			$SchoolManager = new SchoolManager();
			$UserManager = new UserManager();
			if(!($SchoolManager->nameExists($_SESSION['school'])) || !($UserManager->exists($_SESSION['id']))) {
				session_destroy();
				if (isset($_COOKIE['artSchoolId']) || isset($_COOKIE['artSchoolAdminId'])) {
					$this->useCookieToSignIn();
				} else {
					throw new \Exception('Veuillez vous reconnecter pour mettre à jour vos informations. Cocher la case "rester connecté" lors de la connection peu vous éviter ce genre de désagrément');
				}
			}
		} elseif (isset($_COOKIE['artSchoolId']) || isset($_COOKIE['artSchoolAdminId'])) {
			$this->useCookieToSignIn();
		}

		if (isset($_SESSION['grade'])) {
			if ($_SESSION['grade'] === 'admin') {
				static::$color = '#CF8B3F';
			} elseif ($_SESSION['grade'] === 'moderator') {
				static::$color = '#FFC652';
			} elseif ($_SESSION['grade'] === 'student') {
				static::$color = '#3498BF';
			}
		}
	}

	public function useCookieToSignIn()
	{
		if (isset($_COOKIE['artSchoolAdminId'])) {
			$idUser = $_COOKIE['artSchoolAdminId'];
		} elseif (isset($_COOKIE['artSchoolId'])) {
			$idUser = $_COOKIE['artSchoolId'];
		}

		$UserManager = new UserManager();
		if ($UserManager->exists($idUser)) {
			$user = $UserManager->getOneById($idUser);

			$_SESSION['id'] = $user->getId();
			$_SESSION['pseudo'] = $user->getName();
			$_SESSION['school'] = $user->getSchool();
			if ($user->getIsAdmin()) {
				$_SESSION['grade'] = 'admin';
			} elseif ($user->getIsModerator()) {
				$_SESSION['grade'] = 'moderator';
			} else {
				$_SESSION['grade'] = 'student';
			}
		} else {
			if (isset($_SESSION)) {
				session_destroy();
			}
			if (isset($_COOKIE['artSchoolId'])) {
				setcookie('artSchoolId', '', time()-3600, null, null, false, true);
			} elseif (isset($_COOKIE['artSchoolAdminId'])) {
				setcookie('artSchoolAdminId', '', time()-3600, null, null, false, true);
			}
			header('Location: index.php');
		}
	}

	public function home()
	{
		$PostsManager = new PostsManager();

		RenderView::render('template.php', 'frontend/indexView.php', ['slide' => true]);
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

		header('Location: index.php');
	}

	public function signUp()
	{
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
								$nbActiveAccount = $school->getNbActiveAccount() + 1;
								if ($nbActiveAccount <= $school->getNbEleve()) {
									$SchoolManager->updateByName($schoolName, 'nbActiveAccount', $nbActiveAccount);

									$UserManager->add(new User([
									'name' => $_POST['pseudo'], 
									'mail' => $_POST['mail'], 
									'password' => password_hash($_POST['password'], PASSWORD_DEFAULT), 
									'school' => $schoolName,
									'isAdmin' => false, 
									'isModerator' => false]));

									$message = "Le compte à bien été créé, vous pouvez maintenant <a href='index.php?action=signIn'>vous connecter</a>";
								} else {
									$message = "Il est impossible de créer un compte pour le moment, le nombre maximum de compte utilisateur pour cet établissement a été atteint. Veuillez contacter un responsable de cet établissement pour plus d'informations";
								}
							} else {
								$message = "Le code d'affiliation est incorrect";
							}
						} else {
							//without affiliation code
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
			RenderView::render('template.php', 'frontend/signUpView.php', ['message' => $message]);
		} else {
			RenderView::render('template.php', 'frontend/signUpView.php');
		}
	}

	public function signIn()
	{
		//if user is not connected and try to connect
		if (isset($_POST['ConnectPseudo']) && isset($_POST['ConnectPassword'])) {
			$UserManager = new UserManager();
			$userExist = $UserManager->nameExists($_POST['ConnectPseudo']);

			if ($userExist) {
				$user = $UserManager->getUserByName($_POST['ConnectPseudo']);
				$passwordIsOk = $UserManager->checkPassword($user, $_POST['ConnectPassword']);

				if ($passwordIsOk) {
					$isAdmin = $user->getIsAdmin();
					$isModerator = $user->getIsModerator();

					if (isset($_POST['stayConnect'])) {
							//if user want to stay connect
							setcookie('artSchoolId', $user->getId(), time()+(365*24*3600), null, null, false, true);
						}
						$_SESSION['id'] = $user->getId();
						$_SESSION['pseudo'] = $user->getName();
						$_SESSION['school'] = $user->getSchool();

					if ($isAdmin) {
						$_SESSION['grade'] = 'admin';
						header('Location: index.php');
					} elseif ($isModerator){
						$_SESSION['grade'] = 'moderator';
						header('Location: index.php');
					} else {
						$_SESSION['grade'] = 'student';
						header('Location: index.php');
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
					http://julienchemin.fr/projet5/index.php?action=resetPassword&key=' . $temporaryPassword . '&id=' . $user->getId();
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
			RenderView::render('template.php', 'frontend/signInView.php', ['option' => ['forgetPassword'], 'message' => $message]);
		} else {
			RenderView::render('template.php', 'frontend/signInView.php', ['option' => ['forgetPassword']]);
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
				RenderView::render('template.php', 'frontend/resetPasswordView.php', ['user' => $user, 'message' => $message]);
			} else {
				RenderView::render('template.php', 'frontend/resetPasswordView.php', ['user' => $user]);
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
				header('Location: index.php?action=resetPassword&key=' . $_POST['key'] . '&id=' . $_POST['id'] . '&wrongPassword=true');
			}
			RenderView::render('template.php', 'frontend/resetPasswordView.php', ['message' => $message]);
		} else {
			throw new \Exception("Pour réinitialiser votre mot de passe, vous devez passer directement par le lien qui vous a été envoyé par mail");
		}
		
	}

	public function error(string $error_msg)
	{
		RenderView::render('template.php', 'frontend/errorView.php', ['error_msg' => $error_msg]);
	}	
}
