<?php

/**
 * when load site by url for any page all goes though this and handled by here
 * 
 * Note: convert later to OOP
 */
require_once "logic/user.class.php";
$user = new user();
//start of auto login with remembed account
if (!is_logged())
    $user->signInRemembered();
//end of auto login with remembed account

$footJs = '';
$verify = false;

//$verifyCode must be already set in router.php if we are working on verification
if (!empty($verifyCode)) {
    /*
     * this mean verification code is passed so we have to invoke verfication functionality
     * if we have seesion that is logged in we clear session and start over so no one is logged in to 
     * our site using that browser when verfication works
     */
    $verify = true;
    $res = $user->verifications($verifyCode);

    if (!$res['status']) {
        $alertMessage = $res['error'];
        $alertClass = 'danger';
    } else {
        $alertMessage = $res['text'];
        $alertClass = 'success';
        //if we are going to reset password we need to show password change popup
        if (!empty($res['popupc'])) {
            //for this case $alertMessage is empty so no alert script
            $footJs.=resetPassPopupJs($res);
        } else {
            // if not password change then verficaiton are sign up email or change email verficaiton
        }
    }
    if ($alertMessage != '')
        $footJs.=verifyAlertJs($alertMessage, $alertClass);
}


$page = ''; //page going to load as home page to set js in footer
$centent = ''; //markup for #centent inner html
/*
 * if USERNAME true and if site/username requested even user not logged in we show public profile data
 */
if (USERNAME && isset($_REQUEST['username']) && $_REQUEST['username'] != '') {
    //load requested username user details
    $ruContent = loadRequestedUser($_REQUEST['username'], true);
    $page = $ruContent['page'];
    $footJs .= $ruContent['footJs'];
    $centent = $ruContent['cnt'];
} else if ($directPageLoad != '') {
    /*
     * $directPageLoad already set in route() function
     * if user has requested direct page load like site/signup or site/signin
     * accoding to getInforFromUrl() logic $_REQUEST['username'] and $directPageLoad will not be avaible 
     * at once. so order of above two if closes does not matter
     */

    if (!isset($options))
        $options = array();
    $dlContent = directLoadPage($directPageLoad, $options);
    //$page = $fcontent['page'];//not needed to set and already set
    //page content
    $centent = $dlContent['cnt'];
    $footJs .= $dlContent['footJs'];
} else {
    //load default center contains for home page
    if (!is_logged()) {
        //get home page content
        $centent = homePageContent();
    } else {
        //load profile page page
        $fcontent = firtPage(true);
        $page = $fcontent['page'];
        $centent = $fcontent['cnt'];
        $footJs .= $fcontent['footJs'];
    }
}

//get complete html for page show here
$pageHtml = pageHtml($centent, $page, $footJs, $directPageLoad, $options);
//output page html
echo $pageHtml;

/**
 * load user requested by site/username
 */
function loadRequestedUser($username, $reload = false) {

    require_once "ui/ui.header.class.php";
    $uiHeader = new uiHeader();

    require_once "logic/user.class.php";
    $user = new user();
    $cnt = $page = $footJs = '';
    //get user infor for passed $username
    $userData = $user->getUserByUsername($username, array('id', 'status', 'user_public', 'verified', 'fname', 'lname'));
    if (!empty($userData)) {
        if ($userData['status'] == 'deleted' && !isAdmin()) {
            $cnt = 'Sorry! This account has been deleted.';
        } else if ($userData['status'] == 'suspended' && !isAdmin()) {
            $cnt = 'Sorry! This account has been suspended.';
        } else if ($userData['user_public'] != '1' && !isAdmin()) {
            $cnt = 'Sorry! This user does not have pubic profile.';
        } elseif ($userData['verified'] != '1' && !isAdmin()) {
            $cnt = 'Sorry! This user is not active yet.';
        } else {
            if (!is_logged()) {
                /*
                 * load page in step 2
                 */
                $cnt = '';
                $footJs = '$( document ).ready(function() {   
                            var opt={username:"' . $username . '"};
                            loadMainContent(\'visitor\',opt);
                         });';
            } else {
                require_once "logic/visitor.class.php";
                $visitor = new visitor($userData['id']);
                $cnt = $visitor->profileSummery(true, $reload, false, $username);
                $page = 'visitor';
            }
        }
    } else {
        $cnt = $uiHeader->noUserFound();
        $footJs = ' $( document ).ready(function() {
                        scrollToViewTop();
                    });';
    }
    return array('cnt' => $cnt, 'page' => $page, 'footJs' => $footJs);
}

/**
 * return reset password on verfication popup js
 */
function resetPassPopupJs($res) {
    $Js = '$( document ).ready(function() {
                $.fancybox({
                    content:\'' . $res['popupc'] . '\',
                    closeBtn:true,
                    maxWidth:\'90%\',
                    minWidth:\'10%\',
                    autoSize:true,
                    autoResize:true,
                    autoCenter:true,
                    fitToView:true,
                    nextClick :false,
                    scrolling:\'no\',
                    arrows :false,        
                    openEffect: \'elastic\',
                    closeEffect: \'elastic\',
                    padding: [0,15,15,15],
                    title:fancyBoxCustomTitile(\'' . langText('reset_password') . '\'),
                    helpers : {
                        title: {
                            type: \'inside\',
                            position: \'top\'
                        }
                    }
                });
                //change url to home url remving verify query string
                ChangeUrl(\'\',false);
            });';
    return $Js;
}

/**
 * return alert js for verification
 */
function verifyAlertJs($alertMessage, $alertClass) {
    $fade = 'true';
    if ($alertClass == 'success') {
        //we check if we have any links in $alertMessage, if so we don't fade message
        if (preg_match('/\<a/', $alertMessage))
            $fade = 'false';
    }
    $Js = '  $( document ).ready(function() {
                showNotificationAlert(\'' . $alertMessage . '\',\'' . $alertClass . '\',' . $fade . ',3000,true,true); 
                //change url to home url removing verify query string
                ChangeUrl(\'\',false);
            });';
    return $Js;
}

/**
 * load page direclty like
 * site/signup
 * site/signin
 * 
 */
function directLoadPage($directPageLoad, $options) {
    //sign up page markup
    $cnt = $footJs = '';

    if (isset($options['firstParamIsDataset']) && $options['firstParamIsDataset'] === true) {
        //for view data url
        if ((!empty($options['slashParams']) && count($options['slashParams']) > 1)) {
            require_once "logic/dataset.class.php";
            $dataset = new dataset(getUserId());
            //we have second slash paramter in url so we should load ministry page
            $uid = getUniqueIdFromParam($options['slashParams'][1]);

            $cntRes = $dataset->directViewSingleData($directPageLoad, $uid, array(), 'UTC', 1, 0);
            $cnt .=$cntRes['cnt'];
            //we have selected a categories
            $footJs.=$cntRes['footJs'];
        }
    } else {
        //other cases except view url
        switch (strtolower($directPageLoad)) {
            case 'register':
                require_once "logic/user.class.php";
                $user = new user();
                $cntRes = $user->directLoadSignUpPage(true);
                $cnt .=$cntRes['cnt'];
                $footJs.=$cntRes['footJs'];
                break;
            case 'login':
                require_once "logic/user.class.php";
                $user = new user();
                $cntRes = $user->directLoadSignInPage(true);
                $cnt .=$cntRes['cnt'];
                $footJs.=$cntRes['footJs'];               
                break;
            case 'school':
//                require_once "logic/user.class.php";
//                $user = new user();
//                $cntRes = $user->directLoadSignUpPage(true);
//                $cnt .=$cntRes['cnt'];
//                $footJs.=$cntRes['footJs'];
                $cnt .= homePageContent();
                break;
            case 'home':
                $cnt .= homePageContent();
                break;            
        }
    }
    return array('cnt' => $cnt, 'footJs' => $footJs);
}

?>