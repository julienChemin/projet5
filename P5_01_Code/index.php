<?php

if (!empty($_GET['action']) && ($_GET['action'] === 'search' || $_GET['action'] === 'advancedSearch')) {
    //disable validation of form by the browser
    session_cache_limiter('private_no_expire');
}
session_start();

use \Chemin\ArtSchools\Model\Frontend;

spl_autoload_register(
    function ($class) {
        $arr = str_split($class, 24);
        $nameClass = $arr[1];
    
        if ($nameClass === 'Frontend' || $nameClass === 'Backend' || $nameClass === 'Controller') {
            require 'controller/' . $nameClass . '.php';
        } else {
            require 'model/' . $nameClass . '.php';
        }
    }
);

const BACKEND = false;
const FRONTEND = true;
const ALL_SCHOOL = 'allSchool';
const NO_SCHOOL = 'noSchool';
const ADMIN = 'admin';
const MODERATOR = 'moderator';
const STUDENT = 'student';
const USER = 'user';

const SITE_MAINTENANCE = false;
const DURATION_MAINTENANCE = 'Moins de X minutes';
const REASON_MAINTENANCE = "R.A.S";

/*---------------------------------*/
try {
    $Frontend = new Frontend();
    $Frontend->verifyInformation();
    if (isset($_GET['action']) && !SITE_MAINTENANCE) {
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

            case 'settings' :
                $Frontend->settings();
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
                $Frontend->tryUploadPost();
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

            case 'getCommentsFromPosts' :
                $Frontend->getCommentsFromPosts();
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

            case 'updateUserSettings' :
                $Frontend->updateUserSettings();
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

            case 'cgu' :
                $Frontend->cgu();
            break;

            case 'forum' :
                $Frontend->forum();
            break;

            case 'category' :
                $Frontend->category();
            break;

            case 'forumTopic' :
                $Frontend->forumTopic();
            break;

            case 'createTopic' :
                $Frontend->createTopic();
            break;

            case 'addTopic' :
                $Frontend->addTopic();
            break;

            case 'deleteTopic' :
                $Frontend->deleteTopic();
            break;

            case 'editTopic' :
                $Frontend->editTopic();
            break;

            case 'updateTopic' :
                $Frontend->updateTopic();
            break;

            case 'addReply' :
                $Frontend->addReply();
            break;

            case 'deleteReply' :
                $Frontend->deleteReply();
            break;

            case 'editReply' :
                $Frontend->editReply();
            break;

            case 'updateReply' :
                $Frontend->updateReply();
            break;

            case 'cv' :
                $Frontend->cv();
            break;

            case 'editCv' :
                $Frontend->editCv();
            break;

            case 'deleteSection' :
                $Frontend->deleteSection();
            break;

            case 'updateCvBlockContent' :
                $Frontend->updateCvBlockContent();
            break;

            case 'deleteBlock' :
                $Frontend->deleteBlock();
            break;

            case 'portfolio' :
                $Frontend->portfolio();
            break;

            case 'editPortfolio' :
                $Frontend->editPortfolio();
            break;

            case 'toggleTopicIsClose' :
                $Frontend->toggleTopicIsClose();
            break;

            case 'toggleTopicIsPinned' :
                $Frontend->toggleTopicIsPinned();
            break;

            case 'updateCv' :
                $Frontend->updateCv();
            break;

            case 'addNewSection' :
                $Frontend->addNewSection();
            break;

            case 'changeSectionOrder' :
                $Frontend->changeSectionOrder();
            break;

            case 'updateCvBlock' :
                $Frontend->updateCvBlock();
            break;

            case 'changeCvBlockOrder' :
                $Frontend->changeCvBlockOrder();
            break;

            case 'addNewCvBlock' :
                $Frontend->addNewCvBlock();
            break;

            default :
                //"action" value is unknow
                throw new Exception('L\'action renseignée est inexistante.');
        }
    } else {
        if (SITE_MAINTENANCE) {
            //site is in maintenance
            $Frontend->maintenance();
        } else {
            //"action" undefined
            $Frontend->home();
        }
    }
} catch (Exception $e) {
    $Frontend->error($e->getMessage());
}
