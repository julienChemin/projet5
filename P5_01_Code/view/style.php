<?php
echo '<link rel="stylesheet" type="text/css" href="public/css/styleGeneral.css">';
// no action
if (defined('FRONTEND') && FRONTEND === true) {
    // home frontend
    echo '<link rel="stylesheet" type="text/css" href="view/frontend/style.php">';
} else {
    // home backend
    echo '<link rel="stylesheet" type="text/css" href="view/backend/style.php">';
}
?>
