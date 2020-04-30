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
					$UserManager->updateById($user->getId(), 'temporaryPassword', $temporaryPassword);

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
								$UserManager->updateById($user->getId(), 'password', password_hash($_POST['newPassword'], PASSWORD_DEFAULT));

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

	public function userProfile()
	{
		if (!empty($_GET['userId'])) {
			$UserManager = new UserManager();

			if ($UserManager->exists($_GET['userId'])) {
				$ProfileContentManager = new ProfileContentManager();
				$user = $UserManager->getOneById($_GET['userId']);
				$profileContent = $ProfileContentManager->getByProfileId($user->getId());

				RenderView::render('template.php', 'frontend/userProfileView.php', ['user' => $user, 'profileContent' => $profileContent, 'option' => ['userProfile', 'tinyMCE']]);
			} else {
				throw new \Exception("L'utilisateur recherché n'existe pas");
			}
		} else {
			throw new \Exception("Les informations renseignées sont incorrectes");
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
				throw new \Exception("L'établissement recherché n'existe pas");
			}
		} else {
			throw new \Exception("Les informations renseignées sont incorrectes");
		}
	}

	public function updateProfile()
	{
		if (!empty($_GET['userId']) && !empty($_GET['elem'])) {
			$UserManager = new UserManager();

			if ($UserManager->exists($_GET['userId']) && $_GET['userId'] === $_SESSION['id']) {
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

						if (!empty($_POST['deleteBlock'])) {
							//delete content
							$ProfileContentManager->deleteByProfileId($_SESSION['id'], $_POST['type'], $_POST['deleteBlock']);
							$order = intval($_POST['deleteBlock']);
							$contentToUpdate = $ProfileContentManager->getContentForDelete($_SESSION['id'], $_POST['type'], $_POST['deleteBlock']);

							foreach ($contentToUpdate as $content) {
								var_dump($content);
								$newOrderContent = intval($content->getContentOrder())-1;
								$ProfileContentManager->updateElem($content, 'contentOrder', $newOrderContent);
							}
						} else {
							if (stripos($_POST['tinyMCEtextarea'], '&lt;script') === false && stripos($_POST['tinyMCEtextarea'], '&lt;iframe') === false) {
								//add new content
								if ($_POST['blockOrderValue'] === 'new') {
									if ($_POST['newOrderValue'] === 'last') {
										//new content go to last place
										$order = $ProfileContentManager->getCount($_SESSION['id'], $_POST['type']) + 1;
									} else {
										//new content go to "newOrderValue" place
										$order = intval($_POST['newOrderValue']);
										$contentToUpdate = $ProfileContentManager->getContentForAdd($_SESSION['id'], $_POST['type'], $order);
										foreach ($contentToUpdate as $content) {
											$newOrderContent = intval($content->getContentOrder())+1;
											$ProfileContentManager->updateElem($content, 'contentOrder', $newOrderContent);
										}
									}

									$ProfileContentManager->add(new ProfileContent([
										'userId' => $_SESSION['id'],
										'tab' => $_POST['type'],
										'size' => $_POST['sizeValue'],
										'contentOrder' => $order,
										'align' => $_POST['alignValue'],
										'content' => $_POST['tinyMCEtextarea']]));
								} else {
									//edit content
									if ($_POST['blockOrderValue'] === $_POST['newOrderValue']) {
										//content keep his place number
										$order = intval($_POST['newOrderValue']);
										$ProfileContentManager->update($_POST['blockOrderValue'], new ProfileContent([
											'userId' => $_SESSION['id'],
											'tab' => $_POST['type'],
											'size' => $_POST['sizeValue'],
											'contentOrder' => $order,
											'align' => $_POST['alignValue'],
											'content' => $_POST['tinyMCEtextarea']]));
									} else {
										//content change place number
										$ProfileContentManager->deleteByProfileId($_SESSION['id'], $_POST['type'], $_POST['blockOrderValue']);
										$order = intval($_POST['newOrderValue']);
										$contentToUpdate = $ProfileContentManager->getContentForUpdate($_SESSION['id'], $_POST['type'], $_POST['blockOrderValue'], $_POST['newOrderValue']);
										if ($_POST['newOrderValue'] < $_POST['blockOrderValue']) {
											foreach ($contentToUpdate as $content) {
												$newOrderContent = intval($content->getContentOrder())+1;
												$ProfileContentManager->updateElem($content, 'contentOrder', $newOrderContent);
											}
										} else {
											foreach ($contentToUpdate as $content) {
												$newOrderContent = intval($content->getContentOrder())-1;
												$ProfileContentManager->updateElem($content, 'contentOrder', $newOrderContent);
											}
										}

										$ProfileContentManager->add(new ProfileContent([
											'userId' => $_SESSION['id'],
											'tab' => $_POST['type'],
											'size' => $_POST['sizeValue'],
											'contentOrder' => $order,
											'align' => $_POST['alignValue'],
											'content' => $_POST['tinyMCEtextarea']]));
									}
								}
							}
						}
						if (isset($_SERVER['HTTP_REFERER'])) {
							header('Location: ' . $_SERVER['HTTP_REFERER']);
						} else {
							header('Location: index.php');
						}
					break;
				}
			}
		}
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
						if (isset($_GET['noBanner'])) {
							$infos = $final_path . ' ' . $_GET['noBanner'];
							$UserManager->updateById($_SESSION['id'], 'profileBannerInfo', $infos);
						}
					break;
					case 'picture' :
						if (isset($_GET['orientation'], $_GET['size'])) {
							$infos = $final_path . ' ' . $_GET['orientation'] . ' ' . $_GET['size'];
							$UserManager->updateById($_SESSION['id'], 'profilePictureInfo', $infos);
						}
					break;
				}
			}

			if (isset($_SERVER['HTTP_REFERER'])) {
				header('Location: ' . $_SERVER['HTTP_REFERER']);
			} else {
				header('Location: index.php');
			}
		}
	}

	public function addPost()
	{
		RenderView::render('template.php', 'frontend/addPostView.php', ['option' => ['addPost', 'tinyMCE']]);
	}

	public function uploadPost()
	{
		if (!empty($_POST['fileTypeValue'])) {
			$PostsManager = new PostsManager();
			//check for script / iframe insertion
			$titleOk = $this->checkForScriptInsertion($_POST['title']);
			$descriptionOk = $this->checkForScriptInsertion($_POST['tinyMCEtextarea']);
			$urlOk = $this->checkForScriptInsertion($_POST['videoLink']);
			$tagOk = "";
			//check tags
			$listTags = explode(',', $_POST['listTags']);
			array_shift($listTags);
			foreach ($listTags as $tag) {
				if (!$this->tagIsValide($tag)) {
					$tagOk = false;
				}
			}
			if ($tagOk !== false) {
				$tagOk = true;
			}
			
			if ($titleOk && $descriptionOk && $urlOk && $tagOk) {
				//set $folder
				if (!empty($_GET['folder']) && $PostsManager->folderBelongsToUser($_GET['folder'], $_SESSION['id'])) {
					$folder = $_GET['folder'];
				} else {
					$folder = null;
				}

				switch ($_POST['fileTypeValue']) {
					case 'image':
						if (!empty($_FILES) && !empty($_POST['listTags'])) {
							$arrAcceptedExtention = array("jpeg", "jpg", "png", "gif");
							require('view/upload.php');
							if (!empty($final_path)) {
								$PostsManager->set(new Post([
									'idAuthor' => $_SESSION['id'], 
									'title' => $_POST['title'], 
									'filePath' => $final_path, 
									'description' => $_POST['tinyMCEtextarea'], 
									'isPrivate' => false, 
									'postType' => 'userPost', 
									'fileType' => $_POST['fileTypeValue'], 
									'onFolder' => $folder, 
									'tags' => $_POST['listTags']]));
								$this->checkForNewTag($_POST['listTags']);
							}
						}
					break;
					case 'video':
						if (!empty($_POST['videoLink']) && !empty($_POST['listTags'])) {
							$arrAcceptedExtention = array("jpeg", "jpg", "png", "gif");
							require('view/upload.php');
							!empty($final_path) ? $filePath = $final_path : $filePath = null;
							
							$PostsManager->set(new Post([
								'idAuthor' => $_SESSION['id'], 
								'title' => $_POST['title'], 
								'filePath' => $filePath, 
								'urlVideo' => $_POST['videoLink'], 
								'description' => $_POST['tinyMCEtextarea'], 
								'isPrivate' => false, 
								'postType' => 'userPost', 
								'fileType' => $_POST['fileTypeValue'], 
								'onFolder' => $folder, 
								'tags' => $_POST['listTags']]));
							$this->checkForNewTag($_POST['listTags']);
						}
					break;
					case 'compressed':
						if (!empty($_FILES) && !empty($_POST['title'])) {
							$arrAcceptedExtention = array("zip", "rar");
							require('view/upload.php');
							if (!empty($final_path)) {
								$PostsManager->set(new Post([
									'idAuthor' => $_SESSION['id'], 
									'title' => $_POST['title'], 
									'filePath' => $final_path, 
									'description' => $_POST['tinyMCEtextarea'], 
									'isPrivate' => false, 
									'postType' => 'userPost', 
									'fileType' => $_POST['fileTypeValue'], 
									'onFolder' => $folder]));
							}
						}
					break;
					case 'folder':
						if (!empty($_POST['title'])) {
							$arrAcceptedExtention = array("jpeg", "jpg", "png", "gif");
							require('view/upload.php');
							!empty($final_path) ? $filePath = $final_path : $filePath = null;

							$PostsManager->set(new Post([
								'idAuthor' => $_SESSION['id'], 
								'title' => $_POST['title'], 
								'filePath' => $filePath,  
								'description' => $_POST['tinyMCEtextarea'], 
								'isPrivate' => false, 
								'postType' => 'userPost', 
								'fileType' => $_POST['fileTypeValue'], 
								'onFolder' => $folder]));
						}
					break;
				}
			}
		}
		header('Location: index.php?action=userProfile&userId=' . $_SESSION['id']);
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

	private function tagIsValide(string $tag)
	{
		$regex = '/^[a-z0-9]+[a-z0-9 ]*[a-z0-9]+$/i';
		if (preg_match($regex, $tag)) {
			return true;
		} else {
			return false;
		}
	}

	private function checkForNewTag(string $listTags)
	{
		if (!empty($listTags)) {
			$TagsManager = new TagsManager();
			$arrTags = explode(',', $listTags);

			for ($i=1; $i<count($arrTags); $i++) {
				if (!$TagsManager->exists($arrTags[$i])) {
					$TagsManager->set($arrTags[$i]);
				}
			}
		}
	}

	private function checkForScriptInsertion(string $str)
	{
		if (!empty($str)) {
			if (stripos($str, '&lt;script') === false && stripos($str, '&lt;iframe') === false) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	public function error(string $error_msg)
	{
		RenderView::render('template.php', 'frontend/errorView.php', ['error_msg' => $error_msg]);
	}	
}
