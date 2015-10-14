<?php

/*
 * hold common ui functions
 */
require_once "base/ui.common.base.class.php";

class uiCommon extends uiCommonBase {
    /*
     * variables
     */

    public function __construct() {
        parent::__construct();
    }

    /**
     * get common lis
     */
    private function commonLis() {
        $commonLis = '   <li><a href="#specialannouncements">Kindle Conference</a></li>
                                <li><a href="#top">Home</a></li>
                                <li><a href="#about">About</a></li>
                                <li><a href="#services">What We Do</a></li>
                                <!--<li><a href="#membership">Membership</a></li>-->
                                <li><a href="#board">COAL Board</a></li>
                                <li><a href="#database">Database</a></li>
                                <li><a href="#map">Our Location</a></li>';
        return $commonLis;
    }

    /**
     * return side navigation options
     *  
     * @param array $langs list of lanuages array, each elment is row from languages table
     */
    public function sideNavOptions($langs = array()) {
        $signInLi = $signUpLi = $signOutLi = $nameLi = $schoolLi = $otherLis = '';
        $commonLis=$this->commonLis();

        if (!is_logged()) {
            $signInLi = '<li><a href="' . SITE_URL . '/login">' . langText('sign_in') . '</a></li>';
            $signUpLi = '<li><a href="' . SITE_URL . '/register">Register</a></li>';
            $otherLis=$commonLis;
        } else {
            $signOutLi = '  <li><a href="#specialannouncements" onclick="signOut()" class="text-capitalize">Logout</a></li>';
            $nameLi = $this->nameLi(false, '#custom-section');
            
            if (!isAdmin()) {
//                $schoolLi = '   <li><a href="#school-section">School</a></li>';
                $schoolLi = '   <li><a href="' . SITE_URL . '/school">School</a></li>';
                $otherLis = $commonLis;
            } else {
                $otherLis = '<li><a href="javascript:void(0)" onclick="loadMainContent(\'adminSettings\')">Admin</a></li>';
            }
        }


        $markup = '';
        $markup .= '<ul class="sidebar-nav">
                        <a id="menu-close" href="#" class="btn btn-light btn-lg pull-right toggle"><i class="fa fa-times"></i></a>
                        <li class="sidebar-brand">
                        </li>';
        $markup .= $signInLi;
        $markup .= $signUpLi;
        $markup .= $nameLi;
        $markup .= $schoolLi;
        $markup .= $otherLis;
        $markup .= $signOutLi;
        $markup .= '</ul>';


        return $markup;
    }

}

?>