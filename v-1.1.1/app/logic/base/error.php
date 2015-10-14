<?php
/**
 * Note: convert later to OOP
 * 
 * @param type $error_number
 * @param type $error_message
 * @param type $error_file
 * @param type $error_line
 */

//The error message can then be displayed, emailed, etc within the callback function.
function ErrorHandler($error_number, $error_message, $error_file, $error_line) {
    $error = "Error Number: " . $error_number . " | msg:" . $error_message . " | file:" . $error_file . " | ln:" . $error_line;

    switch ($error_number) {
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_PARSE:
            logError("Error fatal - :$error");
            break;
        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
            logError("Error - error:$error");
            break;
        case E_WARNING:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        case E_USER_WARNING:
            //for now we don't send emails to admin for warning and notice it may affect to speed of site 
            logError("Error - warn:$error", false);
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            logError("Error - info:$error", false);
            break;
        case E_STRICT:
            logError("Error - debug:$error");
            break;
        default:
            logError("Error - warn: $error");
    }
}

function shutdown() {
    $isError = false;

    if ($error = error_get_last()) {
        switch ($error['type']) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                $isError = true;
                break;
        }
    }

    if ($isError) {
        $error = "[SHUTDOWN] lvl:" . $error['type'] . " | msg:" . $error['message'] . " | file:" . $error['file'] . " | ln:" . $error['line'];
        logError("Shutdown:$error");
    }
}

/**
 * log error and send email
 * @param text $what
 * @param bool $email if ture send email and else not
 */
function logError($what, $email = true) {
    errorLog("emailSupportandLog: " . $what);

    //for now we don't send emails to admin for warning and notice it may affect to speed of site  
    if ($email) {
        $to = SUPPORT_EMAIL;
        $subject = ADMIN_ERROR_EMAIL_SUBJECT;
        $body = "$what";
        sendEmail($to, $subject, $body);
    }
}

?>