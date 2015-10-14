<?php
/**
 * rote functions
 * 
 * Note: convert later to OOP
 */


include 'config/conf.php';
include 'logic/base/error.php';
include 'Swift-5.0.1/swift_required.php';
require_once "logic/base/db.class.php";
require_once "logic/base/common.php";

/**
 * array of regex to match for direact page loads ex : sitename/signup 
 * 
 * signin, signup
 */
function regexDirectPageLoadMatch() {
    $dpLoad = array(
        '^register$',
        '^login$',
        '^school$',
        '^home$'
    );
    return $dpLoad;
}

/**
 * check if given text is matched direct page request 
 * for now we allow only sitename/signin and sitename/signup
 */
function isDirectPageLoadRequest($text) {
    $fTypes = regexDirectPageLoadMatch();
    $joined = '(' . implode(')|(', $fTypes) . ')';
    $regex = '/' . $joined . '/i'; //case insenstive

    if (preg_match($regex, $text)) {
        //matched to file types
        return true;
    }
    return false;
}

/*
 * return list of allwed pages as array
 */

function getAllowedPages() {
    //list of pages allow to access 
    $alwPages = array(
        'user_aj' => array('page' => 'user_ajax.php', 'path' => 'ajax/'),
        'profile_aj' => array('page' => 'profile_ajax.php', 'path' => 'ajax/'),
        'recaptchaToIframe' => array('page' => 'recaptchaToIframe.php', 'path' => 'ajax/'),
        'admin_aj' => array('page'=>'admin_ajax.php','path'=>'ajax/')
    );


    return $alwPages;
}

/**
 * return allowed $_GET params 
 * @return string
 */
function allowedGetParams() {
    $allowedGetParams = array('vr', 'c', 'u', 'callback');
    return $allowedGetParams;
}

/**
 * check if we have extra get paramters than what we allow now
 */
function hasExtraGetParams() {
    $allowedGetParams = allowedGetParams();
    $getCount = count($_GET);
    if (count($getCount) > 0) {
        $allowedCount = 0;
        //we have get params
        foreach ($allowedGetParams as $allowed) {
            if (isset($_GET[$allowed])) {
                $allowedCount++;
            }
        }
        if ($getCount > $allowedCount) {
            return true;
        }
    }
    return false;
}

/**
 * return requested username and pagename in url
 * 
 */
function getInforFromUrl() {
    $pageName = $pagePath = $pageCode = $username = $verifyCode = '';
    $fileName = $directPageLoad = '';
    $urlFirstParam = $urlSecondParam = $requestedPageCode = '';
    $urlParamCount = 0;
    $extraGetParam = false;
    $options = array();

    //firt check if page and or user name passed as $_GET parameter
    $verifyCode = trim(get('vr', ''));

    if ($verifyCode == '') {
        //if verification realted action goes no need to worry about username or pagename and they will be empty
        $pageCode = $requestedPageCode = trim(get('c'), '');
        $username = trim(get('u', ''));


        //if $username is passed via post
        if ($username == '')
            $username = trim(post('username', ''));

        /*
         * if any of $pageName or $userName or both found in $_GET paramter we have used
         * $_GET to pass infor and no need to check for if user name or page passed using slashs after
         * site name. ex :domain/username/page or domain/username
         * we only look for pagename in slashed url if username is passed using shalshes
         * 
         * if ajax request $pageCode must be set already with since it is passed with get params 
         */
        if ($pageCode == '' && (!USERNAME || (USERNAME && $username == ''))) {

            //get uri
            $rUri = trim($_SERVER['REQUEST_URI']);
            //get hosted parth url, in case hosted in subfolder in url using SITE_URL
            $hostedUrlPath = str_replace(array('http://', 'https://', '://', $_SERVER['HTTP_HOST']), array('', '', '', ''), SITE_URL);
            //replace back slash if exist with forward slash, note: can't use preg_replace for this
            $rUri = str_replace(array('\\', $hostedUrlPath), array('/', ''), $rUri);
            //remove start and end slashes and white spaces and any query strings if have
            $rUri = trim(preg_replace(array('/\?.*$/', '/^\//', '/\/$/'), array('', '', ''), $rUri));
            //we have ignored hosted path form $rUri and now what we have is uri param passed with slashes
            $comps = explode('/', $rUri);
            //set all slash params to be used later  
            $options['slashParams'] = $comps;

            $urlParamCount = count($comps);
            if ($urlParamCount >= 1) {
                /*
                 * if we have one param passes as sitename/parama1 (may be we have some get parmas at the end like ?a=b&c=1 .. etc)
                 * we take first param to consideration and this can be a username, 
                 * filename like sitenmae/index.php or sitename/a.png
                 * or direct page load request like sitename/signup
                 */
                $urlFirstParam = urldecode(trim($comps[0]));

                if ($urlFirstParam != '') {
                    //first check given $urlFirstParam is a file
                    if (isFileType($urlFirstParam)) {
                        //above code may have taken filename in url as username, so we add fix here
                        $fileName = $urlFirstParam;
                    } else if (isDirectPageLoadRequest($urlFirstParam)) {
                        //check if $urlFirstParam is a direct page load request ex: sitename/signup
                        $directPageLoad = $urlFirstParam;
                    }else if (USERNAME) {
                        //if not  and USERNAME is true $urlFirstParam can be available or not available username
                        $username = $urlFirstParam;
                    }

                    //check for second param in url, but not used so far
                    if ($urlParamCount >= 2) {
                        /*
                         * note: this is not used any place so far 4/2/2015 Eranga
                         * 
                         * if we have more than one params passes as sitename/parama1/param2 ....
                         * we take up to 2 params to consideration 
                         * if $urlFirstParam is username $urlSecondParam can be page code sitename/username/page
                         */
                        if (USERNAME && $username != '') {
                            $urlSecondParam = trim($comps[1]);
                            if (!isFileType($urlFirstParam)) {
                                //above code may have taken filename in url as username, so we add fix here
                                $pageCode = $urlSecondParam;
                            }
                        } else if ($directPageLoad != '') {
                            /*
                             * we are in direct page load mode and we have second paramter 
                             */
                        }
                    }
                }
            }
        }

        //check if $pageCode has any allowed page name and if there we set it as $pageName to inculde
        $alwPages = getAllowedPages();
        if ($pageCode != '' && array_key_exists($pageCode, $alwPages)) {
            $pageName = $alwPages[$pageCode]['page'];
            $pagePath = $alwPages[$pageCode]['path'];
        }

        //note:in user.username is not url encoded, so we have to get urldecoded user name 
        $username = urldecode($username);

        $extraGetParam = hasExtraGetParams();
    }
    return array('username' => $username, 'pageName' => $pageName, 'pagePath' => $pagePath, 'verifyCode' => $verifyCode,
        'fileName' => $fileName, 'directPageLoad' => $directPageLoad, 'urlParamCount' => $urlParamCount
        , 'extraGetParam' => $extraGetParam, 'requestedPageCode' => $requestedPageCode, 'options' => $options);
}

/**
 * do routing
 */
function route() {

    /*
     *  only allowed permited field to access 
     *  if requseting a page must use site/?c=page format in url
     *  if url is for username loading have to be site/username or site.com?u=username if username used systems
     *  if want to call page with username user site/username/page format or url or site?u=username&c=pagename
     */

    //check if user name requsted in url
    $urlInfor = getInforFromUrl();
    //if verification realted action goes no need to worry about username or pagename and they will be empty
    $verifyCode = $urlInfor['verifyCode']; //used in home.php
    $username = $urlInfor['username'];
    $pageName = $urlInfor['pageName'];
    $pagePath = $urlInfor['pagePath'];
    $fileName = $urlInfor['fileName'];
    $directPageLoad = strtolower($urlInfor['directPageLoad']); // will be used in home.php
    $extraGetParam = $urlInfor['extraGetParam'];
    $urlParamCount = $urlInfor['urlParamCount'];
    $requestedPageCode = $urlInfor['requestedPageCode'];
    $options = $urlInfor['options'];

    if (!doUrlChangedReload($verifyCode, $username, $pageName, $fileName, $directPageLoad, $extraGetParam, $urlParamCount, $requestedPageCode, $options)) {
        //update $_REQUEST use later if need
        $_REQUEST['username'] = $username;

        if ($pageName != '') {
            $page = $pagePath . $pageName;
        } else {
            $page = 'logic/home.php';
        }

        include $page;
    }
}

/**
 * if url is not clean if have lot of unwanted stuffs like extra get params or 
 * extras urls params like sitename/parma1/param2/param3
 * Or combined that is not something from us.
 * So will reload proper url
 * 
 */
function doUrlChangedReload($verifyCode, $username, $pageName, $fileName, $directPageLoad, $extraGetParam, $urlParamCount, $requestedPageCode, $options) {

    //if we have extra, unwated parms 
    if ($extraGetParam) {
        if ($fileName == 'index.php') {
            //die('1');
            $url = SITE_URL;
            redirect($url);
            return true;
        } else if ($verifyCode != '') {
            //we load url only with $verifyCode
            $url = SITE_URL . '/?vr=' . $verifyCode;
            redirect($url);
            return true;
        }
    }
    //if we have direct page load request
    if ($directPageLoad != '') {
        $directPageLoad = strtolower($directPageLoad);
        if (is_logged()) {
            //loggeed in
            if (in_array($directPageLoad, array('register', 'login'))) {
                //we dont load signup or sign in if user user alreday logged in 
                $url = SITE_URL;
                redirect($url);
                return true;
            }else if (isAdmin()) {
                //if admin logged in adn requested school page or any page we direct to admin page (home page)
                $url = SITE_URL ;
                redirect($url);
                return true;
            }  else if (in_array($directPageLoad, array('school')) && ($extraGetParam || $urlParamCount > 1)) {
                //in case we have sign up and has any more slash params or get params we remove it
                $url = SITE_URL . '/' . $directPageLoad;
                redirect($url);
                return true;
            } 
        } else {
            //for not logged in
            if (in_array($directPageLoad, array('school'))) {
                //if requesting to load pages that need to be sign in to view we show sign in page
                $url = SITE_URL . '/login';
                redirect($url);
                return true;
            } else if (in_array($directPageLoad, array('register', 'login')) && ($extraGetParam || $urlParamCount > 1)) {
                //in case we have sign up and has any more slash params or get params we remove it
                $url = SITE_URL . '/' . $directPageLoad;
                redirect($url);
                return true;
            }
        }

        //for both logged in and not
        if ($directPageLoad == 'home') {
            //if requested home page using direct url site/home we just load site
            $url = SITE_URL;
            redirect($url);
            return true;
        }
    }else{
        if (is_logged() && !isAdmin() && $pageName=='') {
            //if logged in redirect to school page if not ajax call, if ajax call $pageName will not be empty
            $url = SITE_URL . '/school';
                redirect($url);
                return true;
        }
    }

    //don't have site/username page for this
//    //if requested to load username via url in browser
//    if (USERNAME && $username != '' && $urlParamCount > 0) {
//        $url = SITE_URL . '/' . urlencode($username);
//        redirect($url);
//        return true;
//    }

    //for direct page load if current url we have has no issue but has slash at then end we remove it.
    $rUri = trim($_SERVER['REQUEST_URI']);
    if ($directPageLoad != '' && preg_match('/\/$/i', $rUri)) {
        $url = trim(preg_replace('/\/$/i', '', $rUri));
        redirect($url);
        return true;
    }

    return false;
}

?>