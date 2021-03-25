<?php
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'signUp' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/frontend/signInSignUp.css">';
            break;
        case 'signIn' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/frontend/signInSignUp.css">';
            break;
        case 'resetPassword' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/frontend/signInSignUp.css">';
            break;
        case 'settings' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/settings.css">';
            break;
        case 'search' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/frontend/search.css">';
            break;
        case 'advancedSearch' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/frontend/search.css">';
            break;
        case 'listTags' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/frontend/search.css">';
            break;
        case 'listSchools' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/frontend/search.css">';
            break;
        case 'userProfile' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/profile.css">';
            break;
        case 'schoolProfile' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/profile.css">';
            break;
        case 'report' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/report.css">';
            break;
        case 'post' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/postAndFolder.css">';
            echo '<link rel="stylesheet" type="text/css" href="public/css/profile.css">';
            break;
        case 'addPost' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/addPost.css">';
            break;
        case 'faq' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/frontend/faq.css">';
            break;
        case 'cgu' :
            echo '<link rel="stylesheet" type="text/css" href="public/css/cgu.css">';
            break;
    }
} else {
    //"action" undefined
    echo '<link rel="stylesheet" type="text/css" href="public/css/frontend/home.css">';
}
?>