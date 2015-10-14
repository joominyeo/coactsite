<?php

/*
 * hold footer ui functions
 */
require_once "ui/ui.common.class.php";

class uiFooterBase extends uiCommon {

    public function __construct() {
        parent::__construct();
    }

    /**
     * $page : page loading
     */
    protected function getJsFiles($page, $directPageLoad = '', $options = array()) {

        $jsAppend = '';
        //load js files in page load/realod for profile etc
        if (!empty($page)) {
            switch ($page) {
                case 'profile':
                    $jsAppend.= "\n" . '<script src="' . JS_URL . '/profile.js"></script>';
                    break;
                case 'admin':
                    $jsAppend.= "\n" . '<script src="' . JS_URL . '/admin.js"></script>';
                    $jsAppend.= "\n" . '<script src="' . JS_URL . '/bootstrap-datetimepicker.js"></script>';
                    break;
                case 'profile':
                    $jsAppend.= "\n" . '<script src="' . JS_URL . '/school.js"></script>';
                    break;
            }
        }


        $JS_URL = JS_URL;
        $html = <<<EOT
<!-- Js -->
<!--<script src="{$JS_URL}/jquery-1.11.0.js"></script>-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

<script src="{$JS_URL}/default.js"></script>
<script src="{$JS_URL}/default.coalcal.js"></script>
    
<script src="{$JS_URL}/jquery-ui.min.js"></script>
    
<script src="{$JS_URL}/bootstrap.min.js"></script>
    
<!-- <script src="{$JS_URL}/datedropper.js"></script>  -->
        
<script src="{$JS_URL}/jquery.fancybox.pack.js"></script> <!-- Fancybox -->
<script src="{$JS_URL}/jquery.fancybox-media.js"></script> <!-- Fancybox for Media -->
<script src="{$JS_URL}/bootstrap-notify.js"></script>
<script src="{$JS_URL}/jquery.cookie.js"></script>
{$jsAppend}
<!-- End Js -->
EOT;
        return $html;
    }

    /**
     * custom js script load at bottom of page by default
     */
    protected function customJsScripts() {
        $jsScripts = '';

        return $jsScripts;
    }

    /**
     * return js footer scripts
     */
    protected function getJsScripts($footJs, $csrf) {
        $jsScripts = '';
        $jsScripts.= '<script type="text/javascript">';
        $jsScripts.= 'var JS_URL="' . JS_URL . '";';
        $jsScripts.= 'var CSS_URL="' . CSS_URL . '";';
        $jsScripts.= 'var SITE_URL="' . SITE_URL . '";';
        $jsScripts.= 'var APP_IMG_URL="' . APP_IMG_URL . '";';
        $jsScripts.= 'var COOKIE_NAME="' . COOKIE_NAME . '";';
        $jsScripts.= 'var COOKIE_TIME="' . COOKIE_TIME . '";';
        $jsScripts.= 'var COOKIE_DOMAIN="' . COOKIE_DOMAIN . '";';
        $jsScripts.= 'var LOCAL_DEV="' . LOCAL_DEV . '";';
        $jsScripts.= 'var USERNAME="' . USERNAME . '";';
        $jsScripts.= 'var SITE_NAME="' . SITE_NAME . '";';
        $jsScripts.= 'var SIGNUP_NAME="' . SIGNUP_NAME . '";';
        $jsScripts.= 'var SIGNUP_PASS2="' . SIGNUP_PASS2 . '";';
        $jsScripts.= 'var USERNAME="' . USERNAME . '";';
        $jsScripts.= 'var SIGNUP_CAPTCHAR="' . SIGNUP_CAPTCHAR . '";';
        $jsScripts.= 'var SIGNUP_CAPTCHAR_VERSION="' . SIGNUP_CAPTCHAR_VERSION . '";';
        $jsScripts.= 'var csrf=' . $csrf . ';';

        //for languages
        $jsScripts.= 'var langJs=' . getLangJs() . ';';

        if (isAdmin())
            $jsScripts.= 'var admin=true;';
        else
            $jsScripts.= 'var admin=false;';
        //if js script set
        if (!empty($footJs))
            $jsScripts.= $footJs;

        $jsScripts.= $this->customJsScripts();

        $jsScripts.= '</script>';

        return $jsScripts;
    }

    /*
     * return footer html markup
     */

    public function getFooterUi($page, $footJs, $csrf, $directPageLoad = '', $options = array(), $email = '', $name = '') {
        $jsFiles = $this->getJsFiles($page, $directPageLoad, $options);
        $jsScripts = $this->getJsScripts($footJs, $csrf);

        $html = $this->footerFoot1();
        $html .= $this->alertNotificationsBoxUi();
        $html .= $this->contactSupportModelMrkup($email, $name);
        $html .= <<<EOT
{$jsFiles}
{$jsScripts}
</body>
</html>
EOT;

        return $html;
    }

    protected function footerFoot1() {
        $siteName = SITE_NAME;
        $about = langText('about');
        $contact_us = langText('contact_us');
        $linkAboutUs = SITE_URL . '/about';

        $html = <<<EOT
        <!-- Footer -->
<div id="outer-footer" class="container-fluid">
    <footer>
        <div class="row " >
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                <br>
                <hr>
                <ul class="list-unstyled">
                    <li class="pull-left">
                        <a href="{$linkAboutUs}" title="{$about} {$siteName}">{$about} {$siteName}</a>
                    </li>
                    <!-- 
                    <li class="pull-left">     
                        &nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="loadMainContent('privacyPolicy');" title="Privacy Policy">Privacy Policy</a>
                    </li>
                    <li class="pull-left">     
                        &nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" title="Help">Help</a>
                    </li>
                    -->
                    <li class="pull-left">    
                        &nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="contactSupport('view','contact');" title="{$contact_us}">{$contact_us}</a>
                    </li>
                    <!--
                    <li class="pull-left">    
                        &nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="loadMainContent('termsOfService');" title="Terms of Service">Terms of Service</a>
                    </li>     
                    -->
                    <li class="pull-left"> 
                        <p>&nbsp;&nbsp;&nbsp;&copy;2015 {$siteName}</p>
                    </li>
                </ul>                
            </div>
        </div>
    </footer>
</div>
<!-- End Footer -->
EOT;
        return $html;
    }

    /**
     * return notifications box ui
     */
    protected function alertNotificationsBoxUi() {
        $html = <<<EOT
<div class="container-fluid">
   <div class="row">
        <div class="col-md-12">             
            <div id="alert-top" class='notifications top-right'>
            </div>
        </div>
    </div>
</div>
EOT;
        return $html;
    }

}

?>