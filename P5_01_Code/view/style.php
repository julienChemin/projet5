<?php
echo '<link rel="stylesheet" type="text/css" href="public/css/styleGeneral.css">';
// no action
if (defined('FRONTEND') && FRONTEND === true) {
    // home frontend
    require 'view/frontend/style.php';
} else {
    // home backend
    require 'view/backend/style.php';
}
?>
