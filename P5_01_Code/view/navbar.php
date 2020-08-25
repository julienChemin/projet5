<?php
if (defined('BACKEND') &&  BACKEND === true && !empty($_SESSION) && ($_SESSION['grade'] === ADMIN  || $_SESSION['grade'] === MODERATOR)) {
    if (!empty($_SESSION) && $_SESSION['school'] === ALL_SCHOOL) {
        require 'backend/navbarWebM.php';
    } else {
        require 'backend/navbarAdmin.php';
    }
} else {
    if (!empty($_SESSION)) {
        if ($_SESSION['school'] === ALL_SCHOOL) {
            require 'frontend/navbarWebM.php';
        } elseif ($_SESSION['grade'] === ADMIN || $_SESSION['grade'] === MODERATOR) {
            require 'frontend/navbarAdmin.php';
        } else {
            require 'frontend/navbarUser.php';
        }
    } else {
        require 'frontend/navbarDefault.php';
    }
}
?>
