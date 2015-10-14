<?php

/*
 * hold footer ui functions
 */
require_once "base/ui.user.base.class.php";

class uiUser extends uiUserBase {

    public function __construct() {
        parent::__construct();
    }

    protected function signUpSignInLinkRow($rowClass = '') {
        $buttonRow = '<div class="row sign-up-sing-in-link-row- ' . $rowClass . '"> 
                        <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div> 
                        <div class="col-xs-12 col-sm-10 col-md-12 col-lg-12  ">
                            <h5>' . langText('already_signed_up_q') . ' <a href="javascript:void(0)" onclick="loadMainContent(\'signInPage\');">' . langText('sign_in_instead') . '</a></h5>
                        </div>
                      </div>';

        return $buttonRow;
    }

    /**
     * return markup for sign up page 
     *  
     * @param array $options can be used to pass any data needed
     */
    public function signUpPageMrkup($options = array()) {

        $nameRow = $this->signUpNameRow('', $options);
        $emailRow = $this->signUpEmailRow('', '', $options);
        $passwordRow = $this->signUpPasswordRow('', $options);
        $passConfRow = $this->signUpPass2Row('', $options);
        $usernameRow = $this->signUpUsernameRow();
        $recaptcharRow = $this->signUpCaptcharRow('', $options);
        $buttonRow = $this->signUpButtonRow();
        $signInLinkRow = $this->signUpSignInLinkRow();
        $schoolAndGradeRow = $this->signUpSchoolAndGradeRow('', $options);

        //build sign up page elements as rows
        $rows = '';
        $rows .= $nameRow;
        $rows .= $schoolAndGradeRow;
        $rows .= $emailRow;
        $rows .= $usernameRow;
        $rows .= $passwordRow;
        $rows .= $passConfRow;
        $rows .= $recaptcharRow;
        $rows .= $buttonRow;
        $rows .= $signInLinkRow;

//        //sign up head
//        $mMarckup = $this->signUpHead();
        //get content of sign up page
        $mMarckup = $this->signUpContentContainer($rows);


        return $mMarckup;
    }

    protected function signUpHead($rowClass = '') {
        $mMarckup = '<div class="container-fluid sign-up-header ' . $rowClass . '">
                        <div class="row">
                            <div class="col-sm-3 col-md-4 col-lg-4 hidden-xs">
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <h2 class="text-center">' . langText('sign_up') . '</h2>
                                <hr class="small">
                            </div>
                            <div class="col-sm-3 col-md-4 col-lg-4 hidden-xs">
                            </div>
                        </div>
                    </div><!-- ./container-fluid -->';

        return $mMarckup;
    }

    protected function signUpContentContainer($rows) {
        $mMarckup = '<div class="container-fluid">';
        $mMarckup .= '<div class="row sign-up-container">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">                            
                            <div class="row sign-up-content">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="col-sm-2 col-md-3 col-lg-3 hidden-xs">
                                    </div>      
                                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                                        <h1 class="text-uppercase pull-left coal-text">coal</h1>
                                    </div>                                                              
                                    <div class="col-xs-12 col-sm-5 col-md-4 col-lg-4 sign-up-holder-col">
                                        <form method="post" action="" id="fmSignUp" onsubmit="return false;" accept-charset="UTF-8">
                                            ' . $rows . '
                                        </form>
                                    </div>
                                    <div class="col-sm-3 col-md-3 col-lg-3 hidden-xs">
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>';
        $mMarckup .= '</div><!-- ./container-fluid -->';
        return $mMarckup;
    }

    /**
     * return append for verfication confirmation message in website
     */
    public function verficationConfirmAppend($pageUrl = '') {
        $append = '<br>' . langText('access_your_profile_page', array('URL' => $pageUrl));
        return $append;
    }

    protected function signUpUsernameRow($rowClass = '') {

        $usernameRow = '';
        if (USERNAME) {
            $usernameRow = '<div class="row sing-up-row-username ' . $rowClass . '">
                                <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div>
                                <div class="col-xs-12 col-sm-10 col-md-12 col-lg-12  ">
                                    <div class="form-group">
                                        <!--<label for="su_username">' . langText('username') . ' <small><span class="glyphicon glyphicon glyphicon-asterisk required"></span></small></label>-->
                                        <div class="form-group">                                      
                                            <input type="text" class="form-control" maxlength="50" placeholder="' . langText('username') . '" id="su_username" name="su_username">
                                        </div>
                                    </div> 
                                </div>
                            </div>';
        }

        return $usernameRow;
    }

    /**
     * return markup for sign In page 
     * 
     * note: request invitaion and sign up options (twiter,FB etc) are hidden for now untill they are done
     */
    public function signInPageMrkup() {

        $userOrEmailText = langText('username_or_email');
        if (!USERNAME)
            $userOrEmailText = langText('email');

        $resetPassModel = $this->resetPassModelMrkup();
        $mMarckup ='';
/*
        $mMarckup = ' <div class="container-fluid sign-in-header">
                        <div class="row">
                            <div class="col-sm-3 col-md-4 col-lg-4 hidden-xs">
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <h2 class="text-center">' . langText('sign_in') . '</h2>
                                <hr class="small">
                            </div>
                            <div class="col-sm-3 col-md-4 col-lg-4 hidden-xs">
                            </div>
                        </div>
                      </div><!-- ./container-fluid -->';
        */
        $mMarckup .= '<div class="container-fluid">';
        $mMarckup .= '<div class="row sign-in-container">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">                            
                            <div class="row sign-in-content">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="col-sm-2 col-md-3 col-lg-3 hidden-xs">
                                    </div>      
                                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                                        <h1 class="text-uppercase pull-left coal-text">coal</h1>
                                    </div> 
                                    <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 sign-in-holder-col">
                                        <form method="post" action="" id="fmSignIn" onsubmit="return false;" accept-charset="UTF-8">
                                            <div class="row">
                                                <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div>
                                                <div class="col-xs-12 col-sm-10 col-md-12 col-lg-12  ">
                                                    <div class="form-group">
                                                        <!--<label for="login_email_or_username">' . $userOrEmailText . ' <small><span class="glyphicon glyphicon glyphicon-asterisk required"></span></small></label>-->
                                                        <input class="form-control" type="text" maxlength="75" placeholder="' . strtolower($userOrEmailText) . '" id="login_email_or_username" name="login_email_or_username">                                
                                                    </div>                            
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div>
                                                <div class="col-xs-12 col-sm-10 col-md-12 col-lg-12  ">
                                                    <div class="form-group">
                                                        <!--<label for="login_password">' . langText('password') . ' <small><span class="glyphicon glyphicon glyphicon-asterisk required"></span></small></label>-->
                                                        <input class="form-control" type="password" placeholder="' . strtolower(langText('password')) . '" id="login_password" name="login_password">                                
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row remember-me-row">
                                                <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div>
                                                <div class="col-xs-12 col-sm-10 col-md-12 col-lg-12  ">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" name="login_rememberme" id="login_rememberme" value="1"> ' . langText('remember_me') . '
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row"> 
                                                <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div> 
                                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  ">
                                                    <button type="button" class="btn btn-default btn-sm pull-left text-capitalize col-xs-12 col-sm-12 col-md-12 col-lg-12" id="sign-in" onclick="signIn();" >' . langText('sign_in') . '</button>                                        
                                                </div>
                                           </div>
                                           <div class="row"> 
                                                <!--<div class="clearfix hidden-xs hidden-md hidden-lg"></div>
                                                <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div>-->
                                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 forgot-pass-box ">
                                                    <a href="javascript:void(0)" onclick="resetPassword(\'view\')">' . langText('forgot_password') . '?</a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4 hidden-xs">
                                    </div>                                
                                </div>
                            </div>
                        </div>
                    </div>';
        $mMarckup .= '</div><!-- ./container-fluid -->';
        $mMarckup .=$resetPassModel;

        return $mMarckup;
    }

    /**
     * return school Select element
     * 
     *
     * @param array $schools schools array
     * @param int $schoolId school Id to poulate 
     * @param string $name element name
     * @param string $id element id
     * @param string $extraAttributes extra attribute ex: set to 'disable="disable"'
     * @param string $onchange javascript function/s to execute on change
     * @param string $class class/es
     * @param string $style style codes
     * @return string
     */
    public function schoolSelect($schools, $schoolId = 0, $name = '', $id = '', $extraAttributes = '', $onchange = '', $class = '', $style = '') {

        $optMarkup = '';
        //empty option
        $optMarkup.=$this->htmlSelectOptions('','select school','','','','empty_option','');
        
        if (!empty($schools)) {
            foreach ($schools as $opt) {
                $optSelected = '';
                if ($schoolId == $opt['id'])
                    $optSelected .= 'selected';

                $optMarkup.=$this->htmlSelectOptions($this->et($opt['id']), $this->et($opt['name']), '', '', $optSelected);
            }
        }
        $select = $this->htmlSelect($optMarkup, $name, $id, $extraAttributes, $onchange, $class, $style);

        return $select;
    }

    /**
     * return grade Select element
     * 
     *
     * @param array $grades grades array
     * @param int $gradeId grade Id to poulate 
     * @param string $name element name
     * @param string $id element id
     * @param string $extraAttributes extra attribute ex: set to 'disable="disable"'
     * @param string $onchange javascript function/s to execute on change
     * @param string $class class/es
     * @param string $style style codes
     * @return string
     */
    public function gradeSelect($grades, $gradeId = 0, $name = '', $id = '', $extraAttributes = '', $onchange = '', $class = '', $style = '') {

        $optMarkup = '';
        //empty option
        $optMarkup.=$this->htmlSelectOptions('','select grade','','','','empty_option','');
        if (!empty($grades)) {
            foreach ($grades as $val => $opt) {
                $optSelected = '';
                if ($gradeId == $val)
                    $optSelected .= 'selected';

                $optMarkup.=$this->htmlSelectOptions($this->et($val), $this->et($opt), '', '', $optSelected);
            }
        }
        $select = $this->htmlSelect($optMarkup, $name, $id, $extraAttributes, $onchange, $class, $style);

        return $select;
    }

    /**
     * return sign Up School And Grade Row
     * 
     * @param string $rowClass classes set to row
     * @param array $options can be used to pass any data needed
     * @return string
     */
    protected function signUpSchoolAndGradeRow($rowClass = '', $options = array()) {
        $row = '';
        if (isset($options['schoolSelect']) && !empty($options['schoolSelect']) && isset($options['gradeSelect']) && !empty($options['gradeSelect'])) {
            $row .= '<div class="row sign-up-row-school-grade ' . $rowClass . '">
                        <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div>
                        <div class="col-xs-12 col-sm-10 col-md-6 col-lg-6  ">
                            <div class="form-group">
                                <!--<label for="school">' . langText('school') . ' <small><span class="glyphicon glyphicon glyphicon-asterisk required"></span></small></label>-->
                                ' . $options['schoolSelect'] . '
                            </div>                            
                        </div>
                        <div class="clearfix hidden-xs hidden-md hidden-lg"></div>
                        <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div>
                        <div class="col-xs-12 col-sm-10 col-md-6 col-lg-6  ">
                            <div class="form-group">
                               <!-- <label for="grade">' . langText('grade') . ' <small><span class="glyphicon glyphicon glyphicon-asterisk required"></span></small></label>-->
                                ' . $options['gradeSelect'] . '
                            </div>                            
                        </div>
                    </div>';
        }

        return $row;
    }

}

?>