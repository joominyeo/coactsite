<?php

/*
 * hold common ui functions
 */
if (!class_exists('htmlElementsBase'))
    require_once "ui.htmlElements.base.class.php";

class uiCommonBase extends htmlElementsBase {
    /*
     * variables
     */

    public function __construct() {
        parent::__construct();
    }

    /*
     * echo contet as html entities for security (XSS)
     */

    public function se($txt) {
        echo(et($txt));
    }

    /**
     * return contet as html formmatd as entities for security (XSS)
     * just same as se() but not echo but turn values
     */
    public function et($txt) {
        return htmlentities($txt, ENT_QUOTES, 'UTF-8');
    }

    /**
     * get name to disply using 
     * first name, last name 
     * $maxLength : if 0 not shotning. if grater than 0 we make sure max length of reuturn name
     */
    public function getDispalyName($fname = '', $lname = '', $username = '', $email = '', $maxLength = 0) {
        $cont_name = '';
        if (trim($fname) != '')
            $cont_name = ucfirst(trim($fname));

        if (trim($lname) != '') {
            if ($cont_name != '')
                $cont_name .= ' ' . ucfirst(trim($lname));
            else
                $cont_name = ucfirst(trim($lname));
        }

        //if still contact name empty and $username not empty we show $username
        if ($cont_name == '' && trim($username) != '')
            $cont_name = trim($username);

        $useEmailAsContact = false;
        //if nothing set and we set email
        if ($cont_name == '' && trim($email) != '') {
            $cont_name = preg_replace('/@.+$/i', '', trim($email));
            $useEmailAsContact = true;
        }

        //shoten if too long
        if ($cont_name != '' && $maxLength > 0) {
            //make sure max length for $cont_name
            $cont_name = $this->formatText($cont_name, $maxLength);
        }

        //if we don't have firstname and last name or username we use email as contact name, make sure it is lower case
        if ($useEmailAsContact)
            $cont_name = strtolower($cont_name);
        else
            $cont_name = ucwords($cont_name);

        return $cont_name;
    }

    /*
     * 
     * return markup to show loading 
     * ie:loading image 
     * 
     * $version: thumbnail|icon|small|large|medium  
     * Thumbnail: 120(w) X 213 (h)
      Small : 240(w) X 427(h)
      Medium: 640 (W) X 1138 (h)
      Large: 1024 (W) X 1820 (h)
      Icon : 75 (W) X 75 (h)
     * tiny: 16 (W) X 16 (h)
     * 
     * $height,$width : auto|100px et 
     * $id for loading element . so if need user can use this id to replace content after load done
     * 
     * getLoadingMarkup('centerDev', 'thumbnail', '100%', 'auto')
     */

    public function getLoadingMarkup($type, $version, $id = '', $width = 'auto', $height = 'auto', $image = '') {
        if ($image == '') {
            switch ($version) {
                case 'thumbnail':
//                $image = 'loading-124x124.gif';
                    $image = 'hair-growing-240x96.gif';
                    break;
                case 'small':
//                $image = 'loading-300x300.gif';
                    $image = 'hair-growing-240x96.gif';
                    break;
                case 'medium':
//                $image = 'loading-512x512.gif';
                    $image = 'hair-growing-240x96.gif';
                    break;
                case 'large':
//                $image = 'loading-512x512.gif';
                    $image = 'hair-growing-240x96.gif';
                    break;
                case 'icon':
                    //$image = 'loading-100x100.gif';
                    $image = 'hair-growing-120x48.gif';
                    break;
                case 'tiny':
//                $image = 'loading-24x24.gif';
                    $image = 'hair-growing-120x48.gif';
                    break;
            }
        }

        $markup = '';
        switch ($type) {
            case 'imageTag':
                //just loading image link
                $style = 'width:' . $width . ';height:' . $height . ';max-width:100%;max-height:100%;';
                $markup = '<img id="' . $id . '" class="" src="' . APP_IMG_URL . '/' . $image . '" style="' . $style . '">';
                break;
            case 'centerDev':
                //image in a dev with auto size image horizontal centred and if height is number vertically too

                $style = 'width:auto;height:auto;max-width:100%;max-height:100%';
                //for this set dev with 100%
                $devStyle = 'text-align: center; width:' . $width . ';height:' . $height . ';';
                $devStyle.='line-height: ' . $height . ';';
                $devStyle.='background-color: #F8E0E6;opacity: 0.5;';

                $markup = '<div id="' . $id . '" style="' . $devStyle . '"><img class="" src="' . APP_IMG_URL . '/' . $image . '" style="' . $style . '"></div>';

                break;
            case 'backDiv':
                //div has loading image as background image
                //later add this
                break;
        }

        return $markup;
    }

    /**
     * remove all new lines from a text
     */
    public function stripNewLines($text) {
        $text = trim(preg_replace('/\s+/', ' ', $text));
        return $text;
    }

    /*
     * return logn text formatted to dispaly
     * formatText($aboutme,120,3,40,$href)
     * 
     * $text,
     * $maxLineLength = 120 max length for single line text,
     * $maxLines=3 max lines we can show,
     * $maxMultyLineLength = 40 when multy line text is there max length for a line, 
     * $viewMoreHref = '' link to load for view more
     * $viewMorePop = html codes show at view more, this is done to use for popup view more
     * note:don't set $viewMorePop and $viewMoreHref both. if set only $viewMoreHref will work
     * 
     * usage : for single line $this->ui->formatText($textLine, 16);
     * 
     */

    public function formatText($text, $maxLineLength = 120, $maxLines = 3, $maxMultyLineLength = 40, $viewMoreHref = '', $viewMorePop = '') {
        if (trim($text) != '') {
            $textArr = explode("\n", $text);
            $lines = count($textArr);

            if ($lines > 1) {
                $linesText = '';
                $shrinkLine = false;
                //we can only allow max lines for text
                if ($lines < $maxLines)
                    $maxLines = $lines;

                for ($x = 0; $x <= ($maxLines - 1); $x++) {
                    $tempText = $this->et($textArr[$x]);
                    //make sure max length for a line
                    if (strlen($tempText) > $maxMultyLineLength) {
                        $tempText = substr($tempText, 0, ($maxMultyLineLength - 3)) . '...';
                        $shrinkLine = true;
                    }
                    //if more line left add to last line... 
                    if ($x == ($maxLines - 1) && ($lines > $maxLines || $shrinkLine)) {
                        $tempText .= '<br>.......';
                        if ($viewMoreHref != '')
                            $tempText .=' <a class="" href="' . $viewMoreHref . '" target="_blank">View More</a>';
                        else if ($viewMorePop != '')
                            $tempText .=$viewMorePop;
                    }

                    //add <br> except for first line
                    if ($linesText != '')
                        $tempText = '<br>' . $tempText;

                    $linesText.=$tempText;
                }
                $text = $linesText;
            }else {
                //no lines just fix max length
                //make sure max length for a line

                if (strlen($text) > $maxLineLength) {
                    $text = substr($text, 0, ($maxLineLength - 3)) . '...';
                    if ($viewMoreHref != '')
                        $text .= ' <a class="" href="' . $viewMoreHref . '" target="_blank">View More</a>';
                    else if ($viewMorePop != '')
                        $text .=$viewMorePop;
                }
            }
            $text = $text;
        }
        return $text;
    }

    /*
     * return getRecaptchaIframe
     */

    public function getRecaptchaIframe() {
        $style = 'border:0;width:100%;max-width:100%;';
        $style .='margin-left:-10px;';
//        $style .='margin-right:-10px;';
        $style .='overflow-x: visible;';
        $style .='overflow-y: auto;';
        $style .='height: auto;';

        return '<iframe id="recaptcha_iframe" style="' . $style . '" src="' . SITE_URL . '/index.php?c=recaptchaToIframe">recaptchar</iframe>';
    }

    public function getRecaptchaDiv() {
        $langRes = getDefaultLanguage();
        $recaptcharLangCode = $langRes['recaptcharCode'];

        $html = '<script src="https://www.google.com/recaptcha/api.js?hl=' . $recaptcharLangCode . '" async defer></script>';
//        $html = '<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit&hl=' . $recaptcharLangCode . '" async defer></script>';


        $html.='<div class="g-recaptcha" data-sitekey="' . RECAPTCHA_SITE_KEY . '" ></div>';

//        $html.='<script type="text/javascript">
//                var onloadCallback = function() {
//                  console.log("grecaptcha is ready!");
//                  grecaptcha.render(".g-recaptcha",{"sitekey":"' . RECAPTCHA_SITE_KEY . '"});
//                };
//              </script>';

        return $html;
    }

    /**
     * return li list for lanugage drop down
     * 
     * @param array $langs list of lanuages array, each elment is row from languages table
     */
    protected function languageDropDownList($langs = array()) {
        $li = '';
        $currentLang = 'Language';
        foreach ($langs as $lang) {
            if ((!empty($_SESSION['language_code_default']) && $lang['code'] == $_SESSION['language_code_default'])
                    || (!empty($_SESSION['language_code']) && $lang['code'] == $_SESSION['language_code'])) {
                $currentLang = $lang['name'];
                //we don't add current laguage to list
                continue;
            }

            $li .= '<li><a href="javascript:void(0)" class="text-capitalize" onclick="changeLanguage(\'' . $lang['code'] . '\')">' . $lang['name'] . '</a></li>';
        }
        return array('li' => $li, 'currentLang' => $currentLang);
    }

    /**
     * return language li drop down
     * 
     * @param array $langs list of lanuages array, each elment is row from languages table
     */
    protected function languageLi($langs = array()) {
        $langLi = '';
        if (MULTI_LANGUAGE) {
            $langLiDropOptionsRes = $this->languageDropDownList($langs);
            $langLiDropOptions = $langLiDropOptionsRes['li'];
            $currentLang = $langLiDropOptionsRes['currentLang'];

            $langNoLi = '<a href="javascript:void(0)" class="dropdown-toggle name-li" id="dropdown-name" data-toggle="dropdown">' . $currentLang . ' <b class="caret"></b> </a>
                        <ul class="dropdown-menu">
                            ' . $langLiDropOptions . '
                        </ul>';
            $langLi = '<li class="dropdown">' . $langNoLi . '</li>';
        }

        return $langLi;
    }

    protected function homeLi() {
        $homeLink = '<a href="' . SITE_URL . '" class="text-capitalize"><span class="glyphicon glyphicon-home"></span></a>';
        $homeLi = '<li>' . $homeLink . '</li>';
        return $homeLi;
    }

    /**
     * nav menu li list show when user signed and sign both
     */
    protected function navMenuCommonLiList() {
        $markup = '';
        $markup .= $this->homeLi();

        return $markup;
    }

    /**
     * option for name li drop down shown after logged in
     * 
     * @param bool $glyphicon if try show glyphicon at menu
     * @param string $href value set for href and default javascript:void(0)
     * @return string
     */
    protected function nameLiDropOptions($glyphicon = true,$href='javascript:void(0)') {
        $generalSettingsLiIcon = '';
        if ($glyphicon)
            $generalSettingsLiIcon = '<span class="glyphicon glyphicon-cog"></span> ';
        $generalSettingsLi = '<li><a href="'.$href.'" class="text-capitalize" onclick="loadMainContent(\'profile\',{\'tab\':\'generalSettings\'})">' . $generalSettingsLiIcon . langText('profile_user_settings') . '</a></li>';

        $options = '';
        $options.=$generalSettingsLi;
        return $options;
    }

    /**
     * return name li
     * 
     * @param bool $glyphicon if try show glyphicon at menu
     * @param string $href value set for href and default javascript:void(0)
     * @return string
     */
    protected function nameLi($glyphicon = true,$href='javascript:void(0)') {
        //for logged in user
        $cont_name = $this->getDispalyName($_SESSION['fname'], $_SESSION['lname'], $_SESSION['username'], $_SESSION['email'], 20);

        $nameLiDropOptions = $this->nameLiDropOptions($glyphicon,$href);
        
        $userIcon = '';
        if ($glyphicon)
            $userIcon = '<span class="glyphicon glyphicon-user"></span> ';

        $nameNoLi = '   <a href="javascript:void(0)" class="dropdown-toggle name-li" id="dropdown-name" data-toggle="dropdown">' .$userIcon. $this->et($cont_name) . ' <b class="caret"></b> </a>
                        <ul class="dropdown-menu">
                            ' . $nameLiDropOptions . '
                        </ul>';
        $nameLi = '<li class="dropdown">' . $nameNoLi . '</li>';

        return $nameLi;
    }

    protected function adminLi() {
        $adminLi = '<li><a href="' . SITE_URL . '/admin" class="text-capitalize">Admin</a></li>';
        return $adminLi;
    }

    protected function signOutLi() {
        //for sign out li
        $signOutLi = '  <li>
                            <a href="javascript:void(0)" onclick="signOut()" class="text-capitalize"><span class="glyphicon glyphicon-off"></span></a>
                        </li>';
        return $signOutLi;
    }

    /**
     * nav menu li list show when user signed in only
     * 
     * @param array $langs list of lanuages array, each elment is row from languages table
     */
    protected function navMenuSignedInLiList() {
        $verticalSeparatorLi = $this->verticalSeparatorLi();

        $markup = '';

        if (!isAdmin()) {
            $markup .= $this->navMenuCommonLiList();
        } else if (ADMIN_ENABLED) {
            //get home li, we don't show other page links for admin
//            $markup .= $this->homeLi();
            $markup .= $verticalSeparatorLi;
            $adminLi = $this->adminLi();
            $markup .=$adminLi;
        }

        if ($markup != '')
            $markup .= $verticalSeparatorLi;
        $markup .= $this->nameLi();
        $markup .= $verticalSeparatorLi;
        $markup .= $this->signOutLi();

        return $markup;
    }

    /**
     * nav menu li list show when user not signed in only
     * 
     * @param array $langs list of lanuages array, each elment is row from languages table
     */
    protected function navMenuNotSignedInLiList($langs = array()) {
        $verticalSeparatorLi = $this->verticalSeparatorLi();

        $signUpLi = '<li class="sginup-li"><a href = "' . SITE_URL . '/signup">' . langText('sign_up') . '</a></li>';
        $signInLi = '<li class="sginin-li"><a href = "' . SITE_URL . '/signin">' . langText('sign_in') . '</a></li>';
        //get lang li
        $langLi = $this->languageLi($langs);

        $markup = '';
        $markup .= $this->navMenuCommonLiList();

        $markup.= $verticalSeparatorLi;
        $markup.= $signUpLi;
        $markup.= $verticalSeparatorLi;
        $markup.= $signInLi;

        if ($langLi != '') {
            $markup .= $verticalSeparatorLi;
            $markup .= $langLi;
        }
        return $markup;
    }

    protected function verticalSeparatorLi() {
        $verticalSeparatorLi = '<li class="hidden-xs"><div class="vertical–separator"></div></li>';
        return $verticalSeparatorLi;
    }

    /**
     * return nav brand 
     * @return string
     */
    protected function navBarnd() {
        $markup = ' <a class="navbar-brand" style="" href="' . SITE_URL . '" onclick="">
                        ' . SITE_NAME . '
                    </a>';
        return $markup;
    }

    /**
     * return top navigation drop downs links etc
     *  
     * @param array $langs list of lanuages array, each elment is row from languages table
     */
    public function topNavOptions($langs = array()) {
        $markup = '';
        $markup .= '<div class="container-fluid navbar-container-one">
                            <!-- Brand and toggle get grouped for better mobile display -->
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navbar-collapse-1">
                                  <span class="sr-only">Toggle navigation</span>
                                  <span class="icon-bar"></span>
                                  <span class="icon-bar"></span>
                                  <span class="icon-bar"></span>
                                </button>';
        $markup .= $this->navBarnd();
        $markup .= '    </div>';

        $markup .= '    <!-- Collect the nav links, forms, and other content for toggling -->
                            <div class="collapse navbar-collapse navbar-row-one" id="bs-navbar-collapse-1">';
        $markup .= '        <ul id="menu" class="nav navbar-nav navbar-right">';
        if (!is_logged())
            $markup .= $this->navMenuNotSignedInLiList($langs);
        else
            $markup .= $this->navMenuSignedInLiList();
        $markup.='          </ul>';
        $markup .= '    </div><!-- /.navbar-collapse --> ';
        $markup .= '</div><!-- /.container-fluid -->';

        return $markup;
    }

    /**
     * has to load recaptchar content to ifrmae, 
     * else it does not work on ajax load
     */
    public function getRecaptchaHtml($recaptcha) {

        return '<!DOCTYPE html>
                <html>
                    <head>
                        <title></title>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    </head>
                    <body>
                        ' . $recaptcha . '
                    </body>
                </html>';
    }

    /*
     * return formatted selected option name to show in drop down 
     * 
     * $em: if ture add <em></em> around $name
     */

    public function dropSelectedOptionName($name, $em = false) {
        $name = $this->et($name);
        if ($em)
            $name = '<em>' . $name . '</em>';
        return $name;
    }

    /*
     * return boostrap drop down option li
     */

    public function dropOptionLi($optNname, $onclick, $liClass = '', $liButtons = '', $href = 'javascript:void(0)') {
        $option = ' <li class="' . $liClass . '">
                        <a href="' . $href . '" onclick="' . $onclick . '" >' . $optNname . '</a>
                        ' . $liButtons . '
                    </li>';
        return $option;
    }

    /*
     * hidden field to hold activity drop down value
     */

    public function dropHiddenValuefield($prf, $uniqueId, $selectedId) {
        $h_input = '<input type="hidden" id="' . $prf . 'id_' . $uniqueId . '" name="' . $prf . 'id[' . $uniqueId . ']" value="' . $selectedId . '" />';
        return $h_input;
    }

    /**
     * return email body html
     * 
     * @param type $message
     * @param type $title
     * @param type $name
     * @param type $signature
     * @param type $showSignature
     * @param type $titleTag
     * @param type $quote qute to show
     * @return string
     */
    public function emailBody($message, $title = '', $name = '', $signature = '', $showSignature = true, $titleTag = '', $quote = '') {

        $bodySignature = '';
        if ($showSignature) {
            //add default signature if $signature empty
            if ($signature == '') {
                $signature = '  <p>' . langText('email_signature', array('SITE_NAME' => SITE_NAME)) . '</p>';
                if (!empty($quote))
                    $signature .='<p>' . $quote . '</p>';
            }
            //add signature to dev to debug and make sure we send email from
            if (APPMODE == 'dev')
                $signature .='<br>' . SITE_URL;

            $bodySignature.= $signature;
        }

        $langTag = $this->langTag();

        $body = '
<!DOCTYPE html>
<html ' . $langTag . '> 
<head>
  <meta charset="utf-8">
  <title>' . $titleTag . '</title>
 
  <style>
    @media only screen and (min-device-width: 541px) {
      .content {
        width: 540px !important;
      }
    }
  </style>
</head>
 
<body>
 
<!--[if (gte mso 9)|(IE)]>
  <table width="540" align="center" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td>
<![endif]-->
                <table class="content" bgcolor= "#DCDCE6" align="center" cellpadding="10" cellspacing="0" border="0" style="width: 100%; max-width: 540px;">
                <tr><td><h3>' . $title . '</h3></td></tr>
		</table>
		<table class="content" bgcolor= "#FFFFFF" align="center" cellpadding="10" cellspacing="0" border="1" bordercolor="#FFFFFF"style="width: 100%; max-width: 540px;">
                </table>
		<table class="content" bgcolor= "#F4F4FF" align="center" cellpadding="10" cellspacing="0" border="0" style="width: 100%; max-width: 540px;">
                <tr>
                <td>
		<p> ' . langText('salutation') . ' ' . $name . ', </p>
		' . $message . '
                ' . $bodySignature . '
		</td>
                </tr>
        </table>
 
<!--[if (gte mso 9)|(IE)]>
      </td>
    </tr>
</table>
<![endif]-->
 
</body>';

        return $body;
    }

    public function quickStartText() {
        $text = '<p class="text-p"> quick start text';
        $text .='</p>';
        return $text;
    }

    /**
     * format date time time to given format 
     * http://php.net/manual/en/function.date.php
     * 
     * @param string $dateTime date time string
     * @param string $format date time format default to 
     */
    public function formatDate($dateTime, $format = 'd F Y') {
        return date($format, strtotime($dateTime));
    }

    /**
     * format date time time to same default format with local lanuage settings
     * http://php.net/manual/en/function.strftime.php
     * 
     * @param string $dateTime date time string
     * @param string $format date time format default to 
     */
    public function formatDateLocal($dateTime, $format = '%d %B %Y') {
        setlocale(LC_TIME, getLanguageCode());
        // echo 'test';
        $retDateStr = strftime($format, strtotime($dateTime));
        setlocale(LC_TIME, NULL);
        return $retDateStr;
    }

    /**
     * return markup for contact Support modal 
     * 
     */
    public function contactSupportModelMrkup($email = '', $name = '') {
        $titleText = 'Drop us a line. We are really quick on email.';
        $emailRO = $nameRO = '';
        //if user logged in we pouplate email and name and make fields read only 
        if ($email != '') {
            $emailRO = 'readonly="readonly"';
            //this mean user logged in
            $titleText = 'Please enter name, email address and description and click submit.';
            if ($name != '') {
                $nameRO = 'readonly="readonly"';
            }
        }

        $mMarckup = '
            <div class="container-fluid">
                <div id="supportPopup" style="display: none;">                           
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                            <p>' . $titleText . '</p>
                        </div>
                    </div>                    
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  no-padding-col">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" value="' . $this->et($name) . '"  class="form-control" maxlength="100" placeholder="Name" id="supportPopupName" ' . $nameRO . '/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                            <input type="hidden" id="supportPopupType" value=""/>
                            <div class="form-group">                                
                                <label>Email Address</label>
                                <input type="text" value="' . $this->et($email) . '" class="form-control" maxlength="75" placeholder="Email" id="supportPopupEmail" ' . $emailRO . '/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                            <div class="form-group">
                                <label>Your message</label>
                                <textarea class="form-control" rows="3" placeholder="Your message" id="supportPopupDes"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">   
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col btn-col"> 
                            <button type="button" class="btn btn-default btn-sm pull-right" onclick="contactSupport(\'submit\')">Submit</button>
                            <!--<button type="button" class="btn btn-default btn-sm pull-right" style="margin-right: 10px;"  onclick="$.fancybox.close(true);">Close</button>-->
                        </div>
                    </div>
                </div>
            </div>';
        return $mMarckup;
    }

    /**
     * return lang tag for html5 declaration
     * @return string
     */
    protected function langTag() {
        $langCode = getLanguageCode();
        //set default
        if ($langCode == '')
            $langCode = 'en';
        $langTag = 'lang="' . $langCode . '"';

        return $langTag;
    }

}

?>