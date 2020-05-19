<?php
namespace Chemin\ArtSchool\Model;

abstract class Controller
{
	public function useCookieToSignIn()
	{
		switch (static::$SIDE) {
			case 'frontend' :
				if (!empty($_COOKIE['artSchoolAdminId'])) {
					$cookie = explode("-", $_COOKIE['artSchoolAdminId']);
				} else {
					$cookie = explode("-", $_COOKIE['artSchoolId']);
				}
			break;
			case 'backend' :
				$cookie = explode("-", $_COOKIE['artSchoolAdminId']);
			break;
		}
		if (count($cookie) === 2) {
			$UserManager = new UserManager();
			$userId = htmlspecialchars($cookie[0]);
			$userPassword = htmlspecialchars($cookie[1]);
			if ($UserManager->exists($userId)) {
				$user = $UserManager->getOneById($userId);
				if ($user->getPassword() === $userPassword) {
					$SchoolManager = new SchoolManager();
					if(!$SchoolManager->nameExists($user->getSchool()) && $user->getSchool() !== ALL_SCHOOL) {
						//if school name don't exist and isn't "allSchool"
						$this->cookieDestroy();
						throw new \Exception("Le nom de l'établissement scolaire auquel vous êtes affilié n'existe pas / plus.
							Un message d'erreur a été envoyé à un administrateur du site et sera traité dans les plus brefs délais.
							Merci de votre compréhension");
					} else {$this->connect($user);}
				} else {$this->disconnect();}
			} else {$this->disconnect();}
		} else {$this->disconnect();}
	}

	public function connect(User $user)
	{
		$_SESSION['id'] = $user->getId();
		$_SESSION['pseudo'] = $user->getName();
		$_SESSION['school'] = $user->getSchool();
		$_SESSION['group'] = $user->getSchoolGroup();

		if ($user->getIsAdmin()) {
			$_SESSION['grade'] = ADMIN;
		} elseif ($user->getIsModerator()) {
			$_SESSION['grade'] = MODERATOR;
		} elseif (static::$SIDE === 'frontend' && $_SESSION['school'] !== NO_SCHOOL) {
			$_SESSION['grade'] = STUDENT;
		} elseif (static::$SIDE === 'frontend') {
			$_SESSION['grade'] = USER;
		}
	}

	public function disconnect()
	{
		if (isset($_SESSION)) {
			session_destroy();
		}
		$this->cookieDestroy();
		header('Location: ' . static::$INDEX);
	}

	public function forceDisconnect()
	{
		session_destroy();
		if (isset($_COOKIE['artSchoolId']) || isset($_COOKIE['artSchoolAdminId'])) {
			$this->useCookieToSignIn();
		} else {
			throw new \Exception("Certaines informations lié a votre compte ne sont plus valide,
			 veuillez vous reconnecter pour mettre à jour ces informations.
			 Cocher la case 'rester connecté' lors de la connection peu vous éviter ce genre de désagrément");
		}
	}

	public function setCookie(User $user)
	{
		setcookie('artSchoolId', $user->getId() . '-' . $user->getPassword(), time()+(365*24*3600), null, null, false, true);
		if ($user->getIsAdmin() || $user->getIsModerator()) {
			setcookie('artSchoolAdminId', $user->getId() . '-' . $user->getPassword(), time()+(365*24*3600), null, null, false, true);
		}
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

	public function redirection(string $url = null)
	{
		if (isset($_SERVER['HTTP_REFERER'])) {
			header('Location: ' . $_SERVER['HTTP_REFERER']);
		} elseif (!empty($url)) {
			header('Location: ' . $url);
		} else {
			header('Location: ' . static::$INDEX);
		}
	}

	public function accessDenied()
	{
		throw new \Exception("Vous n'avez pas accès à cette page");
	}

	public function invalidLink()
	{
		throw new \Exception("Ce lien a expiré ou la page n'existe pas");
	}

	public function incorrectInformation()
	{
		throw new \Exception("Les informations renseignées sont incorrectes");
	}

	public function error(string $error_msg)
	{
		RenderView::render('template.php', static::$SIDE . '/errorView.php', ['error_msg' => $error_msg]);
	}
}