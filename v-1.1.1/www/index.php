<?php
/**
 * All request to website routes route this page.  
 */

/*
 * Set include paths to app and lib
 */
$basePath='C:/xampp/htdocs/fiverr/jobs-working/coalsocal/coalcal.com/';
ini_set('include_path', get_include_path() . PATH_SEPARATOR . $basePath.'app');
ini_set('include_path', get_include_path() . PATH_SEPARATOR . $basePath.'lib');


include "logic/route.php";
init();
try {
    //start routing
    route();
} catch (Exception $e) {
    //error stop script
    die("An Error has occured and support has been notified. Sorry for your inconvience.");
}
?>