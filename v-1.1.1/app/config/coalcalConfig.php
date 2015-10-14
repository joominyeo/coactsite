<?php

/*
 * This file contain coalcal.com site specific configurations  
 * Only change these if you know what you do.
 * Incorrect changing these may result errors in website.  
 * 
 */

/**
 * direcotry to uplad images
 * must have ending /
 */
define('UPLOAD_IMG_DIR', 'C:/xampp/htdocs/fiverr/jobs-working/coalsocal/coalcal.com/upl/');

/**
 * url of upload image directory UPLOAD_IMG_DIR
 * must not have ending /
 */
define('UPLOAD_IMG_URL', 'http://localhost/fiverr/jobs-working/coalsocal/coalcal.com/www/upl');


/**
 * if google analytics account id
 */
define('GOOGLE_ANALYTICS_ACCOUNT','');

/////////////// satrt $GLOBALS['titleForceLowerCases'] gloabally ///////////////////

/**
 * list of words that must be lower case in results title columns
 * keep all here in list in lowercase
 * @global type $GLOBALS['titleForceLowerCases']
 * @name $titleLowerCases 
 */
$GLOBALS['titleForceLowerCases'] = array(
    "of", "for", "up", "to", "in", "by", "and", "against", "at", "on", "keeps", "its", "the"
);
/////////////// end $GLOBALS['titleForceLowerCases'] gloabally ///////////////////


?>