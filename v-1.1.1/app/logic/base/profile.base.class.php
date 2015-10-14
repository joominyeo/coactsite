<?php

class profileBase {
    /*
     * variables
     */

    protected $userId;
    protected $db;
    protected $user;
    protected $ui;
    protected $res = array('status' => true, 'text' => '', 'error' => '');

    /**
     * set true if calls is from api
     * @var bool 
     */
    protected $api = false;

    public function __construct($userId = 0, $api = false) {
        require_once "logic/user.class.php";
        $this->db = getDbClassInstance();
        $this->userId = $userId;
        $this->api = $api;
        $this->user = new user($userId, $api);

        require_once "ui/ui.profile.class.php";
        $this->ui = new uiProfile();
    }

    /**
     * return markup for general settings content
     */
    protected function generalSettings() {
        $user = $this->user->getUser($this->userId);

        $basicSettings = $this->basicSettingsMarkup($user);
        $usernameSettings = '';
        if (USERNAME)
            $usernameSettings = $this->usernameSettingsMarkup($user);
        $emailSettings = $this->emailSettingsMarkup($user);
        $passwordSettings = $this->passwordSettingsMarkup();
        //get final html for general settings 
        $markup = $this->ui->generalSettings($basicSettings, $usernameSettings, $emailSettings, $passwordSettings);

        return $markup;
    }

    /**
     * reutrn basic settings markup for firstname, lastname, aboutme and timezone
     * 
     * @param array $user user data row
     */
    protected function basicSettingsMarkup($user) {
        $markup = '';
        $fname = $lname = '';
        if (!empty($user['fname']))
            $fname = $this->ui->et($user['fname']);

        if (!empty($user['lname']))
            $lname = $this->ui->et($user['lname']);

        $dropTzOpt = '';
        if (TIME_ZONE) {
            $timezones = DateTimeZone::listIdentifiers();
            foreach ($timezones as $timezone) {
                $dropTzOptSelected = '';
                if ($timezone == $user['php_tz_identifier'])
                    $dropTzOptSelected .= 'selected';

                $dropTzOpt.=$this->ui->htmlSelectOptions($this->ui->et($timezone), $this->ui->et($timezone), '', '', $dropTzOptSelected);
            }
        }

        //get general settings basic Panel html
        $markup.= $this->ui->settingsBasicPanel($fname, $lname, $dropTzOpt);
        //save button
        $markup .= $this->ui->settingsSaveBtn('basic');
        //get profile general settings Form ui html
        $markupForm = $this->ui->settingsForm('basic', $markup);

        return $markupForm;
    }

    /**
     * return username settings markup
     * 
     * @param array $user user data row
     */
    protected function usernameSettingsMarkup($user) {

        $markup = '';
        $username = '';
        if (!empty($user['username']))
            $username = $this->ui->et($user['username']);
        //get settings Username Panel html
        $markup .= $this->ui->settingsUsernamePanel($username);
        //get profile settings Form ui html
        $markupForm = $this->ui->settingsForm('username', $markup);

        return $markupForm;
    }

    /**
     * return email settings markup
     * 
     * @param array $user user data row
     */
    protected function emailSettingsMarkup($user) {


        $markup = '';
        $email = '';
        if (!empty($user['email']))
            $email = $this->ui->et($user['email']);
        $nemail = '';
        if (!empty($user['email2']))
            $nemail = $this->ui->et($user['email2']);

        //get profile settings Email Panel ui html
        $markup .= $this->ui->settingsEmailPanel($email, $nemail);
        //add verify new email note with resend verification options
        if ($nemail != '')
            $markup .= $this->user->verifyNewEmailNote($this->userId);
        //save button
        $markup .= $this->ui->settingsSaveBtn('email');
        //get profile settings Form ui html
        $markupForm = $this->ui->settingsForm('email', $markup);

        return $markupForm;
    }

    /**
     * return password settings markup
     */
    protected function passwordSettingsMarkup() {
        $markup = '';
        //get profile settings Password Panel ui html
        $markup .= $this->ui->settingsPasswordPanel();
        //save button
        $markup .= $this->ui->settingsSaveBtn('pass');
        //get profile settings Form ui html
        $markupForm = $this->ui->settingsForm('pass', $markup);

        return $markupForm;
    }

    public function saveSettings($panel, $iData) {
        switch ($panel) {
            case 'basic':
                $this->res = $this->user->saveBasicSettings($iData, $this->userId);
                break;
            case 'email':
                $this->res = $this->user->saveEmailSettings($iData, $this->userId);
                break;
            case 'pass':
                $this->res = $this->user->changePassword($iData, $this->userId, true);
                break;
        }

        return $this->res;
    }

    public function resendChangeEmailVerificaiton($userId) {
        $this->res = $this->user->resendVerifyEmail($userId, 'email2');
        return $this->res;
    }

    public function removeChangeEmail($userId) {
        $this->res = $this->user->removeChangeEmail($userId);
        return $this->res;
    }

    /**
     * 
     * return profile page content
     */
    public function profilePage($markup = false, $reload = false) {
        //get general settings
        $generalSettings = $this->generalSettings();
        //get profile page content html
        $final_markup = $this->ui->profilePage($reload, $generalSettings);

        if ($markup)
            return $final_markup;
        else {
            $this->res['cnt'] = $final_markup; //content to replace center part
            $this->res['text'] = '';
            $this->res['status'] = true;

            return $this->res;
        }
    }

}
?>