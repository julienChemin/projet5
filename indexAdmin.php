<?php

session_start();

use \Chemin\ArtSchool\Model\Backend;

spl_autoload_register(function($class)
{
	$arr = str_split($class, 23);
	$nameClass = $arr[1];
	
	if ($nameClass === 'Frontend' || $nameClass === 'Backend') {
		require 'controller/' . $nameClass . '.php';
	} else {
		require 'model/' . $nameClass . '.php';
	}
});

$Backend = new Backend();

/*---------------------------------*/

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
			case 'editGrade' :
				$Backend->editGrade();
			break;
			case 'toggleIsActive' :
				$Backend->toggleIsActive();
			break;
			case 'delete' :
				$Backend->delete();
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
