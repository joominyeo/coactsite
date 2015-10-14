<?php

/*
 * this file will respose to requests to sign up accounts and profile settings
 */

//check if request is ajax and redirect home if not
checkIsAjax();

//check for csrf issues first and only check if user logged in
if (is_logged())
    checkCsrf();

require_once "logic/user.class.php";
$user = new user();

$res = array('status' => true, 'text' => '', 'error' => '');
if (isset($_POST['action']) && trim($_POST['action']) != '') {
    switch (trim($_POST['action'])) {
        case 'signUpPage':
            $res = $user->signUpPage();
            break;
        case 'signup':
            parse_str($_POST['data'], $input);
            $input['tzOffset'] = $_POST['tzOffset'];
            if (SIGNUP_CAPTCHAR_VERSION == "1") {
                $input['recaptcha_response_field'] = $_POST['recaptcha_response_field'];
                $input['recaptcha_challenge_field'] = $_POST['recaptcha_challenge_field'];
            } else {
                if (isset($input['g-recaptcha-response']))
                    $input['recaptcha_response_field'] = $input['g-recaptcha-response'];
                else
                    $input['recaptcha_response_field'] = '';
            }
            $res = $user->signup($input);
            break;
        case 'signInPage':
            $res = $user->signInPage();
            break;
        case 'signin':
            parse_str($_POST['data'], $input);
            $res = $user->signin($input);
            break;
        case 'signout':
            $res = $user->signout();
            break;
        case 'resendv':
            $res = $user->resendVerifyEmail($_POST['id'], 'email');
            break;
        case 'resetplink':
            $res = $user->requestPasswordResetLink($_POST['username_or_email']);
            break;
        case 'changepass':
            $res = $user->resetPassword($_POST['rpassword'], $_POST['rcpassword']);
            break;
        case 'support':
            $res = $user->supportReqest($_POST['email'], $_POST['name'], $_POST['description'], $_POST['type']);
            break;
//        case 'homepage':
//            $res = $user->loadHomePage();
//            break;
//        case 'signUpCheckEmailAvailable':
//            $res = $user->signUpCheckEmailAvailable($_POST['email']);
//            break;
    }
} else {
    $res['status'] = false;
    $res['error'] = 'Invalid request.';
}
//sleep(5);
returnAjaxResult($res);


?>