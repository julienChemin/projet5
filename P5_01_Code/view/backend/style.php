<?php
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'addSchool' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/backend/moderatSchool.css">';
            echo '<link rel="stylesheet" type="text/css" href="public/css/backend/home.css">';
        break;
        
        case 'settings' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/settings.css">';
        break;
        
        case 'moderatSchool' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/backend/moderatSchool.css">';
        break;
        
        case 'moderatWebsite' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/backend/moderatWebsite.css">';
        break;
        
        case 'moderatAdmin' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/backend/moderatAdmin.css">';
        break;
        
        case 'moderatUsers' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/backend/moderatUsers.css">';
        break;
        
        case 'moderatReports' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/backend/moderatReports.css">';
        break;
        
        case 'schoolProfile' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/profile.css">';
        break;
        
        case 'schoolHistory' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/backend/schoolHistory.css">';
        break;
        
        case 'addSchoolPost' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/addPost.css">';
        break;
        
    }
} else {
    //"action" undefined
    echo '<link rel="stylesheet" type="text/css" href="public/css/backend/home.css">';
    echo '<link rel="stylesheet" type="text/css" href="public/css/frontend/home.css">';
}
?>