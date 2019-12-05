<?php

if (isset($_SESSION)) {
	session_destroy();
}

if (isset($_COOKIE['artSchoolAdminId'])) {
	setcookie('artSchoolAdminId', '', time()-3600, null, null, false, true);
}
