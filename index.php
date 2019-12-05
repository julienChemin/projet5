<?php

session_start();

use \Chemin\ArtSchool\Model\Frontend;

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

$frontend = new Frontend();

/*---------------------------------*/

try {
	if (isset($_GET['action'])) {
		/*switch ($_GET['action']) {
			case 'posts' :
				
				break;
			default :
				//"action" value is unknow
				throw new Exception('L\'action renseignée est inexistante.');
		}*/
	} else {
		//"action" undefined
		$frontend->home();
	}
} catch (Exception $e) {
	$frontend->error($e->getMessage());
}
