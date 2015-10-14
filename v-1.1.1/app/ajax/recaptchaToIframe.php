<?php

/*
 * return recaptcha using lib/recaptchalib.php library
 */

function getRecaptcha() {    
     $langRes = getDefaultLanguage();
        $recaptcharLangCode = $langRes['recaptcharCode'];
        
    require_once "recaptchalib.php";
    $recaptcha = recaptcha_get_html(RECAPTCHA_SITE_KEY, null, RECAPTCHA_USE_SSL,$recaptcharLangCode);
    return $recaptcha;
}

$recaptcha = getRecaptcha();

require_once "ui/ui.common.class.php";
$uiCommon = new uiCommon();

/*
 * has to load recaptchar content to ifrmae, 
 * else it does not work on ajax load
 */
echo $uiCommon->getRecaptchaHtml($recaptcha);
die();

?>