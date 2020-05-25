<?php

session_start();

use \Chemin\ArtSchool\Model\Backend;

spl_autoload_register(function($class)
{
	$arr = str_split($class, 23);
	$nameClass = $arr[1];
	
	if ($nameClass === 'Frontend' || $nameClass === 'Backend' || $nameClass === 'Controller') {
		require 'controller/' . $nameClass . '.php';
	} else {
		require 'model/' . $nameClass . '.php';
	}
});

const BACKEND = true;
const ALL_SCHOOL = 'allSchool';
const NO_SCHOOL = 'noSchool';
const ADMIN = 'admin';
const MODERATOR = 'moderator';
const STUDENT = 'student';
const USER = 'user';

/*---------------------------------*/
$Backend = new Backend();
try {
	if (isset($_GET['action'])) {
		switch ($_GET['action']) {
			case 'resetPassword' :
				$Backend->resetPassword();
			break;
			case 'disconnect' :
				$Backend->disconnect();
			break;
			case 'addSchool' :
				$Backend->addSchool();
			break;
			case 'moderatSchool' :
				$Backend->moderatSchool();
			break;
			case 'editSchool' :
				$Backend->editSchool();
			break;
			case 'moderatAdmin' :
				$Backend->moderatAdmin();
			break;
			case 'moderatUsers' :
				$Backend->moderatUsers();
			break;
			case 'createGroup' :
				$Backend->createGroup();
			break;
			case 'getGroup' :
				$Backend->getGroup();
			break;
			case 'setGroup' :
				$Backend->setGroup();
			break;
			case 'deleteGroup' :
				$Backend->deleteGroup();
			break;
			case 'editGrade' :
				$Backend->editGrade();
			break;
			case 'toggleUserIsActive' :
				$Backend->toggleUserIsActive();
			break;
			case 'toggleSchoolIsActive' :
				$Backend->toggleSchoolIsActive();
			break;
			case 'delete' :
				$Backend->delete();
			break;
			case 'schoolProfile' :
				$Backend->schoolProfile();
			break;
			case 'updateProfile' :
				$Backend->updateProfile();
			break;
			case 'upload' :
				$Backend->upload();
			break;
			case 'schoolHistory' :
				$Backend->schoolHistory();
			break;
			case 'getSchoolHistory' :
				$Backend->getSchoolHistory();
			break;
			case 'addSchoolPost' :
				$Backend->addSchoolPost();
			break;
			case 'uploadSchoolPost' :
				$Backend->uploadSchoolPost();
			break;
			default :
				//"action" value is unknow
				throw new Exception('L\'action renseignée est inexistante.');
		}
	} else {
		//"action" undefined
		$Backend->home();
	}
} catch (Exception $e) {
	$Backend->error($e->getMessage());
}
