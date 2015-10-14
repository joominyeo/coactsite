<?php

/*
 * all common functions need to run application
 * 
 * all functions that has been used more than one place  is in this file 
 * Note: convert later to OOP
 */

function get($what, $default = '') {
    return(isset($_GET[$what]) ? $_GET[$what] : $default);
}

function post($what, $default = '') {
    return(isset($_POST[$what]) ? $_POST[$what] : $default);
}

function init() {

    $whitelistExpection = False;

    if (APPMODE == "maint") {

        /*
         * check whitelist for maintenance MAINT-WHITELIST          
         */
        $maintWhitelist = explode(',', MAINT_WHITELIST);
        if (!empty($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $maintWhitelist)) {
            $whitelistExpection = True;
        } else {
            ob_get_clean();
            include 'maint.html';
            die();
        }
    }

    //send error to support in production
    if (APPMODE == 'prod' || $whitelistExpection == True) {
        //set custom error handler
        set_error_handler("ErrorHandler", error_reporting());
        register_shutdown_function('shutdown');

        // let error handler handle errors
        error_reporting(-1);
        ini_set("log_errors", 1);  //  this is new, need to test..
        ini_set("display_errors", 0);
        ini_set("display_startup_errors", 0);
        start_user_session();
    }

    if (APPMODE == "dev") {
        // clean all errors,warnings...
        // -1 ensure all errors in all php versions
        error_reporting(-1);
        ini_set("display_errors", 1);
        ini_set("display_startup_errors", 1);
        ini_set("log_errors", 1);
        ini_set("html_errors ", 0);
        ini_set("error_log", "/tmp/php-error.log");
        start_user_session();
    }
}

/**
 * start user session
 */
function start_user_session() {
    if (php_sapi_name() != "cli") {
        if (session_id() == '')
            session_start();
    }
}

/**
 * send email
 * 
 * @param string $to a email or array of email
 * @param string $subject
 * @param string $body
 */
function sendEmail($to, $subject = '', $body = '', $api = false, $userId = 0) {

    if (APPMODE != 'prod') {
        $body = $body . '<br>' . SITE_URL;
    }

    if (is_array($to))
        $logTo = implode(',', $to);
    else
        $logTo = $to;

    try {
        // Create the Mailer using any Transport
        $mailer = Swift_Mailer::newInstance(
                        Swift_SmtpTransport::newInstance(SMTP_HOST, SMTP_PORT, SMTP_SECURE)->setUsername(SMTP_USERNAME)->setPassword(SMTP_PASSWORD)
        );

        // To use the ArrayLogger
        $logger = new Swift_Plugins_Loggers_ArrayLogger();
        $mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

        // Or to use the Echo Logger
        //$logger = new Swift_Plugins_Loggers_EchoLogger();
        //$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
        // Continue sending as normal
        //for ($lotsOfRecipients as $recipient) {
        $message = Swift_Message::newInstance();
        $message->setSubject($subject)
                ->setFrom(array(SMTP_FROM_EMAIL => '' . EMAIL_FROM_NAME))
                ->setTo($to)
                ->setContentType('text/html')
                ->setBody($body);
        $result = $mailer->send($message);

//        writelog('Mail Sent:' . $result . ' sent to ' . $logTo);
        // Dump the log contents
        // NOTE: The EchoLogger dumps in realtime so dump() does nothing for it
//        writelog($logger->dump());
        logAction('send', 'email', 'email send', 'common.class.php , email sent to : ' . $logTo . ' , subject : ' . $subject . ', body : ' . $body, false, $api, $userId);
    } catch (Exception $e) {
        errorLog(print_r($e, true));
        logAction('fail', 'email', 'email fail', 'common.class.php , email failed to : ' . $logTo . ' , subject : ' . $subject . ', body : ' . $body, false, $api, $userId);
    }
}

function devurandom_rand($min = 0, $max = 0x7FFFFFFF) {
    $diff = $max - $min;
    if ($diff < 0 || $diff > 0x7FFFFFFF) {
        throw new RuntimeException("Bad range");
    }
    $bytes = mcrypt_create_iv(4, MCRYPT_DEV_URANDOM);

    if ($bytes === false || strlen($bytes) != 4) {
        throw new RuntimeException("Unable to get 4 bytes");
    }
    $ary = unpack("Nint", $bytes);
    $val = $ary['int'] & 0x7FFFFFFF;   // 32-bit safe
    $fp = (float) $val / 2147483647.0; // convert to [0,1]
    return round($fp * $diff) + $min;
}

function is_mobile() {
    $useragent = $_SERVER['HTTP_USER_AGENT'];

    if (preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4)))
        return true;
    else
        return false;
}

/**
 * return true if user logged in and false else
 */
function is_logged($ajax = false) {
    $loged = false;
    if (isset($_SESSION['id']) && !empty($_SESSION['id']))
        $loged = true;
    else
        $loged = false;
    //if ajax reqest we have to return json so user will noticed that they are logged out
    if (!$loged && $ajax === true) {
        //get forced Log Out Data
        $res = forcedLogOutData();
        returnAjaxResult($res);
    } else
        return $loged;
}

/**
 * return forced Log Out Data
 * 
 * we don't(should not) show any error we just load home page 
 */
function forcedLogOutData() {
    //get home page content
    $centent = ''; //homePageContent();
    //get list of lanuages 
    $langs = languageList();

    if (!class_exists('uiCommon'))
        require_once "ui/ui.common.class.php";
    $uiCommon = new uiCommon();

    $res = array();
    $res['status'] = false;
    $res['flogout'] = true; //indicate force logged out
    $res['error'] = '';
    $res['snav'] = $uiCommon->sideNavOptions($langs);
    $res['contactUs'] = $uiCommon->contactSupportModelMrkup('', '');

    $res['cnt'] = $centent; //content to replace center part

    return $res;
}

/**
 * return home page content
 */
function homePageContent() {
    require_once "ui/ui.landpage.class.php";
    $uiLandPage = new uiLandPage();

    $schoolCnt = '';

    if (is_logged()) {
        //if use logged in we show school section content
        $userId = getUserId();
        require_once "logic/profile.class.php";
        $profile = new profile($userId);
        $schoolCnt = $profile->schoolSection(true, true);
    }


    //get home page content
    $centent = $uiLandPage->getLandingPageUi('', $schoolCnt);
    return $centent;
}

/*
 * check if logged inuser is admin and return true if admin and false if not
 */

function isAdmin() {
    $admin = false;
    //not logged so no need check for admin return false
    if (is_logged()) {
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin')
            $admin = true; //admin is logged in
    }
    return $admin;
}

/**
 * check if we are loading other user's data or loading data for user without loged in.
 * In this case we load view only data in read only mode 
 * this is specailly for search and load user using site/username
 * 
 * @params $userId we trying to load data
 */
function isVisitorsView($userId, $username, $api = false) {
    $visitorsView = false;
    if (!$api) {
        if (!is_logged(false))
            $visitorsView = true;
        else {
            //if logged in need make sure user id we trying to load data is same to logged in user id
            if ($_SESSION['id'] != $userId || trim($username) != '')
                $visitorsView = true; //loading some else data
        }
    }else {
        //for api
    }



    return $visitorsView;
}

/*
 * check if given action name is in $permission array
 * if $permission array is empty it means all allowed
 * if $ajax true just echo error and die else return bool
 */

function isAllowedAction($action, $permission, $ajax = false) {
    $allowed = true;
    if (!empty($permission) && !in_array($action, $permission))
        $allowed = false;

    if ($ajax === true && !$allowed) {
        $res = array();
        $res['status'] = false;
        $res['error'] = 'Sorry! You don\'t have permission for requested action.';

        die(json_encode($res));
    } else
        return $allowed;
}

/*
 * assign output of execution of PHP script to a variable
 * 
 * usage
 * $path = '../photos_page.php';
 * $html = getScriptOutput($path);
 * 
 * if( $html === FALSE)
 * {
 *    # Action when fails
 * }
 * else
 * {
 *    echo $html;
 * }
 * 
 */

function getScriptOutput($path, $print = FALSE) {
    ob_start();

    if (is_readable($path) && $path) {
        include $path;
    } else {
        return FALSE;
    }

    if ($print == FALSE)
        return ob_get_clean();
    else
        echo ob_get_clean();
}

/*
 * delete file 
 * $path full path
 * ex: /var/sites/dev2/www/u/image_1.jpg
 */

function delete_file($path) {
    writelog('common.class.php, delete file : ' . $path);
    return unlink($path);
}

/**
 * fucntion to test time zone conversion to server time and back to local
 */
function testTimeZoneConversion() {
    $utcDate = date('Y-m-d H:i:s');
    echo 'utcDate=' . $utcDate . '<br>';
    $testTime = toLocalDateTime($utcDate, 'Asia/Colombo'); //Asia/Colombo,America/New_York,Pacific/Fiji
    echo 'local date:' . $testTime . '<br>';
    $utcback = toServerDateTime($testTime, 'Asia/Colombo');
    echo 'utc back:' . $utcback . '<br>';

    die('test function testTimeZoneConversion');
}

/**
 * return formatted local time for given time zone .
 *  * 
 * @param string $dateTime strting utc date time as described here http://php.net/manual/en/datetime.formats.date.php
 * @param string $phpTzIdentifier timezone identifiers that utc date need to converted to and return from function as described here http://php.net/manual/en/datetimezone.listidentifiers.php
 * @param string $format date time formats as here http://php.net/manual/en/function.date.php
 * @param string $serverPhpTzIdentifier server timezone identifiers that date need to converted to, 
 *                                      <br>and values return from function as described here 
 *                                      <br>http://php.net/manual/en/datetimezone.listidentifiers.php
 * @return string time formated as requested in $format
 */
function toLocalDateTime($dateTime, $phpTzIdentifier = 'UTC', $format = 'Y-m-d H:i:s',$serverPhpTzIdentifier = SERVER_PHP_TZ_IDENTIFIER_DEFAULT) {
    // Create two timezone objects, one for server and one for $phpTzIdentifier
    $dateTimeZoneSever = new DateTimeZone($serverPhpTzIdentifier);
    //create date time object with server time
    $date = new DateTime(trim($dateTime),$dateTimeZoneSever);
    //convert date time object to given time zone in $phpTzIdentifier
    $date->setTimezone(new DateTimeZone($phpTzIdentifier));
    return $date->format($format);
}

/**
 * return php Tz Identifier from sesion if user logged in, else reutrn UTC
 * This is done to use for website and do not use for api since rest api does not mantain a session
 */
function getPhpTzIdentifierFromSession() {
    $phpTzIdentifier = PHP_TZ_IDENTIFIER_DEFAULT;
    if (TIME_ZONE && is_logged() && !empty($_SESSION['phpTzIdentifier']))
        $phpTzIdentifier = $_SESSION['phpTzIdentifier'];

    return $phpTzIdentifier;
}

/**
 * toServerdateTime
 * 
 * @param string $dateTime string local date time to conver to UTC and format as described here http://php.net/manual/en/datetime.formats.date.php
 * @param string $phpTzIdentifier timezone identifiers that local date is in which  need to converted to UTC and value has be value returns from function as described here http://php.net/manual/en/datetimezone.listidentifiers.php
 * @param string $format date time formats as here http://php.net/manual/en/function.date.php
 * @param string $serverPhpTzIdentifier server timezone identifiers that date need to converted to, 
 *                                      <br>and values return from function as described here 
 *                                      <br>http://php.net/manual/en/datetimezone.listidentifiers.php
 * 
 * @return string time formated as requested in $format
 */
function toServerDateTime($dateTime, $phpTzIdentifier = 'UTC', $format = 'Y-m-d H:i:s',$serverPhpTzIdentifier = SERVER_PHP_TZ_IDENTIFIER_DEFAULT) {
    //create date time object with local time zone
    $dtz = new DateTimeZone($phpTzIdentifier);
    $date = new DateTime(trim($dateTime), $dtz);
    //convert date time object to server time
    $date->setTimezone(new DateTimeZone($serverPhpTzIdentifier));
    return $date->format($format);
}

/**
 * get time zone offset from server time
 * 
 * @param string $phpTzIdentifier timezone identifiers that local date is in which  need to converted to UTC and value has be value returns from function as described here http://php.net/manual/en/datetimezone.listidentifiers.php
 * @param string $unit hours|minutes|seconds
 * @param string $serverPhpTzIdentifier server timezone identifiers that date need to converted to, 
 *                                      <br>and values return from function as described here 
 *                                      <br>http://php.net/manual/en/datetimezone.listidentifiers.php
 * 
 * @return type
 */
function getTimeZoneOffset($phpTzIdentifier = 'UTC', $unit = 'hours',$serverPhpTzIdentifier = SERVER_PHP_TZ_IDENTIFIER_DEFAULT) {

    // Create two timezone objects, one for server time and one for $phpTzIdentifier
    $dateTimeZoneSever = new DateTimeZone($serverPhpTzIdentifier);
    $dateTimeZone = new DateTimeZone($phpTzIdentifier); //ex : test for these America/Los_Angeles,Asia/Colombo,Asia/Kathmandu
    //create date time for now with $dateTimeZoneSever
    $dateTimeUtc = new DateTime("now", $dateTimeZoneSever);

    // Calculate the GMT offset for the date/time contained in the $dateTimeZoneSever
    // object, but using the timezone rules as defined for $phpTzIdentifier time zone
    $timeOffset = $dateTimeZone->getOffset($dateTimeUtc);

    if ($unit == 'hours')
        $timeOffset = round((($timeOffset / 60) / 60), 2);
    else if ($unit == 'minutes')
        $timeOffset = round(($timeOffset / 60), 2);

    return $timeOffset;
}

/**
 * return local today date according to time zone given
 * 
 * @param type $phpTzIdentifier
 * @param type $format
 * @return type
 */
function getLocalToday($phpTzIdentifier = 'UTC', $format = 'Y-m-d') {
    $todayDateLocal = toLocalDateTime(date('Y-m-d H:i:s'), $phpTzIdentifier, $format);

    return $todayDateLocal;
}

/*
 * check if given text is matched to reserved words
 */

function isReserved($text) {
    $resvd = reservedNames();
    $joined = '(' . implode(')|(', $resvd) . ')';
    $regex = '/' . $joined . '/i'; //case insenstive

    if (preg_match($regex, $text)) {
        //matched to reseved name
        return true;
    }
    return false;
}

/*
 * return reserved names and regex without skip // for php preg_match
 */

function reservedNames() {
    $resvn = array(
        '^security.*',
        '^webmaster.*',
        '^root.*',
        '^contact.*',
        '^info.*',
        '^master.*',
        '^app.*',
        '^email.*',
        '^blog.*',
        '^.*support.*$',
        '^.*admin.*$',
        '^.*database.*$',
        '^alpha.*',
        '^beta.*',
        '^live.*',
        '^test.*',
        '^.*payment.*$',
        '^.*maintenance.*$',
        '^template.*',
        '^forum.*',
        '^.*affiliate.*$',
        '^signup$',
        '^signin$',
        '^home$',
        '^pics$',
        '^album$',
        '^settings$',
        '^help$',
        '^feedback$'
    );
    //get regex to match for file types
    $fTypes = regexArrayFileTypesMatch();
    //merge array
    $resvd = array_merge($resvn, $fTypes);

    return $resvd;
}

/**
 * array of regex to match for file types
 * this can be used to not allow have usernames like files 
 * and also manage request urls
 * 
 * php, html, htm, js, css, png, jpg, jpeg, gif
 */
function regexArrayFileTypesMatch() {
    $fTypes = array(
        '^.*\.php$',
        '^.*\.html$',
        '^.*\.htm$',
        '^.*\.js$',
        '^.*\.css$',
        '^.*\.png$',
        '^.*\.jpg$',
        '^.*\.jpeg$',
        '^.*\.gif$'
    );
    return $fTypes;
}

/**
 * check if given text is matched to files types we allow in site
 */
function isFileType($text) {
    $fTypes = regexArrayFileTypesMatch();
    $joined = '(' . implode(')|(', $fTypes) . ')';
    $regex = '/' . $joined . '/i'; //case insenstive

    if (preg_match($regex, $text)) {
        //matched to file types
        return true;
    }
    return false;
}

/*
 * simple test log for use my testing
 * 
 */

function testLog($text) {
    $file = 'test.txt';
// Write the contents back to the file
    file_put_contents($file, $text . ' ' . date("Y-m-d h:i:sA") . ",\n", FILE_APPEND | LOCK_EX);
}

/*
 * log error
 */

function errorLog($logentry, $lgname = ERROR_LOGFILE) {
    writelog($logentry, $lgname);
}

/*
 * return data for access log
 */

function getAccessLogData() {
    $args = array(
        'REMOTE_ADDR',
        'HTTP_REFERER',
        'HTTP_USER_AGENT',
        'HTTP_ACCEPT_LANGUAGE',
        'HTTP_X_FORWARDED',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED_HOST',
        'HTTP_X_FORWARDED_SERVER',
        'HTTP_CLIENT_IP'
    );

    $sqlData = array();
    $sqlFields = $sqlValues = '';
    $logText = '';
    foreach ($args as $arg) {
        if (isset($_SERVER[$arg])) {
            $logText.= $arg . ":" . $_SERVER[$arg] . "\n";
            $sqlData[strtolower($arg)] = $_SERVER[$arg];
            $sqlFields.=strtolower($arg) . ',';
            $sqlValues.=':' . strtolower($arg) . ',';
        } else {
            $logText.= $arg . ":Not Set\n";
        }
    }

    return array('sqlData' => $sqlData, 'sqlFields' => $sqlFields, 'sqlValues' => $sqlValues, 'logText' => $logText);
}

/*
 * log error
 * 
 * $deniedType : ip|country
 */

function accessDeniedLog($logentry, $accessType = '', $deniedType = '', $ip = '', $country_id = 0, $lgname = ACCESS_DENIED_LOGFILE) {
    $accessLogData = getAccessLogData();
    //inset to database
    $db = getDbClassInstance();

    $userId = getUserId();

    //for db log
    $data = $accessLogData['sqlData'];
    $data['user_id'] = $userId;
    $data['access_type'] = $accessType;
    $data['denied_type'] = $deniedType;
    $data['used_as_ip_addr'] = $ip;
    $data['country_id'] = $country_id;

    //for logs file
    $logentry.="\n" . $accessLogData['logText'];
    $logentry.= "user_id:" . $userId . "\n";
    $logentry.= "access_type:" . $accessType . "\n";
    $logentry.= "denied_type:" . $deniedType . "\n";
    $logentry.= "used_as_ip_addr:" . $ip . "\n";
    $logentry.= "country_id:" . $country_id . "\n";

    $sql = 'INSERT INTO access_denied_log(access_type,denied_type,used_as_ip_addr,' . $accessLogData['sqlFields'] . 'user_id,country_id)' .
            ' VALUES(:access_type,:denied_type,:used_as_ip_addr,' . $accessLogData['sqlValues'] . ':user_id,:country_id)';
    $db->query($sql, $data);

    //log to file
    writelog($logentry, $lgname);
}

/*
 * log error
 */

function accessLog($logentry, $accessType = '', $ip = '', $country_id = 0, $lgname = ACCESS_LOGFILE) {

    $accessLogData = getAccessLogData();
    //inset to database
    $db = getDbClassInstance();

    $userId = getUserId();

    //for db logs
    $data = $accessLogData['sqlData'];
    $data['user_id'] = $userId;
    $data['access_type'] = $accessType;
    $data['used_as_ip_addr'] = $ip;
    $data['country_id'] = $country_id;

    //for logs file
    $logentry.="\n" . $accessLogData['logText'];
    $logentry.= "user_id:" . $userId . "\n";
    $logentry.= "access_type:" . $accessType . "\n";
    $logentry.= "used_as_ip_addr:" . $ip . "\n";
    $logentry.= "country_id:" . $country_id . "\n";

    $sql = 'INSERT INTO access_log(access_type,used_as_ip_addr,' . $accessLogData['sqlFields'] . 'user_id,country_id)' .
            ' VALUES(:access_type,:used_as_ip_addr,' . $accessLogData['sqlValues'] . ':user_id,:country_id)';
    $db->query($sql, $data);

    //log to file
    writelog($logentry, $lgname);
}

/*
 * write to log file
 */

function writelog($logentry, $lgname = LOGFILE) {
    //tempary fix for prod to get rid of errors
    if (APPMODE == 'prod')
        return;

    if (is_array($logentry))
        $logentry = print_r($logentry, true);

    $logfile = @fopen($lgname, "a+");
    if (!$logfile) {
        echo ("\n\n ERROR: Failed to open $lgname\n\n");
    } else {
        if (php_sapi_name() == "cli")
            fwrite($logfile, date("Y-m-d h:i:sA") . " -  cli " . " - page:{$_SERVER['PHP_SELF']} - $logentry\n");
        else {
            $REMOTE_ADDR = '';
            if (!empty($_SERVER['REMOTE_ADDR']))
                $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
            fwrite($logfile, date("Y-m-d h:i:sA") . " - " . $REMOTE_ADDR . " - page:{$_SERVER['PHP_SELF']} - $logentry\n");
        }
        fclose($logfile);
    }
}

/**
 * 
 * @return type
 */
function getClientIp() {
    $ip = '';

    if (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}

/*
 * log actions
 * Note: this is something we may show to user. So make sure we don't add content that user should not see
 * 
 * $action : login, create, delete .. etc
 * $type : activity, product, technique .. etc
 * if $logToFile is true writelog also will be called
 * $userId: if need can send user.id value
 */

function logAction($action, $type, $title = '', $description = '', $logToFile = true, $api = false, $userId = 0) {
    if (empty($userId))
        $userId = getUserId();

    $ip_address = getClientIp();

    if ($api) {
        //if $api=true passed it add word api from of log action name.
        $action = 'api ' . $action;
    }

    $db = getDbClassInstance();
    $data = array('user_id' => $userId, 'action' => $action, 'type' => $type,
        'title' => $title, 'description' => $description, 'ip_address' => $ip_address);
    $sql = 'INSERT INTO action_log(user_id,action,type,title,description,ip_address)' .
            ' VALUES(:user_id,:action,:type,:title,:description,:ip_address)';
    $db->query($sql, $data);

    if ($logToFile) {
        writelog('action: ' . $action . ', type:' . $type . ', title: ' . $title . ', description: ' . $description);
    }
}

/**
 * return ajax result $res array
 * if jsonp requested return jsonp
 * else just array
 */
function returnAjaxResult($res) {
    //get token data from request
    $csrfInstance = 0;
    if (!empty($_REQUEST["csrf"])) {
        $csrfInstance = intval($_REQUEST["csrf"]['csrfInstance']);
    }
    //add csrf token
    $res['csrf'] = csrfToken($csrfInstance);

    if (isset($_REQUEST["callback"]) && $_REQUEST["callback"] != '') {
        //return jsonp response if jsonp set
        $response = $_REQUEST["callback"] . "(" . json_encode($res) . ")";
    } else
        $response = json_encode($res);
    die($response);
}

/**
 * return mysql date format string for given date string
 * if $tdate='' return current date
 * if set $time true return time portion also
 */
function mysqlDate($tdate = '', $time = false) {
    $tdate = trim($tdate);

    $timeFormat = '';
    if ($time)
        $timeFormat = ' H:i:s';

    if ($tdate == '')
        $date = date('Y-m-d' . $timeFormat);
    else
        $date = date('Y-m-d' . $timeFormat, strtotime($tdate));
    return $date;
}

/**
 * format given date string  and return with requested fromat
 * 
 * @param string $tdate date string , if empty return current date, date strung as described here http://php.net/manual/en/datetime.formats.date.php
 * @param string $format
 * @return type
 */
function formatDate($tdate = '', $format = 'M d, Y') {
    $tdate = trim($tdate);

    if ($tdate == '')
        $date = date($format);
    else
        $date = date($format, strtotime($tdate));
    return $date;
}

/**
 * $reload =false //indicate page load, reload by user, default not reload
 * $justSignIn indiate user just sign in
 */
function firtPage($reload = false, $justSignIn = false) {
    /*
     * lets check what we should load just after logged in
     */
    $cnt = $page = $footJs = '';

    if (ADMIN_ENABLED && isAdmin()) {
        //if admin logged in we load admin page by default
        require_once "logic/admin.class.php";
        $admin = new admin(getUserId());
        if (!$reload && $justSignIn)
            $cnt = $admin->adminPage(true, $reload);
        else {
            require_once "ui/ui.landpage.class.php";
            $uiLandPage = new uiLandPage();
            $cntAdm = $admin->adminPage(true, $reload);
            //get full page content for admin
            $cnt = $uiLandPage->getLandingPageUi($cntAdm, '');
        }

        $page = 'admin';
    } else if (!$reload && $justSignIn) {
        //so this is showing content just after first user logged in, so return school content to add shool section 
        $userId = getUserId();
        require_once "logic/profile.class.php";
        $profile = new profile($userId);
        $cnt = $profile->schoolSection(true);
        $page = 'school';
    } else {
        //load home page
        $cnt = homePageContent();
        //for home page set $page=''
        $page = '';
        if (is_logged()) {
            //show school section
            $footJs = ' $( document ).ready(function() {
                        scrollViewTo(\'#school-section\');
                       });';
        }
    }
    return array('cnt' => $cnt, 'page' => $page, 'footJs' => $footJs);
}

/*
 * cehck recaptchar is valid
 */

function isValidRecaptcha($input) {
    $state = false;
    if (SIGNUP_CAPTCHAR_VERSION == "1") {
        require_once "recaptchalib.php";
        $resp = recaptcha_check_answer(RECAPTCHA_PRIVKEY, $_SERVER["REMOTE_ADDR"], $input["recaptcha_challenge_field"], $input["recaptcha_response_field"]);
        $state = $resp->is_valid;
    } else {
        $restUri = 'https://www.google.com/recaptcha/api/siteverify';
        $postFields = array(
            'secret' => RECAPTCHA_PRIVKEY,
            'response' => $input['recaptcha_response_field']
        );
        $cres = curlRequest($restUri, $postFields, 15, SITE_NAME, true);
        if ($cres['status'] === true) {
            $cresObj = json_decode($cres['response']);
            if ($cresObj->success == true) {
                $state = true;
            }
        }
    }

    if (!$state)
        return false;
    else
        return true;
}

/*
 * this will trun file name to store image and this is copy of funciton in upload.php
 */

function get_file_name($name, $type = null, $index = null, $content_range = null) {
    require_once 'logic/base/token.class.php';
    $mytoken = token::get();

    return($mytoken . '.' . strtolower(pathinfo($name, PATHINFO_EXTENSION)));
}

/*
 * return new csrf token and set it session also
 * $csrfInstance: to indicate browser tab or window to ignore conflict, 0 means we had add new instance
 */

function csrfToken($csrfInstance = 0) {
    $newToken = true;

    if (!empty($_SESSION['csrfToken'])) {
        $keyArray = array_keys($_SESSION['csrfToken']);
        if (empty($csrfInstance)) {
            //we have to add new instance
            $csrfInstance = max($keyArray) + 1;
        } elseif (in_array($csrfInstance, $keyArray)) {
            //we have intance no need new tocken
            $newToken = false;
        } else {
            //we have to add new instance
            $csrfInstance = max($keyArray) + 1;
        }
    } else {
        //no instnace of tockens
        if (empty($csrfInstance))
            $csrfInstance = 1;
    }

    if ($newToken) {
        require_once 'logic/base/token.class.php';
        $csrfToken = token::get();
        //store to session
        $_SESSION['csrfToken'][$csrfInstance] = $csrfToken;
    } else {
        //get exisiting token
        $csrfToken = $_SESSION['csrfToken'][$csrfInstance];
    }

    return array('csrfToken' => $csrfToken, 'csrfInstance' => $csrfInstance);
}

/*
 * check to make sure csrf security is ok when ajax reqest done
 */

function checkCsrf() {
    if (!empty($_REQUEST["csrf"]) && !empty($_REQUEST["csrf"]['csrfInstance']) && !empty($_REQUEST["csrf"]['csrfToken'])) {
        $csrfInstance = intval($_REQUEST["csrf"]['csrfInstance']);
        $csrfToken = $_REQUEST["csrf"]['csrfToken'];

        if (!empty($_SESSION['csrfToken']) && !empty($_SESSION['csrfToken'][$csrfInstance]) && $_SESSION['csrfToken'][$csrfInstance] == $csrfToken) {
            //we are good return
            return;
        }
    }

    //log out user
    require_once "logic/user.class.php";
    $user = new user();
    $user->unsetSignedInUser();
    logAction('csrf', 'security', 'csrf security', 'common.php , csrf security issue found', true);

    //get home page content
    $centent = ''; //homePageContent();
    //get list of lanuages 
    $langs = languageList();

    require_once "ui/ui.common.class.php";
    $uiCommon = new uiCommon();

    $res['status'] = false;
    $res['error'] = 'Security risk noticed and you have been signed out to protect your data!';
    $res['flogout'] = true; //indicate force logged out
    $res['snav'] = $uiCommon->sideNavOptions($langs);
    $res['contactUs'] = $uiCommon->contactSupportModelMrkup('', '');
    $res['cnt'] = $centent; //content to replace center part

    returnAjaxResult($res);
}

/*
 *  country code are ISO ,region code (FIPS or ISO)
 */

function determineLocation($_ip) {
    include_once( BASE . '/lib/geo/geoipcity.php' );
    include_once( BASE . '/lib/geo/geoipregionvars.php' );
    $gi = geoip_open(BASE . '/lib/geo/GeoLiteCity.dat', GEOIP_STANDARD);

    $record = geoip_record_by_addr($gi, $_ip);

    if (is_object($record)) {
        $code = '';
        if (!empty($record->country_code))
            $code = $record->country_code;

        $name = '';
        if (!empty($record->country_name))
            $name = $record->country_name;

        $city = '';
        if (!empty($record->city))
            $city = $record->city;

        $region = '';
        if (!empty($record->region)) {
            $region = $record->region;
            if (!empty($record->country_code))
                $region .= " " . $GEOIP_REGION_NAME[$record->country_code][$record->region];
        }
//        $lat = $record->latitude;
//        $long = $record->longitude;
//        $continent = $record->continent_code . "\n";
    } else {
        $code = 'XX';
        $region = 'XX';
        $name = 'Unknown';
        $city = 'Unknown';
    }
    geoip_close($gi);
    return array('country_code' => $code, 'region' => $region, 'country_name' => $name, 'city' => $city);
}

/*
 * check counry has permssion to access site
 * make sure country has Access Permission to this site
 * if access not allowed stop 
 * log access and denied information
 * 
 * $accessType : page name route passed
 * 
 * return $countryId
 */

function hasCountryAccessPermission($client_ip = '', $accessType = '') {
    //get location data
    $location = determineLocation($client_ip);
    //check country allowed
    $countryId = 0;
    if (!empty($location['country_code']) && $location['country_code'] != 'XX') {
        $countryId = getCountryId($location['country_code']);
        if (isCountryBlocked($countryId)) {
            //country blocked , so we log access deined and stop executing
            accessDeniedLog('Access denied for country=' . $countryId, $accessType, 'country', $client_ip, $countryId);
            return false;
        }
    } else {
        //could not determinde country
        $logentry = "Could not determined country for ip address : " . $client_ip;
        errorLog($logentry);
    }
    return $countryId;
}

/*
 * check ip and counry has permssion to access site
 * make sure ip address and country has Access Permission to this site
 * if access not allowed stop 
 * log access and denied information
 * 
 * $accessType : page name route passed
 */

function hasIpCountryAccessPermission($accessType = '') {
    //get client ip address
    $client_ip = getClientIp();
    //check for coutnry access permssion
    $countryId = hasCountryAccessPermission($client_ip, $accessType);

    if ($countryId === false) {
        return array('status' => false, 'error' => langText('error_access_denied_country'));
    }
    //check ip allowed
    $ipPermssion = hasIpAccessPermission($client_ip, $accessType);
    if ($ipPermssion === false) {
        return array('status' => false, 'error' => langText('error_access_denied_ip'));
    }
    //update granted log
    $accessGrantLog = 'Access Granted';
    if (!empty($countryId))
        $accessGrantLog.=' coutnry = ' . $countryId;
    if (!empty($client_ip))
        $accessGrantLog.=' ip = ' . $client_ip;
    accessLog($accessGrantLog, $accessType, $client_ip, $countryId);

    return array('status' => true);
}

/*
 * return true if coutnry blocked record exist in blocked_countries
 * 
 * $countriesId blocked_countries.countries_id
 * $ingoreId blocked_countries.id to ignore when do checking
 */

function isCountryBlocked($countriesId, $ingoreId = 0) {
    $db = getDbClassInstance();
    $iData = array("countries_id" => $countriesId);
    //if ignore id provided ignore id from check
    $whereAppend = '';
    if (!empty($ingoreId)) {
        $iData['id'] = $ingoreId;
        $whereAppend = ' and id!=:id';
    }
    $sql = 'select count(*) from blocked_countries where countries_id =:countries_id' . $whereAppend;
    $count = $db->column($sql, $iData);

    if (intval($count[0]) > 0)
        return true;
    else
        return false;
}

/*
 * return countries.id by iso2 counry code
 */

function getCountryId($iso2Code) {
    $db = getDbClassInstance();
    $sql = 'select * from countries where iso2=:iso2';
    $country = $db->row($sql, array('iso2' => $iso2Code));
    if ($db->RowCount() >= 1) {
        //we have found a country
        return $country['id'];
    } else
        return 0;
}

/*
 * return list of coutries order by country names
 */

function getCountries() {
    $db = getDbClassInstance();
    $sql = 'select * from countries order by name_en asc';
    $countries = $db->query($sql, array());
    return $countries;
}

/*
 * validate ip address
 * 
 * return true if valid else false
 */

function isValidateIP($ip) {
    if (filter_var($ip, FILTER_VALIDATE_IP) === false)
        return false;
    else
        return true;
}

/*
 * check ip has permssion to access site
 * make sure ip has Access Permission to this site
 * if access not allowed stop 
 * log access and denied information
 * 
 * $accessType : page name route passed
 * 
 * return $countryId
 */

function hasIpAccessPermission($client_ip = '', $accessType = '') {
    $denied = false;
    if (isIpBlocked($client_ip)) {
        //id address is blocked
        $denied = true;
    } else if (isIpBlockedByIpRange($client_ip)) {
        //ip blocked by ip range filter
        $denied = true;
    } elseif (isIpBlockedBySubnetMask($client_ip)) {
        //ip bliocked by subnet mask filter
        $denied = true;
    }

    if ($denied) {
        //ip blocked , so we log access deined and stop executing
        accessDeniedLog('Access denied for ip=' . $client_ip, $accessType, 'country', $client_ip);
        return false;
    }

    return true;
}

/*
 * return true if ip blocked record exist in blocked_ips
 * 
 * $ip blocked_ips.ip_address
 */

function isIpBlocked($ip) {
    $db = getDbClassInstance();
    $iData = array("ip_address" => $ip);
    $sql = 'select count(*) from blocked_ips where ip_address =:ip_address and type="ip"';
    $count = $db->column($sql, $iData);

    if (intval($count[0]) > 0)
        return true;
    else
        return false;
}

/*
 * return true if ip blocked by ip range
 * 
 * $ip blocked_ips.ip_address
 * 
 * note: this only works for ipv4
 * Tried to workout for ipv6, get overflow issue and can’t store that much big numeric value in php
 */

function isIpBlockedByIpRange($ip) {
    $db = getDbClassInstance();
    $iData = array("ip_range_a" => $ip, 'ip_range_b' => $ip);
    $sql = 'select count(*) from blocked_ips where type="iprange"'
            . ' and IFNULL(INET_ATON(ip_range_from),0)<=IFNULL(INET_ATON(:ip_range_a),-1)'
            . ' and IFNULL(INET_ATON(ip_range_to),0)>=IFNULL(INET_ATON(:ip_range_b),-1)';
    $count = $db->column($sql, $iData);
    if (intval($count[0]) > 0)
        return true;
    else
        return false;
}

/*
 * return true if ip blocked by subnet
 * 
 * $ip blocked_ips.ip_address
 */

function isIpBlockedBySubnetMask($ip) {
    //get list of subnet mask blocked
    $db = getDbClassInstance();
    $sql = 'SELECT subnet_mask FROM blocked_ips where type="subnet" order by last_updated desc';
    $subnetMasks = $db->query($sql, array());
    if ($db->RowCount() > 0) {
        //we have found subnet masks blokced
        foreach ($subnetMasks as $mask) {
            if (cidr_match($ip, $mask['subnet_mask'])) {
                return true;
            }
        }
    }
    return false;
}

/*
 * check if ipv4 address mathced subnet masks cidr
 * use ip2long() to convert the IPs and the subnet range into long integers
 * convert the /xx into a subnet mask
 * do a bitwise 'and' (i.e. ip & mask)' and check that that 'result = subnet'
 * 
 * http://stackoverflow.com/questions/594112/matching-an-ip-to-a-cidr-mask-in-php5
 * 
 */

function cidr_match($ip, $range) {
    list($subnet, $bits) = explode('/', $range);
    $ip = ip2long($ip);
    $subnet = ip2long($subnet);
    $mask = -1 << (32 - $bits);
    $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
    return ($ip & $mask) == $subnet;
}

/*
 * convert ipv6 adn ipv4 to decimal value 
 * 
 * number = (aaa*16777216) + (bbb*65536) + (ccc*256) + ddd 
 * Eranga
 */

function ipToDecimal($ip) {
    $decimal = 0;
    //first detrminde ip is v4 or v6
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
        $decimal = ipV4toDecimal($ip);
    } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
        //so far no way found for this
        //10/9/2014        
    }
    return $decimal;
}

/*
 * convert ipv4 address to base 2 represenation string
 */

function ipv4ToBase2Str($ip) {
    $ipv4arr = explode('.', $ip);
    $base2Str = '';
    foreach ($ipv4arr as $i => $part) {
        if ($i >= 4)
            break;
        if (empty($part))
            $part = 0;
        //convert decimal value to base2
        $base2part = base_convert($part, 10, 2);
        //each part is 8 bit and pad 0 s to front if need
        $base2part = str_pad($base2part, 8, "0", STR_PAD_LEFT);
        $base2Str .=$base2part;
    }
    return $base2Str;
}

/*
 * convert ipv6 address to base 2 represenation string
 */

function ipv6ToBase2Str($ip) {
    $ipv4arr = explode(':', $ip);
    $base2Str = '';
    foreach ($ipv4arr as $i => $part) {
        if ($i >= 8)
            break;
        if (empty($part))
            $part = 0;
        //convert decimal value to base2
        $base2part = base_convert($part, 16, 2);
        //each part is 8 bit and pad 0 s to front if need
        $base2part = str_pad($base2part, 16, "0", STR_PAD_LEFT);
        $base2Str .=$base2part;
    }
    return $base2Str;
}

/*
  convert  ipv4 to decimal value
 * 
 * http://infowave.gr/tutorials/iptonumber.php
 * number = (aaa*256^3) + (bbb*256^2) + (ccc*256^1) + (ddd*256^0)
 * number = (aaa*16777216) + (bbb*65536) + (ccc*256) + ddd
 * 
 * this works fine, Eranga 
 */

function ipV4toDecimal($ip) {
    $decimal = 0;
    $muls = array(16777216, 65536, 256, 1);
    //first get for parts
    $ipv4arr = explode('.', $ip);

    foreach ($ipv4arr as $i => $part) {
        if ($i >= 4)
            break;
        $decimal+=floatval($part) * floatval($muls[$i]);
    }
    return floatval($decimal);
}

/**
 * check if string is valid email
 * 
 * Max email length allowed in db is 75
 * 
 * @param email $email email address
 */
function isValidEmailFormat($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        return false;
    else
        return true;
}

/**
 * check if email is used as active email (user.email) for any account
 * if used return (int)user.id else 0
 */
function checkEmailUsed($email) {
    //we do case insenstive search
    $email = strtolower(trim($email));
    $db = getDbClassInstance();
    $users = $db->query("select id from user where LOWER(email) = :email", array("email" => $email));
    if ($db->RowCount() > 0) {
        return intval($users[0]['id']);
    } else {
        return 0;
    }
}

/**
 * check is request is ajax and if not redirect to home page
 * 
 * @param bool $onlyAllowAjax if this is true and request is not ajax will redirect to home page
 */
function checkIsAjax($onlyAllowAjax = true) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        return true;
    } else {
        if ($onlyAllowAjax) {
            //if this is true and request is not ajax will redirect to home page
            redirect(SITE_URL);
        } else {
            return false;
        }
    }
}

/**
 * redirect page
 * http://stackoverflow.com/questions/768431/how-to-make-a-redirect-in-php
 * 
 * @param type $url
 * @param type $statusCode 301|302|303 ..
 */
function redirect($url = SITE_URL, $statusCode = 301) {
    header('Location: ' . $url, true, $statusCode);
    die();
}

/**
 * return image width and height in px
 * 
 * @param string $filename filename with  path
 */
function getImageWidthHeight($filename) {
    //@is for not give warning error in safari for mutiple uploads
    $sizes = @getimagesize($filename);

    return array('width' => $sizes[0], 'height' => $sizes[1]);
}

/**
 * return file size in bytes
 * 
 * @param string $filename filename with  path
 */
function getFileSize($filename) {
    $size = filesize($filename);
    //Fix for overflowing signed 32 bit integers,
    // works for sizes up to 2^32-1 bytes (4 GiB - 1):
    if ($size < 0) {
        $size += 2.0 * (PHP_INT_MAX + 1);
    }

    return $size;
}

/**
 * validate max length of given string for given length
 * 
 * @param string $text text to validate
 * @param int $maxLength max length of text allowed
 * @return bool true is valid length and false if length exceed allowed length
 */
function validateMaxLength($text, $maxLength = 50) {
    if (strlen(trim($text)) > $maxLength)
        return false;
    else
        return true;
}

/**
 * Convert post fields data array to url type data with url encoding.
 * 
 * @param array $postFields Array of data need to post index filed name.
 *                          ex : array('lname' => "First Name",'fname' => "Last Name");
 * @param boolean $postFieldsUrlEncoded If false we do url encode final string before return. 
 *                                      If true we assume data is already url encoded.
 *
 * @return string post fields data converted url type for CURLOPT_POSTFIELDS
 */
function curlFieldsString($postFields = array(), $postFieldsUrlEncoded = false) {
    $fieldsString = '';
    if (!empty($postFields)) {
        foreach ($postFields as $key => $value) {
            $fieldsString .= $key . '=' . $value . '&';
        }
        $fieldsString = rtrim($fieldsString, '&');
        //if post fields array data is not url encoded we do urlencode here
        if (!$postFieldsUrlEncoded)
            $fieldsString = urlencode($fieldsString);
    }
    return $fieldsString;
}

/**
 * Do curl request and return resulting string 
 * 
 * @param string $url The URL to fetch
 * @param array $postFields Array of data need to post index filed name.
 *                          ex : array('lname' => "First Name",'fname' => "Last Name");
 * @param int $timeOut The maximum number of seconds to allow cURL functions to execute.
 * @param string $userAgent User agent name
 * @param boolean $postFieldsUrlEncoded If false we do url encode final string before return. 
 *                                      If true we assume data is already url encoded.
 * @return string result string
 */
function curlRequest($url, $postFields = array(), $timeOut = 15, $userAgent = SITE_NAME, $postFieldsUrlEncoded = false) {

    //post fields data convert url type for CURLOPT_POSTFIELDS
    $fieldsString = curlFieldsString($postFields, $postFieldsUrlEncoded);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);

    $response = curl_exec($ch);
    $resultStatus = curl_getinfo($ch);

    // echo htmlentities($response) . '<br><br>';
    $res = array('status' => true, 'response' => '');
    if ($resultStatus['http_code'] == 200) {
        //echo htmlentities($response);
        $res = array('status' => true, 'response' => $response);
    } else {
        //echo 'Call Failed: <br />' . print_r($resultStatus, true);
        $res = array('status' => false, 'response' => print_r($resultStatus, true));
    }

    return $res;
}

/////// for sortable tables and paginations
/**
 * fieldsTableColumnHeadrsThGlyphiconType
 */
function fieldsTableColumnHeadrsThGlyphiconType($type) {
    $glyphiconType = '';
    switch ($type) {
        case 'text':
            $glyphiconType = 'glyphicon-sort-by-alphabet';
            break;
        case 'number':
            $glyphiconType = 'glyphicon-sort-by-order';
            break;
        case 'other':
        default :
            $glyphiconType = 'glyphicon-sort-by-attributes';
            break;
    }
    return $glyphiconType;
}

/**
 * fieldsTableColumnHeadrsThGlyphiconSpan
 * $alt; if true append [-alt] at the end of $glyphiconType name
 */
function fieldsTableColumnHeadrsThGlyphiconSpan($glyphiconType, $alt = false) {
    if ($alt)
        $glyphiconType.='-alt';
    $fieldThIcon = '<span class="glyphicon ' . $glyphiconType . '"></span>';
    return $fieldThIcon;
}

/**
 * return Fields Table Column Headrs th ui html
 */
function fieldsTableColumnHeadrsTh($idPrefix, $field, $onclcik, $fieldName, $fieldThIcon) {
    $th = '<th><a class="' . $idPrefix . 'th_' . $field . '" onclick="' . $onclcik . '" href="javascript:void(0)">' . $fieldName . '' . $fieldThIcon . '</a></th>';
    return $th;
}

/**
 * return table coolumn header with ordre by scritpa and css
 * 
 * $orderByField; field name to order by
 * $orderDir: asc|desc
 * 
 * $fields: array of fields indexed by field id and and field type as value
 * filed array field type can be text|number|other 
 * filed array field type can be text|number|other 
 * $fields = array('ad_location' => 'text',
  'ad_title' => 'text',
  'ad_type' => 'text',
  'last_updated' => 'other'
  );
 * 
 * $fieldNames : field names index by field id
  $fieldNames=array(
  'ad_location' => 'Ad Location',
  'ad_type' => 'Ad Type',
  'ad_title' => 'Ad Title',
  'last_updated' => 'Last Updated'
  );
 * 
 * $onclickS : start of onclick th functions str
 * $onclickE : end of onclick th function str
 * ex : 
 * //except order by bject string {} other parts of onclick funcitons
  $onclickS = 'ads(\'sort\',0,';
  $onclickE = ');';
 * 
 * $idPrefix : prefeix to ids
 * 
 */
function getFieldsTableColumnHeadrs($fields, $fieldNames, $orderByField, $orderDir, $onclickS, $onclickE, $idPrefix = '') {

    $thHtml = '';
    foreach ($fields as $field => $type) {
        $fieldThIcon = '';
        $fieldThDir = 'asc';
        $fieldName = $fieldNames[$field];
        if ($orderByField == $field) {
            $glyphiconType = fieldsTableColumnHeadrsThGlyphiconType($type);
            if ($orderDir == 'asc') {
                //if now order by asc next we have do desc
                $fieldThDir = 'desc';
                $fieldThIcon = fieldsTableColumnHeadrsThGlyphiconSpan($glyphiconType, false);
            } else {
                $fieldThDir = 'asc';
                $fieldThIcon = fieldsTableColumnHeadrsThGlyphiconSpan($glyphiconType, true);
            }
        }
        //set th for field            
        $onclick = $onclickS . '{orderBy:\'' . $field . '\',orderDir:\'' . $fieldThDir . '\'}' . $onclickE;
        $th = fieldsTableColumnHeadrsTh($idPrefix, $field, $onclick, $fieldName, $fieldThIcon);
        $thHtml.=$th;
    }

    return $thHtml;
}

/**
 * return order by from options sent via ajax
 * 
 * $options : wihch has orderBy, orderDir indexes if need order by, these can be empty
 * $defultOrderBy : default order by field
 * $defultOrderDir : asc|desc default order by direction 
 * $fields: array of fields indexed by field id and and field type as value
 * filed array field type can be text|number|other 
 * $fields = array('ad_location' => 'text',
  'ad_title' => 'text',
  'ad_type' => 'text',
  'last_updated' => 'other'
  );
 * 
 * 
 */
function getOrderByFromOptions($options, $fields, $defultOrderBy = '', $defultOrderDir = '') {

    $orderByField = '';
    $orderDir = '';
    //set defult order by direction
    if (trim($defultOrderDir) != '') {
        if (in_array($defultOrderDir, array('asc', 'desc')))
            $defultOrderDir = 'desc';
        else
            $defultOrderDir = 'asc';
    }

    //set defult order by and direction
    if (trim($defultOrderBy) != '') {
        $orderByField = $defultOrderBy;
        //now since default order by field is set we can set default orderby direction
        $orderDir = $defultOrderDir;
    }

    if (!empty($options['orderBy']) && in_array($options['orderBy'], array_keys($fields)))
        $orderByField = $options['orderBy'];
    if (!empty($options['orderDir'])) {
        if ($options['orderDir'] == 'asc')
            $orderDir = 'asc';
        else
            $orderDir = 'desc';
    }
    //if order by fields and direction not set set deault order by direction 
    if ($orderByField != '' && $orderDir == '')
        $orderDir = $defultOrderDir;


    return array('orderByField' => $orderByField, 'orderDir' => $orderDir);
}

/**
 * return limit sql 
 * 
 * @param type $page
 * @param type $perPage
 * @return string
 */
function getLimitSql($page, $perPage) {
    //set limit if there are more data than $startLimit
    $startLimit = 0;
    if ($page > 0) {
        $startLimit = (($page - 1) * $perPage);
    }

    $limit = ' LIMIT ' . $startLimit . ',' . $perPage;
    return $limit;
}

/**
 * return pagination related infor
 * 
 * @param string $countSql count sql query to count all data
 * @param array $data
 * @param type $perPage
 * @param type $page
 * @param type $leftJoins
 * @return type
 */
function getPageInfor($countSql, $data, $perPage, $page) {
    $db = getDbClassInstance();
    $countRs = $db->column($countSql, $data);
    $toatlCount = intval($countRs[0]);
    $pageCount = 1;
    $nextPage = 1;
    if ($toatlCount > 0) {
        $pageCount = intval(ceil($toatlCount / $perPage));
        //set max page is last page
        if ($page > $pageCount) {
            $page = $pageCount;
            $nextPage = $page;
        } else if ($page < $pageCount)
            $nextPage = $page + 1;
        else
            $nextPage = $page;
    }

    return array('pageCount' => $pageCount, 'nextPage' => $nextPage, 'page' => $page);
}

/**
 * return option json string with new page
 * @param type $options
 * @param type $page
 */
function paginationOptionJsonString($options=array(),$page=1){
    //'{page:' .  . '}'
    //'{page:' . ($pageEnd + 1) . '}'
    //update new page value
    $options['page']=$page;
    $jsonStr= str_replace('"',"'",json_encode($options));
    return $jsonStr;
}

/**
 * return pagination ui
 * 
 * @param type $page
 * @param type $pageCount
 * @param type $nextPage
 * @param string $uniquePrefix prfix word so we can use elements unique id when need 
 * @param bool $perPageShow show per page drop down
 * @param string $wrapColClass classes to place in wrap column of pagination ex: text-center
 * @return string
 */
function paginationUi($page, $pageCount, $perPage, $onclickS, $onclickE, $uniquePrefix = '',$perPageShow=true,$options=array(),$wrapColClass="") {

    $finalHtml = '';

    //if we have paginations
    if ($pageCount > 1) {

        $disablBack = $activePage = $disableNext = '';


        $pageStart = 1;
        $pageEnd = $pageCount;
        if ($pageCount > 6) {
            //we only show max 7 page selecters at once
            $pageStart = $page - 3;
            if ($pageStart < 1)
                $pageStart = 1;

            $pageEnd = $pageStart + 6;
            if ($pageEnd > $pageCount)
                $pageEnd = $pageCount;
        }

        if ($pageStart == 1)
            $disablBack = 'disabled';
        if ($pageCount == $pageEnd)
            $disableNext = 'disabled';

        $pageLi = '';
        for ($x = $pageStart; $x <= $pageEnd; $x++) {
            $activeClass = '';
            if ($x == $page)
                $activeClass = 'active';

            $onclick = $onclickS . paginationOptionJsonString($options,$x) . $onclickE;

            $pageLi.='<li class="' . $activeClass . '"><a href="javascript:void(0)" onclick="' . $onclick . '">' . $x . '</a></li>';
        }

        $onclickFirstPage = $onclickBack = '';
        if ($disablBack == '') {
            $onclickBack = $onclickS . paginationOptionJsonString($options,($pageStart - 1)) . $onclickE;
            $onclickFirstPage = $onclickS . paginationOptionJsonString($options,1) . $onclickE;
        }

        $onclickNext = '';
        if ($disableNext == '')
            $onclickNext = $onclickS .paginationOptionJsonString($options,($pageEnd + 1))  . $onclickE;

        $onclickLastPage = $onclickS . paginationOptionJsonString($options,$pageCount) . $onclickE;

        $onclickPerPageChange = $onclickS . paginationOptionJsonString($options,1). $onclickE;

        $perPageLi = '';
        if (PER_PAGE && $perPageShow) {
            //if per page option enabled
            $perPageDrop = perPageDropDown($perPage, $onclickPerPageChange, $uniquePrefix);
            $perPageLi = '<li class="">' . $perPageDrop . '<input type="hidden" id="' . $uniquePrefix . 'PerpageValue" value=""></li>';
        }

        $pageCountLi = '';
        if ($page < $pageCount)
            $pageCountLi = '<li class="' . $disableNext . '"><a href="javascript:void(0)" onclick="' . $onclickLastPage . '">»»</a></li>';

//        $finalHtml .= '<div class="row">';
//        $finalHtml .= ' <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">';
//        $finalHtml .= '     <span class="badge">Showing Page ' . $page . ' Out Of <a href="javascript:void(0)" onclick="' . $onclickLastPage . '">' . $pageCount . '</a> Pages</span>';
//        $finalHtml .= ' </div>';
//        $finalHtml .= '</div>';

        $finalHtml .= '<div class="row">';
        $finalHtml .= ' <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 '.$wrapColClass.'">';
        $finalHtml .= '     <ul class="pagination pagination-sm">
                                <li class="' . $disablBack . '"><a href="javascript:void(0)" onclick="' . $onclickFirstPage . '">««</a></li>
                                <li class="' . $disablBack . '"><a href="javascript:void(0)" onclick="' . $onclickBack . '">«</a></li>
                                ' . $pageLi . '
                                <li class="' . $disableNext . '"><a href="javascript:void(0)" onclick="' . $onclickNext . '">»</a></li>
                                ' . $pageCountLi . '
                                ' . $perPageLi . '
                            </ul>';
        $finalHtml .= ' </div>';
        $finalHtml .= '</div>';
    }

    return $finalHtml;
}

/**
 * return per page drop down
 * 
 * @param type $perPage
 * @param type $onclickPerPageChange
 *  @param string $uniquePrefix prfix word so we can use elements unique id when need  
 */
function perPageDropDown($perPage, $onclickPerPageChange, $uniquePrefix = '') {
    $drop = '';
    $drop .='<form class="form-inline pull-left">
                <div class="form-group-sm">
                    <label for="' . $uniquePrefix . 'PerpageSelect" class="control-label">&nbsp;' . langText('per_page') . '&nbsp;</label>';

    $drop .= '      <select class="form-control" id="' . $uniquePrefix . 'PerpageSelect" name="' . $uniquePrefix . 'PerpageSelect" onchange="$(\'#' . $uniquePrefix . 'PerpageValue\').val($(\'#' . $uniquePrefix . 'PerpageSelect\').val());' . $onclickPerPageChange . '">';
    foreach ($GLOBALS['perPageOptions'] as $value) {
        $selected = '';
        if ($value == $perPage)
            $selected = 'selected="selected"';

        $drop.='        <option value="' . $value . '" ' . $selected . '>' . $value . '</option>';
    }
    $drop.='        </select>';
    $drop.='    </div>
            </form>';
    return $drop;
}

/**
 * if per page count is not set in request ie: set to 0 or empty
 * we check if we have per page value in session 
 * 
 * @param int $perPage current value requested
 */
function getPerPage($perPage) {
    if (empty($perPage)) {
//        echo 'test'.$perPage.'<br>';
//        echo 'test'.$_SESSION['perPage'].'<br>';
        //per page value empty, ie: not sent with request
//        if (is_logged()) {
//      //  user logged in
        if (!empty($_SESSION['perPage'])) {
            //we have per page value in session 
            $perPage = $_SESSION['perPage'];
        } else {
            //we don't have per page value in seesion, so we set default value and also update session
            // form config ROWS_PER_PAGE
            $_SESSION['perPage'] = $perPage = ROWS_PER_PAGE;
        }
//        } else {
//            //user not logged in so return default value
//            $perPage = ROWS_PER_PAGE;
//        }
    } else if (!empty($perPage)) {
        //ok per page value is sent with request and we update value to session for later use
        $_SESSION['perPage'] = $perPage;
    }

    return $perPage;
}

/**
 * return db class intiated by reference
 */
function getDbClassInstance() {
    return new DB(DBCONN, DBUSER, DBPASS);
}

/**
 * decode encoded unique id and return integer id
 * 
 * @param string $str
 * @return int
 */
function decodeUrlUniqueId($str) {
    $uid = base64_decode($str);
    if (is_numeric($uid))
        $uid = intval($uid);
    else
        $uid = 0;

    return $uid;
}

/**
 * encode params and return encoded string
 * we don't url encode here
 * 
 * @param string $value
 * @return string
 */
function encodeUrlParamId($value) {
    $str = base64_encode($value);
    return $str;
}

/**
 * get unique id passed with paramter which the paramter is created using uniqueTextUrlParam
 * we use :$: as sperator and we have removed if we had this from $text so no issue will happen
 * 
 * @param type $text
 * @return int unique id and if not found 0
 */
function getUniqueIdFromParam($text) {
    $uid = 0;
    //first we do url decode since we have done url encode for whole text, url was encoded twice(with json ecode) so decode twice
    $text = urldecode(urldecode($text));

    //get part that had id
    $encodedStrArr = array();
    preg_match('/\:\$\:.+$/i', $text, $encodedStrArr);

    if (!empty($encodedStrArr) && count($encodedStrArr) == 1) {
        $b64encodedStr = preg_replace('/^\:\$\:/i', '', $encodedStrArr[0]);
        $uid = decodeUrlUniqueId($b64encodedStr);
    }
    return $uid;
}

/**
 * getUrlParam
 * 
 * @param string $text text need to show in params (for SEO)
 * @param array $params params array to append to $text
 * @param type $maxTextLenth
 * @param bool $urlEncode if true we do url encode returned value
 * @param bool $encodeParams if true we do encode params with base 64 
 * @param string $uidSeperator params seperator
 * @return string
 */
function getUrlParam($text, $params = array(), $maxTextLenth = 25, $urlEncode = false, $encodeParams = false, $uidSeperator = '$') {

    if (!class_exists('uiCommon'))
        require_once "ui/ui.common.class.php";
    $uiCommon = new uiCommon();
    //$text can be empty
    if (!empty($text)) {
        $text = trim($uiCommon->formatText($uiCommon->stripNewLines($text), $maxTextLenth, 1, $maxTextLenth));
        //for XSS and make sure UTF8 and lower case
        $text = strtolower($uiCommon->et($text));
        //remove if we we have ... at the end and replace all spaces and _ with -, $ to - 
        $text = preg_replace(array('/\.+$/i', '/\s+/', '/_+/', '/\$/'), array('', '-', '-', '-'), $text);
    }
    $paramText = '';
    if (!empty($params)) {
        foreach ($params as $param) {
            if ($encodeParams)
                $param = encodeUrlParamId($param);
            $paramText.=$uidSeperator . $param;
        }
    }

    $fullParam = $text . $paramText;
    if ($urlEncode)
        $fullParam = urlencode($fullParam);

    return $fullParam;
}

/**
 * build url to set after page load which can be used as direct page url to load page
 * @param type $params
 * @return string
 */
function getDirectUrl($params = array()) {
    $url = '';
    if (!empty($params)) {
        $url.=SITE_URL;
        foreach ($params as $param) {
            $url.='/' . urlencode($param);
        }
    }
    return $url;
}

/**
 * return default lanuage table languages filename by lanuage code
 * 
 * @param bool $force if true get value form database and update session
 */
function getLanguageFileNameByCode($code, $force = false) {
    $langFileName = '';

    if (!empty($_SESSION['language_file_name_default']) && !$force) {
        $langFileName = $_SESSION['language_file_name_default'];
    } else {
        $db = getDbClassInstance();
        //get if language setting from languages table by code
        $sql = 'select * from languages where lower(code)=:code  AND enabled=1 limit 1';
        $langRow = $db->row($sql, array('code' => strtolower($code)));
        if ($db->RowCount() >= 1 && !empty($langRow['file_name'])) {
            //we have found a language
            $langFileName = $langRow['file_name'];
            setDefaultLanguageSession($langRow['code'], $langFileName, $langRow['recaptchar_code']);
        }
    }

    return $langFileName;
}

/**
 * set session variable for default lanuage settings 
 * 
 * @param type $langCode
 * @param type $langFileName
 * @param type $langRecaptcharCode
 */
function setDefaultLanguageSession($langCode, $langFileName, $langRecaptcharCode) {
    $_SESSION['language_code_default'] = $langCode;
    $_SESSION['language_file_name_default'] = $langFileName;
    $_SESSION['language_recaptchar_code_default'] = $langRecaptcharCode;
}

/**
 * return language code
 */
function getLanguageCode() {
    //lets check if we have default lanuage or lanuage settings in user settings
    $langCode = 'en';
    if (!is_logged()) {
        $langRes = getDefaultLanguage();
        $langCode = $langRes['code'];
    } else {
        //we can check user settings 
        require_once "logic/user.class.php";
        $user = new user();
        $langCode = $user->signedInUserLangCode();
    }

    return $langCode;
}

/**
 * return lanuage code (languages.code) by lanuage id (languages.id)
 * 
 * @param int lanuage id (languages.id)
 * @return string $code languages.code
 */
function getLanguageCodeById($langId) {
    $db = getDbClassInstance();
    //get if language setting from languages table by code
    $sql = 'select code from languages where id=:id  AND enabled=1 limit 1';
    $langRow = $db->row($sql, array('id' => $langId));
    $langCode = '';
    if (!empty($langRow))
        $langCode = $langRow['code'];
    return $langCode;
}

/**
 * return lanuage id (languages.id) by lanuage code (languages.code)
 * 
 * @param string $code languages.code
 * @return int lanuage id (languages.id)
 */
function getLanguageIdByCode($code) {
    $db = getDbClassInstance();
    //get if language setting from languages table by code
    $sql = 'select id from languages where lower(code)=:code  AND enabled=1 limit 1';
    $langRow = $db->row($sql, array('code' => strtolower($code)));
    $langId = 0;
    if (!empty($langRow))
        $langId = intval($langRow['id']);
    return $langId;
}

/**
 * return default lanuage table languages filename 
 */
function getDefaultLanguage() {
    $langFileName = $langCode = $langRecaptcharCode = '';
    if (!empty($_SESSION['language_file_name_default'])) {
        $langFileName = $_SESSION['language_file_name_default'];
        $langCode = $_SESSION['language_code_default'];
        $langRecaptcharCode = $_SESSION['language_recaptchar_code_default'];
    } else {
        $db = getDbClassInstance();
        //get if default language set from languages table, we only get first one if more than one set
        $sql = 'select * from languages where `default`=1  AND enabled=1  ORDER BY id asc  limit 1 ';

        $langRow = $db->row($sql, array());
        if ($db->RowCount() >= 1 && !empty($langRow['file_name'])) {
            //we have found a language
            $langFileName = $langRow['file_name'];
            $langCode = $langRow['code'];
            $langRecaptcharCode = $langRow['recaptchar_code'];

            setDefaultLanguageSession($langCode, $langFileName, $langRecaptcharCode);
        }
    }
    return array('fileName' => $langFileName, 'code' => $langCode, 'recaptcharCode' => $langRecaptcharCode);
}

/**
 * set language to global variable to use any plac 
 * http://www.iana.org/assignments/language-subtag-registry/language-subtag-registry
 * 
 * @param string $code lang code, if set will set lanuage mathced lang code only $code not empty
 * @param bool $force if true get value form database and update session
 */
function setLanguage($code = '', $force = false) {
    $langFileName = '';
    //if multi lanuage enabled
    if ($code == '') {
        //lets check if we have default lanuage or lanuage settings in user settings
        if (!is_logged()) {
            $langRes = getDefaultLanguage();
            $langFileName = $langRes['fileName'];
        } else {
            //we can check user settings 
            require_once "logic/user.class.php";
            $user = new user();
            $langFileName = $user->signedInUserLangFileName();
        }
    } else {
        //lets get lanuage filename code
        $langFileName = getLanguageFileNameByCode($code, $force);
    }

    //make sure we have set en lanuage if non found 
    if (empty($langFileName))
        $langFileName = 'en.class.php';

    //load language file
    require_once "lang/" . $langFileName;
    //set lanage instance to $GLOBALS
    $GLOBALS['lang'] = new Lang();
}

/**
 * return language array to use in js 
 * 
 * @param bool $jsonEncode if true return json encode language array to use in js
 * @return type
 */
function getLangJs($jsonEncode = true) {
    //if language not set we set langage and use it
    if (empty($GLOBALS['lang']))
        setLanguage();
    //get object
    $lang = $GLOBALS['lang'];

    return $lang->getJsLang($jsonEncode);
}

/**
 * return text from lang file
 * To use language file just need to call this function.
 * If language files is not added yet this will add and if already added will use what already have.
 * 
 * @param string $keyname name of language string required (ie: $lang[keyname])
 * @param array $params parameters to replace variabeles in string ex:array('COUNT'=>5,'NAME'=>'My Mame')
 */
function langText($keyname, $params = array()) {
    //if language not set we set langage and use it
    if (empty($GLOBALS['lang']))
        setLanguage();
    //get object
    $lang = $GLOBALS['lang'];

    return $lang->langText($keyname, $params);
}

/**
 * return array list of lanuages array, each elment is row from languages table
 * 
 */
function languageList() {
    if (MULTI_LANGUAGE) {
        $db = getDbClassInstance();
        $sql = 'select * from languages where enabled=1  ORDER BY id asc';

        $rows = $db->query($sql, array());
        if ($db->RowCount() >= 1)
            return $rows;
    }
    return array();
}

/**
 * complete html for page show 
 * 
 * @param string $centent contet of html page
 * @param string $page page name
 * @param string $footJs js functions to be loaded to footer
 * @param string $directPageLoad direct load page/end point
 * @param array $options options set in route() function 
 * @return string
 */
function pageHtml($centent, $page, $footJs, $directPageLoad, $options) {

    require_once "ui/ui.header.class.php";
    $uiHeader = new uiHeader();

    $pageHtml = '';

    // header ui
    $pageHtml.= $uiHeader->getHeaderUi();

    // top navigation bar
    $langs = languageList();

    $pageHtml.= $uiHeader->get_side_nav($langs);
    //get loading div
    $pageHtml.= $uiHeader->getLoadingDiv();

    //<!-- center area starts-->
    //get content wrapper and content
    $pageHtml.= $uiHeader->getContentWrapper($centent);
    //<!-- center area ends-->
    //for footer
    $csrf = json_encode(csrfToken());
    require_once "ui/ui.footer.class.php";
    $uiFooter = new uiFooter();

    $email = $contactName = '';
    if (is_logged()) {
        //if logged in we set contact name and email
        $email = $_SESSION['email'];
        $contactName = $uiFooter->getDispalyName($_SESSION['fname'], $_SESSION['lname'], $_SESSION['username'], $_SESSION['email']);
    }

    $pageHtml.= $uiFooter->getFooterUi($page, $footJs, $csrf, $directPageLoad, $options, $email, $contactName);

    return $pageHtml;
}

/**
 * return user id
 * if user logged in return logged in user id else 0
 * 
 * @return int user id
 */
function getUserId() {
    $userId = 0;
    if (is_logged())
        $userId = $_SESSION['id'];

    return $userId;
}

/////// for sortable tabels  and paginations end
//
//coalcal Common function
require_once "logic/commonCoalcal.php";

?>