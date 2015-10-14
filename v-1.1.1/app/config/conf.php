<?php

///////////////////////////////////////////////////////////////////////
////////////////////// basic configuration start //////////////////////

/**
 * This is to use place where site name should appear.  
 */
define('SITE_NAME', 'COAL | California');

/**
 * This is to use place where site domain name should appear.  
 * ex: google.com, abc.abcd.com
 */
define('DOMIN_NAME', 'coalcal.com');

/**
 * Default time zone set to UTC.
 * Recommended to keep this to UTC, If change this we will have to change server time zone 
 * and also mysql server time zone (if not in same server).  
 */
date_default_timezone_set('UTC');

/**
 * application mode dev|prod|maint
 * dev : development 
 * prod: production 
 * maint: maintains 
 * 
 */
define('APPMODE', 'dev');

/**
 * Database host url, if use a database in same server can use ‘localhost’ 
 */
define('DBHOST', 'localhost');
/**
 * Database name 
 */
define('DBNAME', 'coalsocal_coalcal');
/**
 * Database user name 
 */
define('DBUSER', 'root');
/**
 * Database user password  
 */
define('DBPASS', 'abc123');
/**
 * Database connection string for mysql and don't change this
 */
define('DBCONN', 'mysql:host=' . DBHOST . ';dbname=' . DBNAME);


////////////////////// basic configuration ends //////////////////////
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
////////////////////// path and url configuration start ///////////////

/**
 * url of js folder.
 * This will be publically accessible  directory. 
 * If need better performance we can add these files to CDN and use that url. 
 * must not have ending /
 */
define('JS_URL', 'http://localhost/fiverr/jobs-working/coalsocal/coalcal.com/www/js');

/**
 * url of css folder.
 * This will be publically accessible  directory. 
 * If need better performance we can add these files to CDN and use that url. 
 * must not have ending /
 */
define('CSS_URL', 'http://localhost/fiverr/jobs-working/coalsocal/coalcal.com/www/css');

/**
 * url of website. 
 * Note: does not have / at the end
 */
define('SITE_URL', 'http://localhost/fiverr/jobs-working/coalsocal/coalcal.com/www');

/**
 * Application static image url 
 */
define('APP_IMG_URL', 'http://localhost/fiverr/jobs-working/coalsocal/coalcal.com/www/images');

/**
 * Base path where “app”,”lib” folders etc there.
 * If all files are document root this will be path to document root.
 * This must be absolute path. 
 * must not have ending /
 */
define('BASE', 'C:/xampp/htdocs/fiverr/jobs-working/coalsocal/coalcal.com');



define('LOGFILE', 'C:/xampp/htdocs/fiverr/jobs-working/coalsocal/coalcal.com/log/logs.log');
define('ERROR_LOGFILE', 'C:/xampp/htdocs/fiverr/jobs-working/coalsocal/coalcal.com/log/error.log');
define('ACCESS_DENIED_LOGFILE', 'C:/xampp/htdocs/fiverr/jobs-working/coalsocal/coalcal.com/log/denied.access.log');
define('ACCESS_LOGFILE', 'C:/xampp/htdocs/fiverr/jobs-working/coalsocal/coalcal.com/log/access.log');


///////////////////////////////////////////////////////////////////////
////////////////////// path and url configuration end /////////////////
///////////////////////////////////////////////////////////////////////
////////////////////// email configuration start //////////////////////

/**
 * from name when send email to user
 */
define('EMAIL_FROM_NAME', 'coalcal');
/**
 * Email subject when of admin notification on error happened in the site. 
 */
define('ADMIN_ERROR_EMAIL_SUBJECT', 'coalcal Error- local');

/**
 * Support email address. This will not be displayed in web but all emails come to support and all site 
 * error notifications will be sent to this email. 
 */
define('SUPPORT_EMAIL', 'erangawiharagoda@gmail.com');


define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 465); //default 25 //for google 465
define('SMTP_AUTH', true);
define('SMTP_USERNAME', 'erangawdevtest@gmail.com');
define('SMTP_PASSWORD', 'ZBgv2k9w');
define('SMTP_SECURE', 'ssl');
define('SMTP_FROM_EMAIL','erangawdevtest@gmail.com'); 
 


////////////////////// email configuration ends ///////////////////////
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
////////////////////// cookies configuration start ////////////////////

/**
 * Remember me cookie name 
 */
define('COOKIE_NAME', 'coalcal');

/**
 * Remember me cookie domain
 * ex: to apply all subdomains of site
 *  .sitename.com
 */
define('COOKIE_DOMAIN', '.coalcal.com');
/**
 * Remember me cookie expire time.
 * Everytime time user singed in using login remembered remember me cookie updated to be live for next COOKIE_TIME
 * 
 * ex:
 * Expire in 1 hour = 3600, so set to expire in one month of no activity set to (3600 * 24 * 30)
 * 
 */
define('COOKIE_TIME', (3600 * 24 * 30)); //
////////////////////// cookies configuration ends ////////////////////
//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
////////////////////// Security configuration start //////////////////

/**
 * http://www.google.com/recaptcha keys  Site key
 */
define('RECAPTCHA_SITE_KEY', '6LdGKQsTAAAAAOoU8-L5OXUqTTjyOvmh_1ybIBsu');
/**
 * http://www.google.com/recaptcha keys  Secret key
 */
define('RECAPTCHA_PRIVKEY', '6LdGKQsTAAAAAJ7iv1CVwD5IoxYT3wtnToBcpCK4');
/**
 * http://www.google.com/recaptcha keys 
 * Should the request be made over ssl? (optional, default is false)
 */
define('RECAPTCHA_USE_SSL', false);


/**
 * How many continues failed login attempts allowed within LOGIN_ATTEMPTS_DURATION minutes 
 */
define('LOGIN_ATTEMPTS_LIMIT', 3);
/**
 * Duration no of LOGIN_ATTEMPTS_LIMIT allowed in minutes 
 */
define('LOGIN_ATTEMPTS_DURATION', 10);
/**
 * How long user will be lockout after exceed LOGIN_ATTEMPTS_LIMIT within LOGIN_ATTEMPTS_DURATION in minutes 
 */
define('LOGIN_LOCKOUT_TIME', 30);
/**
 * Send email notification to user saying that user has been lockout if user lockout in login attempts. 
 */
define('EMAIL_LOCKOUT_NOTIFICATION', true); // 

/**
 * How long the verify code send to email is valid for in minutes 
 * Verifications codes send emails can be
 * for password rest,  
 * sign up email verification codes, 
 * change email verification code etc.
 * If code expires they can always request new code.
 * So best keep this around 2 hours for security. 
 * 
 * Ex: for 2 hours set to 120 
 */
define('VERIFY_EXPIRE', 120);

/**
 * length of password salt
 * Something like 30 will be reasonable value.  
 * Maximum will be 255  
 */
define('SALT_LENGTH', 30);

/**
 * allow to use production site  when APPMODE=prod  for given ip address to use site for testing etc when in maintain mode 
 */
////////////////////// Security configuration   end ////////////////////
////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
////////////////////// other configuration starts //////////////////////

/**
 * array of options to show in per page, elements in array are rows per page
 * 
 * @global type $GLOBALS['perPageOptions'] 
 * @name $perPageOptions 
 */
$GLOBALS['perPageOptions'] = array(10,15,20,50,100);

/**
 * Number of rows displayed in a page in pagination by default  
 * must be a value from $GLOBALS['perPageOptions'] array
 */
define('ROWS_PER_PAGE', 10);

/**
 * if true per page dropdown option will be shown in pagination
 */
define('PER_PAGE', true);


/**
 * php time zone Identifier to treat as defult local time zone 
 * timezone identifiers that utc date need to converted to and return from function as described here http://php.net/manual/en/datetimezone.listidentifiers.php
 * 
 */
define('PHP_TZ_IDENTIFIER_DEFAULT', 'UTC');//Asia/Kolkata

/**
 * php time zone Identifier to treat as server defult local time zone 
 * This will be used for local to server default time zone conversions and vise versa
 * timezone identifiers that utc date need to converted to and return from function as described here http://php.net/manual/en/datetimezone.listidentifiers.php
 * 
 */
define('SERVER_PHP_TZ_IDENTIFIER_DEFAULT', 'UTC');



////////////////////// other configuration ends //////////////////////
//////////////////////////////////////////////////////////////////////
//tec configurations, Developer only
require_once "tecConfig.php";

//coalcalConfig configurations, only change if you know what you do
require_once "coalcalConfig.php";

?>