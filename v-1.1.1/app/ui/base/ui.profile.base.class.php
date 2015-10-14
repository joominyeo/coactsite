<?php

/*
 * hold footer ui functions
 */
require_once "ui/ui.common.class.php";

class uiProfileBase extends uiCommon {

    public function __construct() {
        parent::__construct();
    }

    /**
     * return general Settings Tab Tab html
     */
    public function generalSettings($basicSettings, $usernameSettings, $emailSettings, $passwordSettings) {
        $markup = '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 general-settings-content">';
        $markup .= '    <div class="row genset-in-header">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <span class="text-capitalize">'.langText('profile_user_settings').'</span>
                                <!-- <hr class="h-line-title"> -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                ' . $basicSettings . '
                                ' . $usernameSettings . '
                                ' . $emailSettings . '
                                ' . $passwordSettings . '
                            </div>
                        </div>';
        $markup .='</div>';

        return $markup;
    }

    /**
     * return profile settings Form ui html
     */
    public function settingsForm($panel, $markup) {
        $markupForm = '<div class="row gen-set-' . $panel . '-cnt">';
        $markupForm .=' <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">';
//        if ($panel == 'username' || $panel == 'email' || $panel == 'pass')
//            $markupForm .='     <div class="row">
//                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
//                                    <hr class="h-line">
//                                </div>
//                            </div>';
        switch ($panel) {
            case 'basic':
                $markupForm .='
                            <div class="row sub-title-row">
                                    <ol class="breadcrumb bg-primary">
                                        <li class="active">'.langText('change_name').'</li>
                                    </ol>
                            </div>';
                break;
            case 'username':
                $markupForm .='
                            <div class="row sub-title-row">
                                    <ol class="breadcrumb bg-primary">
                                        <li class="active">'.langText('username_infor').'</li>
                                    </ol>
                            </div>';
                break;
            case 'email':
                $markupForm .='
                            <div class="row sub-title-row">
                                    <ol class="breadcrumb bg-primary">
                                        <li class="active">'.langText('change_email').'</li>
                                    </ol>
                            </div>';
                break;
            case 'pass':
                $markupForm .='
                            <div class="row sub-title-row">
                                    <ol class="breadcrumb bg-primary">
                                        <li class="active">'.langText('change_password').'</li>
                                    </ol>
                            </div>';
                break;
        }
        $markupForm .='     <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">';
        $markupForm .= '            <form id="fm_' . $panel . '" role="form" class="form-horizontal" accept-charset="UTF-8" onsubmit="return false;" action="" method="post">';
        $markupForm.= '             ' . $markup;
        $markupForm.='              </form>';
        $markupForm.='          </div>
                            </div>
                        </div>
                </div>';


        return $markupForm;
    }

    /*
     * return profile settings save button ui html
     */

    public function settingsSaveBtn($panel) {
        $btnColumClasses = 'col-xs-8 col-sm-8 col-md-8 col-lg-8';
        $btnSizeClasses = 'col-xs-6 col-sm-3 col-md-3 col-lg-3';
        $emptyColumn = 'col-xs-0 col-sm-4 col-md-4 col-lg-4 hidden-xs';

        $markup = ' <div class="form-group button-form-group">
                        <div class="' . $emptyColumn . '"> </div>
                        <div class="' . $btnColumClasses . '">
                            <button type="button" id="save_' . $panel . '" class="btn btn-sm btn-default pull-left ' . $btnSizeClasses . '" onclick="saveSettings(\'' . $panel . '\')">'.langText('save').'</button>
                        </div>
                    </div>';
        return $markup;
    }

    /*
     * return profile settings Password Panel ui html
     */

    public function settingsPasswordPanel() {
        $markup = ' <div class="form-group">     
                        <label for="password" >'.langText('current_password').'</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="'.langText('current_password').'">
                    </div>
                    <div class="form-group">     
                        <label for="npassword">'.langText('new_password').'</label>
                        <input type="password" class="form-control" id="npassword" name="npassword" placeholder="'.langText('new_password').'">
                    </div>
                    <div class="form-group">     
                        <label for="cpassword">'.langText('confirm_password').'</label>
                        <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="'.langText('confirm_password').'">
                    </div>';
        return $markup;
    }

    /*
     * return profile settings Email Panel ui html
     */

    public function settingsEmailPanel($email, $nemail) {
        $markup = ' <div class="form-group">     
                        <label for="email">'.langText('active_email').'</label>
                        <input type="text" class="form-control" value="' . $email . '" id="email" name="email" readonly="readonly" placeholder="'.langText('email_address').'">                        
                    </div>
                    <div class="form-group" id="mail_newemail_row">     
                        <label for="nemail">'.langText('new_email').'</label>
                        <input type="text" class="form-control" value="' . $nemail . '" id="nemail" maxlength="75" name="nemail" placeholder="'.langText('new_email_address').'">
                    </div>';
        $markup.= ' <div class="form-group" id="mail_pass_row">     
                        <label for="epassword">'.langText('password').'</label>
                        <input type="password" class="form-control" id="epassword" name="epassword" maxlength="64" placeholder="'.langText('password').'">
                    </div>';
        return $markup;
    }

    /*
     * return settings Username Panel html
     */

    public function settingsUsernamePanel($username) {
        $href = SITE_URL . '/' . $this->et($username);

        $markup = '<div class="form-group">';
        $markup.='  <label for="email" class="col-xs-12 col-sm-3 col-md-3 col-lg-2 control-label">'.langText('your_username').'</label>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        <label class="gen-infor-lable"><b>' . $this->et($username) . '</b></label>
                    </div>
                   </div>';
        if (!isAdmin()) {
            $markup.='<div class="form-group">
                        <label for="email" class="col-xs-12 col-sm-3 col-md-3 col-lg-2 control-label">'.langText('your_public_profile').'</label>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <label class="gen-infor-lable"><a href="' . $href . '"><b>' . $href . '</b></a></label>
                        </div>
                      </div>';
        }
        return $markup;
    }

    /**
     * return general settings basic Panel html
     */
    public function settingsBasicPanel($fname, $lname, $dropTzOpt) {
        //we show time zone settings if TIME_ZONE is ture only
        $timeZoneMarkup = '';
        if (TIME_ZONE) {
            $dropTz = $this->htmlSelect($dropTzOpt, 'timezone', 'timezone', '', '', 'input-medium form-control');
            $timeZoneMarkup.='<div class="form-group">     
                                <label  for="lname">'.langText('time_zone').'</label>
                                ' . $dropTz . '
                              </div>';
        }

        $markup = '   <div class="form-group">     
                        <label for="fname">'.langText('first_name').'</label>
                        <input type="text" class="form-control" maxlength="50" id="fname" name="fname" value="' . $fname . '" placeholder="'.langText('first_name').'">
                      </div>
                      <div class="form-group">     
                        <label  for="lname">'.langText('last_name').'</label>
                        <input type="text" class="form-control" maxlength="50" id="lname" name="lname" value="' . $lname . '" placeholder="'.langText('last_name').'">
                      </div>';
        $markup .=$timeZoneMarkup;

        return $markup;
    }

    /**
     * 
     * return profile page content html
     */
    public function profilePage($reload = false, $generalSettings = '') {
        $final_markup = '<div class="container-fluid profile-page-container">';
        $final_markup .= '<div class="row profile-page-row">';
        $final_markup .= '  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div id="profile-content-row" class="row">
                                ' . $generalSettings . '
                                </div>
                            </div>
                        </div>';
        $final_markup .= '</div><!-- ./container-fluid -->';


        if (!$reload)
            $final_markup .= '<script src="' . JS_URL . '/profile.js"></script>';

        return $final_markup;
    }

}

?>