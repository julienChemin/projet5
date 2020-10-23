<?php
if (isset($data['option'])) {
    foreach ($data['option'] as $option) {
        switch ($option) {
            case 'home' :
                echo '<script src="public/js/slide.js"></script>';
                if (defined('FRONTEND') && FRONTEND === true) {
                    echo '<script src="public/js/home.js"></script>';
                } else {
                    echo '<script src="public/js/homeAdmin.js"></script>';
                }
                break;
            case 'advancedSearch' :
                echo '<script src="public/js/advancedSearch.js"></script>';
                break;
            case 'listSchools' :
                echo '<script src="public/js/listSchools.js"></script>';
                break;
            case 'buttonToggleSchool' :
                echo '<script src="public/js/buttonToggleSchool.js"></script>';
                break;
            case 'forgetPassword' :
                echo '<script src="public/js/forgetPassword.js"></script>';
                break;
            case 'addSchool' :
                echo '<script src="public/js/addSchool.js"></script>';
                break;
            case 'moderatSchool' :
                echo '<script src="public/js/moderatSchool.js"></script>';
                break;
            case 'warnUser' :
                echo '<script src="public/js/warnUser.js"></script>';
                break;
            case 'moderatWebsite' :
                echo '<script src="public/js/moderatWebsite.js"></script>';
                break;
            case 'moderatAdmin' :
                echo '<script src="public/js/moderatAdmin.js"></script>';
                break;
            case 'moderatUsers' :
                echo '<script src="public/js/moderatUsers.js"></script>';
                break;
            case 'moderatReports' :
                echo '<script src="public/js/moderatReports.js"></script>';
                break;
            case 'schoolHistory' :
                echo '<script src="public/js/schoolHistory.js"></script>';
                break;
            case 'signUp' :
                echo '<script src="public/js/signUp.js"></script>';
                break;
            case 'signIn' :
                echo '<script src="public/js/signIn.js"></script>';
                break;
            case 'userProfile' :
                echo '<script src="public/js/fillProfileWithPosts.js"></script>';
                echo '<script src="public/js/userProfile.js"></script>';
                break;
            case 'schoolProfile' :
                echo '<script src="public/js/fillProfileWithPosts.js"></script>';
                echo '<script src="public/js/schoolProfile.js"></script>';
                break;
            case 'tinyMCE' :
                require 'gitignore/key.php';
                echo '<script src="https://cdn.tiny.cloud/1/' . $tinyMCEapiKey . '/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>';
                echo '<script src="public/js/tinyMCEinit.js"></script>';
                break;
            case 'addPost' :
                echo '<script src="public/js/addPost.js"></script>';
                break;
            case 'postView' :
                echo '<script src="public/js/postView.js"></script>';
                break;
            case 'folderView' :
                echo '<script src="public/js/postView.js"></script>';
                echo '<script src="public/js/fillProfileWithPosts.js"></script>';
                break;
            case 'faq' :
                echo '<script src="public/js/faq.js"></script>';
                break;
            default :
                throw new Exception('L\'option indiqué n\'existe pas.');
                break;
        }
    }
}
?>
<script src="public/js/ajax.js"></script>
<script src="public/js/footer.js"></script>
<script src="public/js/navbar.js"></script>