<?php

/*
 * this file will respose profile related requests
 */

//check if request is ajax and redirect home if not
checkIsAjax();
//if not logged in error will be sent and header and content will be changed
is_logged(true);
//check for csrf issues first and only check if user logged in, if not logged in code does not come to this far
checkCsrf();

require_once "logic/profile.class.php";
$profile = new profile(getUserId());

$res = array('status' => true, 'text' => '', 'error' => '');
if (isset($_POST['action']) && trim($_POST['action']) != '') {
    //get time zone (phpTzIdentifier) from session
    $phpTzIdentifier = getPhpTzIdentifierFromSession();

    switch (trim($_POST['action'])) {
        case 'load':
            $res = $profile->profilePage(false);
            break;
        //for settings
        case 'saveSettings':
            $input = array();
            if ($_POST['data'] != '')
                parse_str($_POST['data'], $input);
            $res = $profile->saveSettings($_POST['panel'], $input);
            break;
        case 'resendcmv':
            $res = $profile->resendChangeEmailVerificaiton($_POST['id']);
            break;
        case 'removecmv':
            $res = $profile->removeChangeEmail($_POST['id']);
            break;
        case 'schoolEvents':
            //process event list in school section, join users to event , leave users from event
            $iData = array('phpTzIdentifier' => $phpTzIdentifier);
            if (!empty($_POST['uid']))
                $iData['uid'] = $_POST['uid'];
            if (!empty($_POST['type']))
                $iData['type'] = $_POST['type'];
            if (!empty($_POST['eventId']))
                $iData['eventId'] = $_POST['eventId'];
//            if (!empty($_POST['start']))
//                $iData['start'] = $_POST['start'];
//            if (!empty($_POST['end']))
//                $iData['end'] = $_POST['end'];
//            if (!empty($_POST['deadline']))
//                $iData['deadline'] = $_POST['deadline'];
            
            if (!empty($_POST['options']))
                $iData['options'] = $_POST['options'];

            $res = $profile->schoolEvents($_POST['mode'], $iData);
    }
} else {
    $res['status'] = false;
    $res['error'] = 'Invalid request.';
}
//sleep(3);
returnAjaxResult($res);

?>