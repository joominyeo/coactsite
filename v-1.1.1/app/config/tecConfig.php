<?php

////////////////////// tec configuration start /////////////////////////
////////////////////////////////////////////////////////////////////////

/*
 * Please don’t change this section if you don’t know what they are.
 * These configurations are for developer.
 */


/**
 * Enable username system.
 * If true you can use username to sign in (will have field to called username in sign up).
 * If public profile is there username can be used to view profile site/username
 * You must make codes and libs needed are available before set this true. 
 */
define('USERNAME', true);

/**
 * if ture we will have first name and last name in sign up
 */
define('SIGNUP_NAME', true);

/**
 * if ture we will have password confirm field  at sign up
 */
define('SIGNUP_PASS2', true);

/**
 * if ture we will captchar at sign up
 */
define('SIGNUP_CAPTCHAR', true);

/**
 * google recaptcha have latest version and old 
 * this can be set to 1 or 2 (2  stil have issues)
 */
define('SIGNUP_CAPTCHAR_VERSION', 1);

/**
 * if ture we will show time zone settings at profile page
 */
define('TIME_ZONE', false);

/**
 * Set this true for local development environment.
 * Some things are not working on localhost. 
 * So to make things works in localhost   we use this variable to make sure there are ignored. 
 * 
 */
define('LOCAL_DEV', false);

/**
 * If true we have enabled admin module 
 */
define('ADMIN_ENABLED', true);

// admin module settings start
/**
 * set true to enable user admin module 
 * admin common modules setting 
 */
define('ADMIN_USER', false);

/**
 * set true to enable user logs admin module 
 * admin common modules setting 
 */
define('ADMIN_USER_LOGS', false);

/**
 * set true to enable ip block admin module 
 * admin common modules setting 
 */
define('ADMIN_IP_BLOCK', false);

/**
 * set true to enable country block admin module 
 * admin common modules setting 
 */
define('ADMIN_COUNTRY_BLOCK', false);

// admin module settings start end

/**
 * if true sign in user when email verifed by email verficaiton link
 * this will happen when verfiy email first time and verify email after change it in user settings
 */
define('SIGNIN_ON_EMAIL_VERIFY', true);

/**
 * string product code so we can recognise product some case to execute commands.    
 */
define('PRODUCT_CODE', 'coalcal.com');

/**
 * if true site is enabled with multi languages  
 * To make this work correctly lang/ folder must have languages files for languages you want to use.
 * Table languages must be filled with appropriate details   
 * 
 */
define('MULTI_LANGUAGE', false);


////////////////////// tec configuration  end //////////////////////////
////////////////////////////////////////////////////////////////////////
?>