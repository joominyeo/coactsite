<?php

/*
 * hold footer ui functions
 */
require_once "ui/ui.common.class.php";

class uiUserBase extends uiCommon {

    public function __construct() {
        parent::__construct();
    }

    /**
     * return email Verification Sent Markup to show in notification
     */
    public function emailVerificationSentMarkup($resend, $userId, $name, $api = false) {
        $typeNote = '';
        if (!$resend)
            $typeNote .= langText('thank_you_signing_up', array('NAME' => $name));
        else
            $typeNote .= langText('verifications_email_resent');

        $resText = $typeNote;
        if (!$api)
            $resText .=' <button type="submit" onclick="resendv(' . $userId . ',\'signup\')" class="btn  btn-xs btn-info">' . langText('resend_verification') . '</button>&nbsp;<br>';
        $resText .=langText('verifications_finalize_your_registration');

        return $resText;
    }

    /**
     * return verification Email Content
     */
    public function verificationEmailContent($subject, $url, $resend, $name = '', $quote = '') {
        $message = '';

        $message .='<p>' . langText('verifications_email_content_sign_up', array('URL' => $url)) . '</p>';

        $emailBody = $this->emailBody($message, $subject, $name, '', true, $subject, $quote);
        return $emailBody;
    }

    /*
     * return verification Email2 Content when change current email
     */

    public function verificationEmail2Content($subject, $url, $email, $name = '') {
        $message = '<p><b>You have requested to change your ' . DOMIN_NAME . ' email address to ' . $email . '.</b> <br><br>' .
                'Once confirmation of email address is successful, ' . $email .
                ' will be set as your active email address and old email address will be removed.<br><br>' .
                'Please click on this <a href="' . $url . '">link</a> to verify your email address or' .
                ' copy paste below url to your Browse address bar.</p>' . $url;

        $emailBody = $this->emailBody($message, $subject, $name, '', true, SITE_NAME . $subject, '');
        return $emailBody;
    }

    /*
     * return email2 Verification Sent Markup for change current email
     */

    public function email2VerificationSentMarkup($resend, $email) {
        $typeNote = '';
        if (!$resend)
            $typeNote .= 'sent';
        else
            $typeNote .= 'resent';

        return 'Verification email has been ' . $typeNote . ' to ' . $email . '.<br>' .
                ' Please check your inbox and follow instruction in the email.';
    }

    /*
     * return email Rest Password Link markup to show after email reset link sent
     */

    public function emailRestPasswordLinkmarkup() {
        return langText('reset_password_link_sent');
    }

    /**
     * return rest Password Link Content
     */
    public function restPasswordLinkContent($subject, $url, $username, $name = '') {
        $message = '<p>' . langText('reset_password_email_messsage', array('URL' => $url)) . '<p>';
        if (USERNAME)
            $message .='<p> ' . langText('your_username') . ' : <b>' . $username . '</b></p>';

        $emailBody = $this->emailBody($message, $subject, $name, '', true, SITE_NAME . ' ' . $subject, '');
        return $emailBody;
    }

    /**
     * return verified Email confirmation Content
     */
    public function verifiedEmailConfirmationContent($subject, $username, $name = '') {
        $message = '<p> ' . langText('email_verified_successfully') . '</p>';
        if (USERNAME)
            $message.='<p> ' . langText('your_username') . ' : <b>' . $username . '</b></p>';

        $emailBody = $this->emailBody($message, $subject, $name, '', true, SITE_NAME . ' ' . $subject, '');
        return $emailBody;
    }

    /**
     * message to show in website after verify sign up email
     * @param type $name
     * @return string
     */
    public function verifiedConfirmationNote($name = '', $append = '') {

        $marckup = '' . langText('thank_you_confirming_registration', array('NAME' => $name, 'APPEND' => $append)) . '';

        return $marckup;
    }

    /**
     * return append for verfication confirmation message in website
     */
    public function verficationConfirmAppend($pageUrl = '') {
        $append = '';
        return $append;
    }

    /**
     * for invited user we do not send verfication email
     * so we just send welcome email
     */
    public function verifiedUserWelcomeContent($subject, $username, $name = '') {
        $signInLink = '<a href="' . SITE_URL . '/signin">' . langText('sign_in') . '</a>';

        $message = langText('welcome_to', array('SITE_NAME' => SITE_NAME)) . ' ' . $signInLink . '.';
        if (USERNAME)
            $message.='<br> ' . langText('your_username') . ' : <b>' . $username . '</b>';

        $emailBody = $this->emailBody($message, $subject, $name, '', true, SITE_NAME . ' ' . $subject, '');
        return $emailBody;
    }

    /**
     * return verified Email2 confirmation Content when change email done
     */
    public function verifiedEmail2ConfirmationContent($subject, $email2, $username, $name = '') {
        $signInLink = '<a href="' . SITE_URL . '/signin">Sign in</a>';

        $message = '<br><br> Your email address ' . $email2 . ' has been verified successfully. Please ' . $signInLink . '.';
        if (USERNAME)
            $message.='<br> Your Username : <b>' . $username . '</b>';

        $emailBody = $this->emailBody($message, $subject, $name, '', true, SITE_NAME . ' ' . $subject, '');
        return $emailBody;
    }

    /**
     * return reset Pass popup Markup html
     * 
     * note: since we add this as js scrip we have to do each line sperately
     */
    public function resetPassMarkup() {
        $markup = ' <div id="rest-pass-edit-pop" style="">';
        $markup .= '    <div class="row">';
        $markup .= '        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">';
        $markup .= '            <p>' . langText('enter_new_password_and_save') . '</p>';
        $markup .= '        </div>';
        $markup .= '    </div>';
        $markup .= '    <div class="row">';
        $markup .= '        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 no-left-padding-col">';
        $markup .= '            <label for="rnpassword" class="">' . langText('new_password') . '</label>';
        $markup .= '        </div>';
        $markup .= '        <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7 no-right-padding-col">';
        $markup .= '           <div class="form-group">';
        $markup .= '               <input type="password" class="form-control" id="rnpassword" name="rnpassword" placeholder="' . langText('new_password') . '">';
        $markup .= '            </div>';
        $markup .= '        </div>';
        $markup .= '    </div>';
        $markup .= '    <div class="row">';
        $markup .= '        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 no-left-padding-col">';
        $markup .= '            <label for="rcpassword" class="">' . langText('confirm_password') . '</label>';
        $markup .= '        </div>';
        $markup .= '         <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7 no-right-padding-col">';
        $markup .= '            <div class="form-group">';
        $markup .= '                <input type="password" class="form-control" id="rcpassword" name="rcpassword" placeholder="' . langText('confirm_password') . '">';
        $markup .= '            </div>';
        $markup .= '         </div>';
        $markup .= '     </div>';
        $markup .= '     <div class="row">';
        $markup .= '         <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">';
        $markup .= '             <button type="button" id="restpass_btn" class="btn  btn-default btn-sm pull-right" onclick="resetPassword(\\\'setpass\\\')">' . langText('save') . '</button>';
        $markup .= '         </div>';
        $markup .= '      </div>';
        $markup .= '   </div>';

        return $markup;
    }

    /*
     * return note content html for verify new email 
     */

    public function verifyNewEmailNote($userId, $resend = false) {
        $resendvBtn = '<button type="submit" id="resendcmailv_btn" onclick="resendCmailV(' . $userId . ')" class="btn  btn-xs btn-default" style="">Resend Verification Email</button>';
        $removeBtn = '<button type="submit" id="removecmailv_btn" onclick="removeCmailV(' . $userId . ')" class="btn  btn-xs btn-default" style="">Remove New Email</button>';
        $note = '<p style="text-align: justify;">Verification email has been ';
        if (!$resend)
            $note .= 'sent ';
        else
            $note .= 'resent ';
        $note .= 'to new email address and please check your inbox and verify your email' .
                ' address.</p>' . $resendvBtn . ' OR ' . $removeBtn;
        $note .= '<br><p style="text-align: justify;margin-top: 0.6em;">Once verification of email address is successful, erangawiharagoda@gmail.com will be set as' .
                ' your active email address and old email address will be removed.</p>';
        return '<div class="row" id="mail_newnote_row">     
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mail_newnote_col"><p style="text-align: justify;">Note:</p>' . $note . '</p></div>                            
                </div>';
    }

    /**
     * return support Reqest To Admin Email content
     */
    public function supportReqestToAdminEmail($subject, $fromEmail, $name, $description, $title) {
        $message = '<br>
                    Request Information:
                    <br>
                    <br><b> from Name : </b> ' . $this->et($name) . '
                    <br><b> from Email : </b> ' . $this->et($fromEmail) . '
                    <br><b> Subject: </b>' . $title . '
                    <br><b> Description: </b>' . nl2br($this->et($description));

        $emailBody = $this->emailBody($message, $subject, '', '', true, SITE_NAME . ' ' . $subject, '');

        return $emailBody;
    }

    /**
     * return support Reqest To user Email content, this is not used now to stop spamers
     * But for sign in users we can use this with using there email from settings
     * 
     * @param type $subject
     * @param type $fromEmail
     * @param type $name
     * @param type $description
     * @param type $title
     * @return type
     */
    public function supportReqestToUserEmail($subject, $fromEmail, $name, $description, $title) {
        $message = '<br>
                    We have received your request and will get back to you shortly.<br>
                    <br>
                    Request Information:<br>
                    <br><b> Subject: </b>' . $this->et($title) . '
                    <br><b> from Name : </b> ' . $this->et($name) . '
                    <br><b> From Email: </b>' . $this->et($fromEmail) . '                    
                    <br><b> Description: </b>' . nl2br($this->et($description));

        $emailBody = $this->emailBody($message, $subject, $name, '', true, SITE_NAME . ' ' . $subject, '');

        return $emailBody;
    }

    /*
     * return lock User Email Content 
     */

    public function lockUserEmailContent($subject, $name = '') {
        $message = '<p>Your ' . DOMIN_NAME . ' account has been locked out temporally due to continues failed sign in attempts.</p>'
                . '<p>Please try again to sign in after ' . LOGIN_LOCKOUT_TIME . ' minutes.</p>';

        $emailBody = $this->emailBody($message, $subject, $name, '', true, SITE_NAME . ' ' . $subject, '');
        return $emailBody;
    }

    protected function signUpUsernameRow($rowClass = '') {

        $usernamePrepend = SITE_URL;
        //to view clearly use shot name in local host
        if (LOCAL_DEV)
            $usernamePrepend = 'sitename';

        $usernameRow = '';
        if (USERNAME) {
            $usernameRow = '<div class="row sing-up-row-username ' . $rowClass . '">
                                <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div>
                                <div class="col-xs-12 col-sm-10 col-md-12 col-lg-12  ">
                                    <div class="form-group">
                                        <label for="su_username">' . langText('username') . ' <small><span class="glyphicon glyphicon glyphicon-asterisk required"></span> Used in public profile url and not allowed to change later.</small></small></label>
                                        <div class="input-group">
                                            <div class="input-group-addon">' . $usernamePrepend . '/</div>                                        
                                            <input type="text" class="form-control" maxlength="50" placeholder="' . langText('username') . '" id="su_username" name="su_username">
                                        </div>
                                    </div> 
                                </div>
                            </div>';
        }

        return $usernameRow;
    }

    protected function signUpNameRow($rowClass = '', $options = array()) {
        $nameRow = '';
        if (SIGNUP_NAME) {
            $fname = $lname = '';
            if (!empty($options['data'])) {
                if (!empty($options['data']['fname']))
                    $fname = $options['data']['fname'];
                if (!empty($options['data']['lname']))
                    $lname = $options['data']['lname'];
            }

            $nameRow = '<div class="row sing-up-row-name ' . $rowClass . '">
                            <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div>
                            <div class="col-xs-12 col-sm-10 col-md-6 col-lg-6  ">
                                <div class="form-group">
                                    <!--<label for="fname">' . langText('first_name') . ' <small><span class="glyphicon glyphicon glyphicon-asterisk required"></span></small></label>-->
                                    <input type="text" value="' . $fname . '" class="form-control" maxlength="50" placeholder="' . langText('first_name') . '" id="fname" name="fname">
                                </div>                            
                            </div>
                            <div class="clearfix hidden-xs hidden-md hidden-lg"></div>
                            <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div>
                            <div class="col-xs-12 col-sm-10 col-md-6 col-lg-6  ">
                                <div class="form-group">
                                    <!--<label for="lname">' . langText('last_name') . ' <small><span class="glyphicon glyphicon glyphicon-asterisk required"></span></small></label>-->
                                    <input type="text" value="' . $lname . '" class="form-control" maxlength="50" placeholder="' . langText('last_name') . '" id="lname" name="lname">
                                </div>                            
                            </div>
                        </div>';
        }

        return $nameRow;
    }

    protected function signUpPass2Row($rowClass = '', $options = array()) {
        $passConfRow = '';

        if (SIGNUP_PASS2) {
            $cpassword = '';
            if (!empty($options['data'])) {
                if (!empty($options['data']['cpassword']))
                    $cpassword = $options['data']['cpassword'];
            }

            $passConfRow = '<div class="row sing-up-row-pass2 ' . $rowClass . '">
                                <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div>
                                <div class="col-xs-12 col-sm-10 col-md-12 col-lg-12  ">
                                    <div class="form-group">
                                        <!--<label for="cpassword">' . langText('confirm_password') . ' <small><span class="glyphicon glyphicon glyphicon-asterisk required"></span></small></label>-->
                                        <input class="form-control" value="'.$cpassword.'" type="password"  placeholder="' . langText('confirm_password') . '" id="cpassword" name="cpassword">
                                    </div>                
                                </div>
                            </div>';
        }

        return $passConfRow;
    }

    protected function signUpCaptcharRow($rowClass = '', $options = array()) {
        $recaptcharRow = '';


        if (SIGNUP_CAPTCHAR) {
            $recaptcha = '';
            $recaptcharClass = '';
            if (SIGNUP_CAPTCHAR_VERSION == 1) {
                $recaptcha .= $this->getRecaptchaIframe();
            } else {
                $recaptcha .= $this->getRecaptchaDiv();
                $recaptcharClass = 'recaptchar-v2-wraper';
            }
            $recaptcharRow = '<div class="row sing-up-row-captchar ' . $rowClass . '">
                                <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div>
                                <div class="col-xs-12 col-sm-10 col-md-12 col-lg-12  no-padding-col ' . $recaptcharClass . '" id="site_recaptcha_col" style="max-width:100%;overflow: auto;">
                                    ' . $recaptcha . '   
                                </div>
                              </div>';
        }

        return $recaptcharRow;
    }

    protected function signUpHead($rowClass = '') {
        $mMarckup = '<div class="container-fluid sign-up-header ' . $rowClass . '">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 hidden-xs hidden-sm">
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                                <span class="text-capitalize">' . langText('sign_up') . '</span>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 hidden-xs hidden-sm">
                            </div>
                        </div>
                    </div><!-- ./container-fluid -->';

        return $mMarckup;
    }

    protected function signUpEmailRow($rowClass = '', $labelClass = '', $options = array()) {
        $email = '';
        if (!empty($options['data'])) {
            if (!empty($options['data']['email']))
                $email = $options['data']['email'];
        }

        $emailRow = '<div class="row sign-up-row-email ' . $rowClass . '">
                        <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div>
                        <div class="col-xs-12 col-sm-10 col-md-12 col-lg-12  ">
                            <div class="form-group">
                                <!--<label class="' . $labelClass . '" for="email">' . langText('email') . ' <small><span class="glyphicon glyphicon glyphicon-asterisk required"></span></small></label>-->
                                <input class="form-control" value="' . $email . '" type="text"  maxlength="75" placeholder="' . langText('email') . '" id="email" name="email">
                            </div>                            
                        </div>
                    </div>';

        return $emailRow;
    }

    protected function signUpPasswordRow($rowClass = '', $options = array()) {
        $password = '';
        if (!empty($options['data'])) {
            if (!empty($options['data']['password']))
                $password = $options['data']['password'];
        }

        $passwordRow = '<div class="row sign-up-row-password ' . $rowClass . '">
                            <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div>
                            <div class="col-xs-12 col-sm-10 col-md-12 col-lg-12  ">
                                <div class="form-group">
                                    <!--<label for="password">' . langText('password') . ' <small><span class="glyphicon glyphicon glyphicon-asterisk required"></span></small></label>-->
                                    <input class="form-control" value="' . $password . '" type="password" placeholder="' . langText('password') . '" id="password" name="password">
                                </div>
                            </div>
                        </div>';

        return $passwordRow;
    }

    protected function signUpButtonRow($rowClass = '') {
        $buttonRow = '<div class="row sign-up-row-button ' . $rowClass . '"> 
                        <div class="hidden-xs hidden-sm hidden-md hidden-lg"></div> 
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  ">
                            <button type="button" class="btn btn-default btn-sm pull-left text-capitalize col-xs-12 col-sm-12 col-md-2 col-lg-12" id="sign-up" onclick="signUp();" >' . langText('sign_up') . '</button>                                        
                        </div>
                      </div>';

        return $buttonRow;
    }

    protected function signUpContentContainer($rows) {
        $mMarckup = '<div class="container-fluid">';
        $mMarckup .= '<div class="row sign-up-container">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">                            
                            <div class="row sign-up-content">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 hidden-xs hidden-sm">
                                    </div>                                                             
                                    <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 sign-up-holder-col">
                                        <form method="post" action="" id="fmSignUp" onsubmit="return false;" accept-charset="UTF-8">
                                            ' . $rows . '
                                        </form>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 hidden-xs hidden-sm">                                        
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 hidden-xs hidden-sm">
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>';
        $mMarckup .= '</div><!-- ./container-fluid -->';
        return $mMarckup;
    }

    /**
     * return markup for sign up page 
     * 
     * @param array $options can be used to pass any data needeed
     */
    public function signUpPageMrkup($options = array()) {


        $nameRow = $this->signUpNameRow();
        $emailRow = $this->signUpEmailRow();
        $passwordRow = $this->signUpPasswordRow();
        $passConfRow = $this->signUpPass2Row();
        $usernameRow = $this->signUpUsernameRow();
        $recaptcharRow = $this->signUpCaptcharRow();
        $buttonRow = $this->signUpButtonRow();

        //build sign up page elements as rows
        $rows = $nameRow;
        $rows .=$emailRow;
        $rows .= $passwordRow;
        $rows .=$passConfRow;
        $rows .=$usernameRow;
        $rows .=$recaptcharRow;
        $rows .=$buttonRow;

        //sign up head
        $mMarckup = $this->signUpHead();
        //get content of sign up page
        $mMarckup .= $this->signUpContentContainer($rows);


        return $mMarckup;
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


        $mMarckup = ' <div class="container-fluid sign-in-header">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 hidden-xs hidden-sm">
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                                <span class="text-capitalize">' . langText('sign_in') . '</span>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 hidden-xs hidden-sm">
                            </div>
                        </div>
                      </div><!-- ./container-fluid -->';
        $mMarckup .= '<div class="container-fluid">';
        $mMarckup .= '<div class="row sign-in-container">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">                            
                            <div class="row sign-in-content">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 hidden-xs hidden-sm">
                                    </div>                                                            
                                    <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 sign-in-holder-col">
                                        <form method="post" action="" id="fmSignIn" onsubmit="return false;" accept-charset="UTF-8">
                                            <div class="row">
                                                <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div>
                                                <div class="col-xs-12 col-sm-10 col-md-12 col-lg-12  ">
                                                    <div class="form-group">
                                                        <label for="login_email_or_username">' . $userOrEmailText . ' <small><span class="glyphicon glyphicon glyphicon-asterisk required"></span></small></label>
                                                        <input class="form-control" type="text" maxlength="75" placeholder="' . $userOrEmailText . '" id="login_email_or_username" name="login_email_or_username">                                
                                                    </div>                            
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div>
                                                <div class="col-xs-12 col-sm-10 col-md-12 col-lg-12  ">
                                                    <div class="form-group">
                                                        <label for="login_password">' . langText('password') . ' <small><span class="glyphicon glyphicon glyphicon-asterisk required"></span></small></label>
                                                        <input class="form-control" type="password" placeholder="' . langText('password') . '" id="login_password" name="login_password">                                
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
                                                <div class="col-xs-12 col-sm-10 col-md-5 col-lg-5  ">
                                                    <button type="button" class="btn btn-default btn-sm pull-left text-capitalize col-xs-8 col-sm-6 col-md-10 col-lg-10" id="sign-in" onclick="signIn();" >' . langText('sign_in') . '</button>                                        
                                                </div>
                                                <div class="clearfix hidden-xs hidden-md hidden-lg"></div>
                                                <div class="col-sm-1 hidden-xs hidden-md hidden-lg"></div>
                                                <div class="col-xs-12 col-sm-10 col-md-6 col-lg-6 forgot-pass-box ">
                                                    <a href="javascript:void(0)" onclick="resetPassword(\'view\')">' . langText('forgot_password') . '?</a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 hidden-xs hidden-sm">
                                       
                                    </div> 
                                    <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 hidden-xs hidden-sm">
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
     * return markup privacy policy
     */
    public function privacyPolicy() {
        $mMarckup = ' <div class="container-fluid privacy-policy-container">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <h3>Privacy Policy</h3>
                            </div>
                        </div>
                      </div><!-- ./container-fluid -->';

        return $mMarckup;
    }

    /**
     * return markup about us page
     */
    public function aboutUs() {
        $mMarckup = ' <div class="container-fluid about-us-container">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <h3>About Us</h3>
                            </div>
                        </div>
                      </div><!-- ./container-fluid -->';

        return $mMarckup;
    }

    /**
     * return markup terms of service page
     */
    public function termsOfService() {
        $mMarckup = ' <div class="container-fluid terms-of-service-container">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <h3>Terms of Service</h3>
                            </div>
                        </div>
                      </div><!-- ./container-fluid -->';

        return $mMarckup;
    }

    /**
     * Quick Start Instructions markup
     */
    public function quickStart() {
        $final_markup = '<div class="container-fluid quick-start-container hide">';
        $final_markup .= '<div class="row quick-start-row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
                                <div id="quickStartPopup" title="Quick Start Instructions">
                                    <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
                                    ' . $this->quickStartText(false) . '
                                    </div>
                                    </div>
                                </div>
                            </div>
                          </div>';
        $final_markup .= '</div><!-- ./container-fluid -->';

        return $final_markup;
    }

    /**
     * return markup for rest password model 
     */
    protected function resetPassModelMrkup() {
        $userOrEmailText = langText('email');
        if (USERNAME)
            $userOrEmailText = langText('username_or_email');

        $mMarckup = '<div id="resetPass" style="display: none;">                          
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                                <p>' . langText('enter_your_registered_email') . '</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 no-left-padding-col"><label>' . $userOrEmailText . '</label></div>
                            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 no-right-padding-col">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="' . $userOrEmailText . '" id="rpass_username_email">
                                </div>
                            </div>
                        </div>
                        <div class="row">    
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col state-col">
                                <button type="button" class="btn btn-default  btn-sm pull-right" onclick="resetPassword(\'reqestemail\')">' . langText('next') . '</button>
                                <!--<button type="button" class="btn btn-default  btn-sm pull-right" style="margin-right: 10px;"  onclick="$.fancybox.close(true);">' . langText('close') . '</button>-->
                            </div>
                        </div>
                    </div>';
        return $mMarckup;
    }

}

?>