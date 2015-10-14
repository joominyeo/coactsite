<?php

/*
 * this file will respose admin related requests
 */
//check if request is ajax and redirect home if not
checkIsAjax();
//if not logged in error will be sent and header and content will be changed
is_logged(true);
//check for csrf issues first and only check if user logged in, if not logged in code does not come to this far
checkCsrf();

$res = array('status' => true, 'text' => '', 'error' => '');

//make sure logged in user is an admin before allowed call admin functions
if (!isAdmin()) {
    //if not admin return error
    $res['status'] = false;
    $res['error'] = 'Invalid request.';
} else {
    //ok logged in user is admin
    require_once "logic/admin.class.php";
    $admin = new admin($_SESSION['id']);

    if (isset($_POST['action']) && trim($_POST['action']) != '') {
        //get time zone (phpTzIdentifier) from session
        $phpTzIdentifier = getPhpTzIdentifierFromSession();

        switch (trim($_POST['action'])) {
            case 'load':
                $res = $admin->adminPage();
                break;
            case 'loadSection':
                $options = array();
                if (!empty($_POST['options']))
                    $options = $_POST['options'];
                $res = $admin->loadAdminSection($_POST['section'], $options, $phpTzIdentifier);
                break;
            case 'event':
                //process event add/edit/delete
                $iData = array('phpTzIdentifier' => $phpTzIdentifier);
                if (!empty($_POST['uid']))
                    $iData['uid'] = $_POST['uid'];
                if (!empty($_POST['name']))
                    $iData['name'] = $_POST['name'];
                if (!empty($_POST['type']))
                    $iData['type'] = $_POST['type'];
                if (!empty($_POST['start']))
                    $iData['start'] = $_POST['start'];
                if (!empty($_POST['end']))
                    $iData['end'] = $_POST['end'];
                if (!empty($_POST['deadline']))
                    $iData['deadline'] = $_POST['deadline'];
                if (!empty($_POST['description']))
                    $iData['description'] = $_POST['description'];
                if (!empty($_POST['publish']))
                    $iData['publish'] = $_POST['publish'];

                if (!empty($_POST['options']))
                    $iData['options'] = $_POST['options'];

                $res = $admin->events($_POST['mode'], $iData);
                break;
            case 'eventNotifications':
                //process eventNotifications add/edit/delete/list
                $iData = array('phpTzIdentifier' => $phpTzIdentifier);

                $iData['eventId'] = $_POST['eventId'];
                if (!empty($_POST['uid']))
                    $iData['uid'] = $_POST['uid'];
                if (!empty($_POST['title']))
                    $iData['title'] = $_POST['title'];
                if (!empty($_POST['publish']))
                    $iData['publish'] = $_POST['publish'];
                if (!empty($_POST['text']))
                    $iData['text'] = $_POST['text'];

                if (!empty($_POST['options']))
                    $iData['options'] = $_POST['options'];

                $res = $admin->eventNotifications($_POST['mode'], $iData);
                break;
            case 'eventUsers':
                //process eventNotifications list
                $iData = array('phpTzIdentifier' => $phpTzIdentifier);

                $iData['eventId'] = $_POST['eventId'];
                if (!empty($_POST['uid']))
                    $iData['uid'] = $_POST['uid'];

                if (!empty($_POST['options']))
                    $iData['options'] = $_POST['options'];

                $res = $admin->eventUsers($_POST['mode'], $iData);
                break;
        }
    } else {
        $res['status'] = false;
        $res['error'] = 'Invalid request.';
    }
}
//sleep(3);
returnAjaxResult($res);

?>