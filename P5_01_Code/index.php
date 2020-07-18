<?php
if (!empty($_GET['action']) && ($_GET['action'] === 'search' || $_GET['action'] === 'advancedSearch')) {
    //disable validation of form by the browser
    session_cache_limiter('private_no_expire');
}
session_start();

use \Chemin\ArtSchool\Model\Frontend;

spl_autoload_register(
    function ($class) {
        $arr = str_split($class, 23);
        $nameClass = $arr[1];
    
        if ($nameClass === 'Frontend' || $nameClass === 'Backend' || $nameClass === 'Controller') {
            require 'controller/' . $nameClass . '.php';
        } else {
            require 'model/' . $nameClass . '.php';
        }
    }
);

const ALL_SCHOOL = 'allSchool';
const NO_SCHOOL = 'noSchool';
const ADMIN = 'admin';
const MODERATOR = 'moderator';
const STUDENT = 'student';
const USER = 'user';

/*---------------------------------*/
try {
    $Frontend = new Frontend();
    $Frontend->verifyInformation();
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
        case 'disconnect' :
            $Frontend->disconnect();
            break;
        case 'signUp' :
            $Frontend->signUp();
            break;
        case 'signIn' :
            $Frontend->signIn();
            break;
        case 'resetPassword' :
            $Frontend->resetPassword();
            break;
        case 'search' :
            $Frontend->search();
            break;
        case 'advancedSearch' :
            $Frontend->advancedSearch();
            break;
        case 'listTags' :
            $Frontend->listTags();
            break;
        case 'listSchools' :
            $Frontend->listSchools();
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
        case 'addSchool' :
            $Frontend->addSchool();
            break;
        case 'getSchools' :
            $Frontend->getSchools();
            break;
        case 'getPostsBySchool' :
            $Frontend->getPostsBySchool();
            break;
        case 'getUsersBySchool' :
            $Frontend->getUsersBySchool();
            break;
        case 'getUserPosts' :
            $Frontend->getUserPosts();
            break;
        case 'getSchoolPosts' :
            $Frontend->getSchoolPosts();
            break;
        case 'getProfilePosts' :
            $Frontend->getProfilePosts();
            break;
        case 'getLastPosted' :
            $Frontend->getLastPosted();
            break;
        case 'getMostLikedPosts' :
            $Frontend->getMostLikedPosts();
            break;
        case 'getPostsByTag' :
            $Frontend->getPostsByTag();
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
        case 'userAlreadyLikePost' :
            $Frontend->userAlreadyLikePost();
            break;
        case 'toggleLikePost' :
            $Frontend->toggleLikePost();
            break;
        case 'report' :
            $Frontend->report();
            break;
        case 'setReport' :
            $Frontend->setReport();
            break;
        case 'faq' :
            $Frontend->faq();
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
