<?php

session_start();

use \Chemin\ArtSchools\Model\Backend;

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

const BACKEND = true;
const FRONTEND = false;
const ALL_SCHOOL = 'allSchool';
const NO_SCHOOL = 'noSchool';
const ADMIN = 'admin';
const MODERATOR = 'moderator';
const STUDENT = 'student';
const USER = 'user';

const SITE_MAINTENANCE = false;

/*---------------------------------*/
try {
    $Backend = new Backend();
    $Backend->verifyInformation();
    if (isset($_GET['action']) && !SITE_MAINTENANCE) {
        switch ($_GET['action']) {
            case 'disconnect' :
                $Backend->disconnect();
                break;
            case 'addSchool' :
                $Backend->addSchool();
                break;
            case 'settings' :
                $Backend->settings();
                break;
            case 'moderatSchool' :
                $Backend->moderatSchool();
                break;
            case 'editSchool' :
                $Backend->editSchool();
                break;
            case 'moderatWebsite' :
                $Backend->moderatWebsite();
                break;
            case 'checkContract' :
                $Backend->checkContract();
                break;
            case 'checkWarnings' :
                $Backend->checkWarnings();
                break;
            case 'checkBanishments' :
                $Backend->checkBanishments();
                break;
            case 'checkUnusedImg' :
                $Backend->checkUnusedImg();
                break;
            case 'warnUser' :
                $Backend->warnUser();
                break;
            case 'addWarning' :
                $Backend->addWarning();
                break;
            case 'moderatAdmin' :
                $Backend->moderatAdmin();
                break;
            case 'moderatUsers' :
                $Backend->moderatUsers();
                break;
            case 'moderatReports' :
                $Backend->moderatReports();
                break;
            case 'getReports' :
                $Backend->getReports();
                break;
            case 'getReportsFromElem' :
                $Backend->getReportsFromElem();
                break;
            case 'getCountReports' :
                $Backend->getCountReports();
                break;
            case 'deleteReport' :
                $Backend->deleteReport();
                break;
            case 'deleteReportsFromElem' :
                $Backend->deleteReportsFromElem();
                break;
            case 'createGroup' :
                $Backend->createGroup();
                break;
            case 'getGroup' :
                $Backend->getGroup();
                break;
            case 'setGroup' :
                $Backend->setGroup();
                break;
            case 'deleteGroup' :
                $Backend->deleteGroup();
                break;
            case 'editGrade' :
                $Backend->editGrade();
                break;
            case 'toggleUserIsActive' :
                $Backend->toggleUserIsActive();
                break;
            case 'delete' :
                $Backend->delete();
                break;
            case 'leaveSchool' :
                $Backend->leaveSchool();
                break;
            case 'schoolProfile' :
                $Backend->schoolProfile();
                break;
            case 'updateProfile' :
                $Backend->updateProfile();
                break;
            case 'upload' :
                $Backend->upload();
                break;
            case 'schoolHistory' :
                $Backend->schoolHistory();
                break;
            case 'getSchoolHistory' :
                $Backend->getSchoolHistory();
                break;
            case 'addSchoolPost' :
                $Backend->addSchoolPost();
                break;
            case 'uploadSchoolPost' :
                $Backend->tryUploadSchoolPost();
                break;
            default :
                //"action" value is unknow
                throw new Exception('L\'action renseignée est inexistante.');
        }
    } else {
        if (SITE_MAINTENANCE) {
            //site is in maintenance
            header('Location: index.php');
        } else {
            //"action" undefined
            $Backend->home();
        }
    }
} catch (Exception $e) {
    $Backend->error($e->getMessage());
}
