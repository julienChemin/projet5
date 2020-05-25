<?php

session_start();

use \Chemin\ArtSchool\Model\Frontend;

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

const ALL_SCHOOL = 'allSchool';
const NO_SCHOOL = 'noSchool';
const ADMIN = 'admin';
const MODERATOR = 'moderator';
const STUDENT = 'student';
const USER = 'user';

/*---------------------------------*/
$Frontend = new Frontend();
try {
	if (isset($_GET['action'])) {
		switch ($_GET['action']) {
			case 'resetPassword' :
				$Frontend->resetPassword();
			break;
			case 'disconnect' :
				$Frontend->disconnect();
			break;
			case 'signUp' :
				$Frontend->signUp();
			break;
			case 'signIn' :
				$Frontend->signIn();
			break;
			case 'userProfile' :
				$Frontend->userProfile();
			break;
			case 'schoolProfile' :
				$Frontend->schoolProfile();
			break;
			case 'updateProfile' :
				$Frontend->updateProfile();
			break;
			case 'upload' :
				$Frontend->upload();
			break;
			case 'post' :
				$Frontend->post();
			break;
			case 'addPost' :
				$Frontend->addPost();
			break;
			case 'uploadPost' :
				$Frontend->uploadPost();
			break;
			case 'getTags' :
				$Frontend->getTags();
			break;
			case 'getSchools' :
				$Frontend->getSchools();
			break;
			case 'getPostsBySchool' :
				$Frontend->getPostsBySchool();
			break;
			case 'getUserPosts' :
				$Frontend->getUserPosts();
			break;
			case 'getSchoolPosts' :
				$Frontend->getSchoolPosts();
			break;
			case 'setComment' :
				$Frontend->setComment();
			break;
			case 'deleteComment' :
				$Frontend->deleteComment();
			break;
			case 'deletePost' :
				$Frontend->deletePost();
			break;
			default :
				//"action" value is unknow
				throw new Exception('L\'action renseignée est inexistante.');
		}
	} else {
		//"action" undefined
		$Frontend->home();
	}
} catch (Exception $e) {
	$Frontend->error($e->getMessage());
}
