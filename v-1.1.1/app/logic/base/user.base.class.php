<?php

/*
 * all user related functions goes here
 */

class userBase {

    protected $res = array('status' => true, 'text' => '', 'error' => '');
    protected $userId;
    protected $ui;

    /**
     * set true if calls is from api
     * @var bool 
     */
    protected $api = false;

    /**
     * vbbuletin class instance
     * @var vblletin calss 
     */
    protected $vbulletin;

    public function __construct($userId = 0, $api = false) {
        $this->db = getDbClassInstance();
        $this->userId = $userId;
        $this->api = $api;

        require_once "ui/ui.user.class.php";
        $this->ui = new uiUser();
    }

    /**
     * validate sign up data
     * 
     * @param array $input input data
     */
    protected function validateSignup($input) {
        $res['state'] = true;
        $res['error'] = '';

        //make sure ip and coutry can access this site         
        $permssion = hasIpCountryAccessPermission('user_ajax.php');
        if ($permssion['status'] === false) {
            $res['state'] = false;
            $res['error'] = $permssion['error'];
            logAction('sign up failed', 'user', 'user sign up failed', 'user.class.php, user sign up failed, error : [' . $permssion['error'] . ']', true, $this->api, $this->userId);
        } else if (is_logged()) {
            $res['state'] = false;
            $res['error'] = langText('error_already_logged');
        } else if (SIGNUP_NAME && trim($input['fname']) == "") {
            $res['state'] = false;
            $res['error'] = langText('error_first_name_empty');
        } else if (SIGNUP_NAME && !validateMaxLength($input['fname'], 50)) {
            $res['state'] = false;
            $res['error'] = langText('error_first_name_too_long');
        } elseif (SIGNUP_NAME && !$this->isValidNameFormat($input['fname'], true)) {
            //check if valid username
            $res['state'] = false;
            $res['error'] = langText('error_first_name_format');
        } else if (SIGNUP_NAME && trim($input['lname']) == "") {
            $res['state'] = false;
            $res['error'] = langText('error_last_name_empty');
        } else if (SIGNUP_NAME && !validateMaxLength($input['lname'], 50)) {
            $res['state'] = false;
            $res['error'] = langText('error_last_name_too_long');
        } elseif (SIGNUP_NAME && !$this->isValidNameFormat($input['lname'], true)) {
            //check if valid username
            $res['state'] = false;
            $res['error'] = langText('error_last_name_format');
        } else if (trim($input['email']) == "") {
            $res['state'] = false;
            $res['error'] = langText('error_email_empty');
        } else if (!validateMaxLength($input['email'], 75)) {
            $res['state'] = false;
            $res['error'] = langText('error_email_too_long');
        } elseif (!isValidEmailFormat($input['email'])) {
            $res['state'] = false;
            $res['error'] = langText('error_email_format');
        } else if (trim($input['password']) == "") {
            $res['state'] = false;
            $res['error'] = langText('error_password_empty');
        } elseif (strlen(trim($input['password'])) < 6) {
            $res['state'] = false;
            $res['error'] = langText('error_password_too_short');
        } elseif (SIGNUP_PASS2 && trim($input['cpassword']) == "") {
            $res['state'] = false;
            $res['error'] = langText('error_confirm_password_empty');
        } elseif (SIGNUP_PASS2 && $input['cpassword'] != $input['password']) {
            $res['state'] = false;
            $res['error'] = langText('error_passwords_not_match');
        } else if (SIGNUP_CAPTCHAR && !$this->api && (empty($input['recaptcha_response_field']) || trim($input['recaptcha_response_field']) == "")) {
            $res['state'] = false;
            $res['error'] = langText('error_recaptcha_empty');
        } else if (SIGNUP_CAPTCHAR && !$this->api && !isValidRecaptcha($input)) {
            //for now we ignroe recaptchar validation for api
            $res['state'] = false;
            $res['error'] = langText('error_recaptcha_incorrect');
        } else {
            if (checkEmailUsed($input['email']) > 0) {
                //make sure unique email
                $res['state'] = false;
                $res['error'] = langText('error_email_exist');
                logAction('sign up failed', 'user', 'user sign up failed', 'user.class.php, user sign up failed, error : [' . $res['error'] . ']', true, $this->api, $this->userId);
            } else {
                //do username validations
                $resUn = $this->validateUsername($input['su_username']);
                if (USERNAME && $resUn['status'] == false) {
                    $res['state'] = false;
                    $res['error'] = $resUn['error'];
                } else if (USERNAME && $this->isPasswordContainUsername($input['password'], $input['su_username'])) {
                    //password contain username
                    $res['state'] = false;
                    $res['error'] = langText('error_password_contain_username');
                } else if ($this->isPasswordContainEmail($input['password'], $input['email'])) {
                    //password contain email
                    $res['state'] = false;
                    $res['error'] = langText('error_password_contain_email');
                }
            }
        }

        return $res;
    }

    /*
     * check if given username used in any account when sign up
     * we do case insensitive search
     * 
     * return true if username avaibale and can be used
     * 
     */

    protected function isUsernameAvailable($username) {
        $username = strtolower($username);
        $sql = 'select count(*) from user where LOWER(username) =:username';
        $iData = array("username" => trim($username));
        $count = $this->db->column($sql, $iData);
        if (intval($count[0]) > 0)
            return false;
        else
            return true;
    }

    /**
     * check if password contain username
     * 
     * @param type $password
     * @param type $username
     * @return boolean
     */
    protected function isPasswordContainUsername($password, $username) {

        if (!empty($username) && strstr($password, trim($username)) !== false)
            return true;

        return false;
    }

    /**
     * check if password contain email
     * 
     * @param type $password
     * @param type $email
     * @return boolean
     */
    protected function isPasswordContainEmail($password, $email) {
        if (strstr($password, trim($email)) !== false)
            return true;

        return false;
    }

    /**
     * hash password and return hash and salt value
     * 
     * @param string $password password string
     * @param string $salt password salt
     * @return array array with hash and salt values ex: array('hash'=>$hash,'salt'=>$salt)
     */
    protected function hashPassword($password, $salt = '') {
        //if salt not passed generate salt
        if (trim($salt) == '')
            $salt = $this->fetchUserSalt();
        // hash the md5'd password with the salt, vb does two md for password text for some reason so we do same
        $hash = md5(md5($password) . $salt);
        return array('hash' => $hash, 'salt' => $salt);
    }

    /**
     * Generates a new user salt string
     *
     * @param integer (Optional) the length of the salt string to generate
     *
     * @return string
     */
    protected function fetchUserSalt($length = SALT_LENGTH) {
        $salt = '';

        for ($i = 0; $i < $length; $i++) {
            $salt .= chr(rand(33, 126));
        }

        return $salt;
    }

    /*
     * @parmas : $type=email|password|email2
     */

    protected function insertEmailVerCode($id, $email, $type) {
        $code = md5($id . uniqid(rand(), true) . $email);
        $data = array("user_id" => $id, "type" => $type, "code" => $code);

        $sql = "insert into verifications (user_id,type,code,created,expire)" .
                " values ( :user_id, :type, :code, NOW(),DATE_ADD(NOW(),INTERVAL " . VERIFY_EXPIRE . " MINUTE))";
        $this->db->query($sql, $data);
        logAction('insert', 'verification code ' . $type, 'verification code insert ' . $type, 'user.class.php ,verification code insert ' . $type . ', data : ' . print_r($data, true) . '] by user: ' . $this->userId, true, $this->api, $this->userId);
        return $code;
    }

    /*
     * handle sign out
     */

    public function signout() {
        if (!is_logged()) {
            //get forced Log Out Data
            $this->res = forcedLogOutData();
        } else {
            $dis_name = $this->ui->getDispalyName($_SESSION['fname'], $_SESSION['lname'], $_SESSION['username'], $_SESSION['email']);
            $user_id = $_SESSION['id'];
            $user_username = $_SESSION['username'];

            $this->unsetSignedInUser();

            logAction('sign out', 'user', 'user sign out', 'user.class.php , successfull sign out for user.id=' . $user_id . ' user.username=' . $user_username, true, $this->api, $user_id);

            //just show home page, for that we need to empy custom-section
            $centent = '';
            //get list of lanuages 
            $langs = languageList();

            $this->res['snav'] = $this->ui->sideNavOptions($langs);
            $this->res['contactUs'] = $this->ui->contactSupportModelMrkup('', '');
            $this->res['cnt'] = $centent; //content to replace center part
            $this->res['text'] = langText('you_just_sign_out', array('NAME' => $dis_name));
            $this->res['status'] = true;
        }
        return $this->res;
    }

    /**
     * unset/clear user session 
     * if remmber me data is there remove form table
     */
    public function unsetSignedInUser() {
        //backup lanuage settings 
        $langCodeDefault = '';
        if (!empty($_SESSION['language_code'])) {
            //if user logged in and language code set we will be using same laguage after sign out 
            $langCodeDefault = $_SESSION['language_code'];
        } else if (!empty($_SESSION['language_code_default'])) {
            //else if we have default lanuages already we set it use after sign out
            $langCodeDefault = $_SESSION['language_code_default'];
        }

        //remove remember me data form db for signed in user
        $userId=getUserId();
        if (!empty($userId)) {
            $this->clearRememberMeData($userId);
        }

        //clear session data
        $this->clear_user_session();

        //now lets see lanuages if have
        if ($langCodeDefault != '') {
            if (session_status() == PHP_SESSION_ACTIVE) {
                //if we have session update language session variable by lang code we saved
                getLanguageFileNameByCode($langCodeDefault, true);
            }
        }
    }

    /*
     * clear remember me data from db to make sure old cookie can't use to 
     * sign in again as remebered login,
     * 
     * With this all remembered me logins for this user will be remved. ex: if have more than one browser
     */

    protected function clearRememberMeData($user_id) {
        $sql = 'delete from remembered_logins WHERE user_id=:user_id';
        $sqlData = array('user_id' => $user_id);
        $this->db->query($sql, $sqlData);
        logAction('clear', 'remembered logins', 'clear remembered logins', 'user.class.php ,clear remembered logins ,  for user: ' . $user_id, true, $this->api, $user_id);
    }

    /*
     * make sure user is logged off and seesion is end
     * if $killSession=true kill session so will have start new session with the server
     */

    protected function clear_user_session($killSession = false) {
        //store user id
        $userId=getUserId();

        if ($killSession) {
            // If it's desired to kill the session, also delete the session cookie.
            // Note: This will destroy the session, and not just the session data!
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
                );
            }
        }
        //Unset all of the session variables.
        $_SESSION = array();

        //destroys all of the data associated with the current session
        @session_destroy();
        logAction('clear', 'session', 'clear session', 'user.class.php ,clear session ,  by user: ' . $this->userId, true, $this->api, $userId);
        @session_start();
    }

    /**
     * return sign in page content
     */
    public function signInPage($markup = false) {
        //get profile page content html
        $final_markup = $this->ui->signInPageMrkup();

        if ($markup)
            return $final_markup;
        else {
            $this->res['cnt'] = $final_markup; //content to replace center part
            $this->res['text'] = '';
            $this->res['status'] = true;

            return $this->res;
        }
    }

    /**
     * sign in
     * 
     * make sure changes added to to do in signInRemembered() and getUserByApiToken()
     */
    public function signin($input) {
        $this->res['resendv'] = ''; //to send resend verfication button
        //make sure ip and coutry can access this site         
        $permssion = hasIpCountryAccessPermission('user_ajax.php');
        if ($permssion['status'] === false) {
            $this->res['status'] = false;
            $this->res['error'] = $permssion['error'];
            return $this->res;
        } else if (is_logged()) {
            $this->res['status'] = false;
            $this->res['error'] = langText('error_already_logged');
            return $this->res;
        } else if (trim($input['login_email_or_username']) == '') {
            $this->res['status'] = false;

            $userOrEmailError = langText('error_email_empty');
            if (USERNAME)
                $userOrEmailError = langText('error_username_or_email_empty');
            $this->res['error'] = $userOrEmailError;
            return $this->res;
        } else if (trim($input['login_password']) == '') {
            $this->res['status'] = false;
            $this->res['error'] = langText('error_password_empty');
            return $this->res;
        } else {
            //note: email or username is case insensitve
            $loginEmailOrUsername = strtolower(trim($input['login_email_or_username']));
            $data = array("email" => $loginEmailOrUsername, 'username' => $loginEmailOrUsername);
            $user = $this->db->row("select * from user where LOWER(email) =:email OR LOWER(username)=:username", $data);

            if ($this->db->RowCount() == 1) {
                //ok we a valid user data , check password
                $hashRes = $this->hashPassword($input['login_password'], $user['salt']);
                $hash = $hashRes['hash'];

                if ($hash == $user['phash']) {
                    if ($user['status'] == 'deleted') {
                        $this->res['status'] = false;
                        $this->res['error'] = langText('error_account_deleted');
                        return $this->res;
                    } else if ($user['status'] == 'suspended') {
                        $this->res['status'] = false;
                        $this->res['error'] = langText('error_account_suspended');
                        return $this->res;
                    } else if ($user['verified'] == 0) {
                        $this->res['status'] = false;
                        $this->res['error'] = langText('error_email_not_verified');

                        if (!$this->api) {
                            $resendv = $this->ui->htmlButton(langText('resend_verification'), 'resendv(' . $user['id'] . ',\'signin\');', 'btn btn-default btn-sm sign-in-resendv-btn text-uppercase col-xs-8 col-sm-6 col-md-12 col-lg-10', '', 'resendv_btn', 'resendv_btn');
                            $resendv = '<div class="clearfix hidden-xs hidden-md hidden-lg resendv-devs"></div>
                                <div class="col-sm-1 hidden-xs hidden-md hidden-lg resendv-devs"></div>
                                <div class="col-xs-12 col-sm-10 col-md-12 col-lg-12 resendv-devs">' .
                                    $resendv .
                                    '</div>';
                            $this->res['resendv'] = $resendv;
                        } else {
                            $this->res['data']['user'] = $user['id'];
                        }
                        return $this->res;
                    } else {
                        //user Credential are correct, now if use is locked out
                        if (!$this->isUserLoked($user['id'], true)) {
                            //set user data to sestion
                            $apiToken = $this->setSignedInUser($user);

                            logAction('sign in', 'user', 'user sign in', 'user.class.php , successfull login for user.id=' . $user['id'], true, $this->api, $user['id']);

                            if (!$this->api) {

                                //get contet to load just after logged in
                                $content = firtPage(false, true);
                                $cnt = $content['cnt'];
                                $page = $content['page'];

                                $this->res['rememberMe'] = '';
                                if (!empty($input['login_rememberme'])) {
                                    $this->res['rememberMe'] = $this->setRememberMe($_SESSION['username'], $_SESSION['id'], 0, '', false);
                                }

//                                if ($page == 'profile') {
//                                    //if we load profile page on sign in we have to show Quick Start Instructions
//                                    $cnt.=$this->ui->quickStart();
//                                }

                                //get contact name
                                $contactName = $this->ui->getDispalyName($_SESSION['fname'], $_SESSION['lname'], $_SESSION['username'], $_SESSION['email']);
                                //get list of lanuages 
                                $langs = languageList();

                                $this->res['admin'] = isAdmin();
                                $this->res['page'] = $page;
                                $this->res['snav'] = $this->ui->sideNavOptions($langs);
                                $this->res['contactUs'] = $this->ui->contactSupportModelMrkup($_SESSION['email'], $contactName);
                                $this->res['cnt'] = $cnt; //content to replace center part
                                $this->res['text'] = langText('welcome_back', array('NAME' => $contactName));
                            } else {
                                //for api
                                $this->res['data']['token'] = $apiToken;
                                $this->res['data']['name'] = $this->ui->getDispalyName($user['fname'], $user['lname'], $user['username'], $user['email']);
                                $this->res['data']['username'] = $user['username'];
                                $this->res['text'] = langText('login_successful');
                            }
                            $this->res['status'] = true;
                        } else {
                            //user has been lockout
                            $this->res['status'] = false;
                            $errorMsg = langText('error_account_locked');
                            if (!$this->api)
                                $errorMsg.= '<br>';
                            $errorMsg.= langText('error_locked_try_again_after', array('LOGIN_LOCKOUT_TIME' => LOGIN_LOCKOUT_TIME));
                            $this->res['error'] = $errorMsg;
                            return $this->res;
                        }
                    }
                } else {
                    //password is incorrect so check for limit login attemps and block user if need
                    $blocked = $this->limitFailedLoginAttempts($user['id']);
                    $errorMsg = langText('error_invalid_email_password');
                    if (USERNAME)
                        $errorMsg = langText('error_invalid_username_email_password');

                    if ($blocked) {
                        $errorMsg .= langText('error_account_locked');
                        if (!$this->api)
                            $errorMsg.= '<br>';
                        $errorMsg.= langText('error_locked_try_again_after', array('LOGIN_LOCKOUT_TIME' => LOGIN_LOCKOUT_TIME));
                    }
                    $this->res['status'] = false;
                    $this->res['error'] = $errorMsg;
                    return $this->res;
                }
            } else {
                $userOrEmailError = langText('error_invalid_email_password');
                if (USERNAME)
                    $userOrEmailError = langText('error_invalid_username_email_password');

                $this->res['status'] = false;
                $this->res['error'] = $userOrEmailError;
                return $this->res;
            }
        }
        return $this->res;
    }

    /**
     * If there are remembered login validate credentials and sign user.  
     * if $ipCheck true only allowed sign in again for same ip user signed in when create remember me
     */
    public function signInRemembered($ipCheck = false) {
        if (!empty($_COOKIE[COOKIE_NAME])) {
            $iData = array();
            //decord encoded string
            $cookieStr = base64_decode($_COOKIE[COOKIE_NAME]);
            parse_str($cookieStr, $iData);

            if (!empty($iData['seriesId'])) {
                $sql = 'SELECT * from remembered_logins WHERE series_id=:series_id AND expire>NOW() LIMIT 1';
                $sqlData = array('series_id' => $iData['seriesId']);
                $rembUser = $this->db->row($sql, $sqlData);
                if ($this->db->RowCount() > 0) {
                    //Verify the password
                    if ($iData['username'] == $rembUser['username']) {
                        if ($iData['token'] == $rembUser['token']) {
                            if ($ipCheck) {
                                $ip = getClientIp();
                                if ($ip != $rembUser['ipaddr']) {
                                    return;
                                }
                            }
                            //now we can cotinue setting user sign in for remembered user
                            $user = $this->getUser($rembUser['user_id']);
                            if (!empty($user)) {
                                //remeber me user Credential are correct, now if use is locked out
                                if ($user['status'] == 'deleted') {
                                    //This account has been deleted  
                                    $this->clearRememberMeData($user['id']);
                                    return;
                                } else if ($user['status'] == 'suspended') {
                                    //This account has been suspended 
                                    $this->clearRememberMeData($user['id']);
                                    return;
                                } else if (!$this->isUserLoked($user['id'], true)) {
                                    //set user data to sestion                            
                                    $this->setSignedInUser($user);
                                    logAction('sign in auto', 'user', 'user sign in auto', 'user.class.php , successfull login with remembered credentials for user.id=' . $user['id'], true, $this->api, $user['id']);

                                    //need to set remember me new token for this remembered_logins.id
                                    $this->setRememberMe($iData['username'], $rembUser['user_id'], $rembUser['id'], $rembUser['series_id'], true);
                                } else {
                                    //this case will not happen since we clart remember me data on lokout user, but just kept the code
                                    //user has been lockout, make sure all remember me removed for security
                                    $this->clearRememberMeData($user['id']);
                                    return;
                                }
                            }
                        } else {
                            //theft is assumed. 
                            //The user receives may a strongly worded warning and all of the user's remembered sessions are deleted.
                            //make sure all remember me removed for security for this user
                            $this->clearRememberMeData($rembUser['user_id']);
                        }
                    } else {
                        //cooke does not match to database mean them assumed
                        //make sure all remember me removed for security for this user
                        $this->clearRememberMeData($rembUser['user_id']);
                    }
                } else {
                    //no loger this cookie not valid, so remove it
                    $this->unsetRememberCookie();
                }
            }
        }
    }

    /**
     * sign in user on email verfication sucess
     * we don't do any validation for user active, delted etc since it must be already done
     * 
     * @param int $userId user.id
     */
    public function signInUserOnEmailVerify($userId) {
        //no loger this cookie not valid, so remove it
        $this->unsetRememberCookie();

        $user = $this->getUser($userId);
        if (!empty($user)) {
            $this->setSignedInUser($user);
            logAction('sign in email verify', 'user', 'user sign in email verify', 'user.class.php , successfull login with email verify for user.id=' . $user['id'], true, $this->api, $user['id']);
            return true;
        }

        return false;
    }

    /*
     * set user last login and ip to user table
     */

    protected function setLastLoginData($userId) {
        //set lastlogindt,lastloginip
        $lastloginip = getClientIp();
        $data = array('id' => $userId, 'lastloginip' => $lastloginip);
        $sql = 'update user set lastlogindt=NOW(),lastloginip=:lastloginip where id = :id';
        $this->db->query($sql, $data);
    }

    /**
     * complete sign in sesstion variables
     * 
     * @param array $user user data
     * @return string
     */
    protected function setSignedInUser($user) {
        //update last login details
        $this->setLastLoginData($user['id']);
        //api token is to be used as a login token for api
        $apiToken = '';
        if (!$this->api) {
            //set user data to sestion
            session_regenerate_id(true);
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['fname'] = $user['fname'];
            $_SESSION['lname'] = $user['lname'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            //set lanuage code and file name for settsion to later use
            $this->setUserLangageToSession($user['id']);
            //for custom applications 
            $this->setCustomSessionVars($user);

            //set php Tz Identifier to seeesion
            if (!empty($user['php_tz_identifier']))
                $_SESSION['phpTzIdentifier'] = $user['php_tz_identifier'];
            else
                $_SESSION['phpTzIdentifier'] = 'UTC';
            //note: do not add session_write_close() since we may need to set $_SESSION['csrfToken'] later in other place
        }else {
            //for api
            require_once "logic/base/token.class.php";
            $apiToken = $this->setUniqueApiToken($user['id']);
        }

        return $apiToken;
    }
    
    /**
     * setCustomSessionVars for custom applications by overide setCustomSessionVars in custom user class
     * 
     * @param type $user
     */
    protected function setCustomSessionVars($user){
        
    }

    /**
     * return sign up page content
     */
    public function signUpPage($markup = false) {
        //get profile page content html
        $final_markup = $this->ui->signUpPageMrkup();

        if ($markup)
            return $final_markup;
        else {
            $this->res['cnt'] = $final_markup; //content to replace center part
            $this->res['text'] = '';
            $this->res['status'] = true;

            return $this->res;
        }
    }

    /**
     * crete user after validations done
     *  
     * @param array $input
     * @param int $verified 1 or 0
     * @return int user id
     */
    protected function createUser($input, $verified = 0) {
        //remove spaces if avaible at start and end for password
        $input['password'] = trim($input['password']);
        $hashRes = $this->hashPassword($input['password']);
        $hash = $hashRes['hash'];
        $salt = $hashRes['salt'];

        //get php_tz_identifier from $input['tzOffset']
        if (empty($input['tzOffset'])) {
            $input['tzOffset'] = 0;
            $php_tz_identifier = 'UTC';
        } else {
            $php_tz_identifier = timezone_name_from_abbr("", intval(doubleval($input['tzOffset']) * 60));
            if (empty($php_tz_identifier))
                $php_tz_identifier = 'UTC';
        }

        //make sure first name last name indexes are there
        if (!isset($input['fname']))
            $input['fname'] = '';
        if (!isset($input['lname']))
            $input['lname'] = '';


        $data = array("status" => "active", "fname" => trim($input['fname']), "lname" => trim($input['lname']),
            "email" => trim($input['email']),
            "php_tz_identifier" => $php_tz_identifier, "username" => trim($input['su_username']),
            "hash" => $hash, "salt" => $salt, 'verified' => $verified);

        $this->db->query("insert into user (status,fname,lname,email,username,php_tz_identifier,phash,salt,created,verified)" .
                " values ( :status, :fname, :lname,:email,:username,:php_tz_identifier, :hash, :salt,NOW(),:verified)", $data);

        $id = $this->db->insertId();
        //unset login has so not goes to logs
        unset($data['hash']);
        logAction('sign up', 'user', 'user sign up', 'user.class.php, user sign up, data : [' . print_r($data, true) . ']', true, $this->api, $id);

        return $id;
    }

    /**
     * create user account
     */
    public function signup($input) {

        //fix for error username index not set if USERNAME set to false
        if (!USERNAME)
            $input['su_username'] = '';

        //valdiate sign up data            
        $res = $this->validateSignup($input);
        if ($res['state']) {
            //if need some custom logic to auto verify email known we can do here and set $verified=1 for them
            $verified = 0;
            //we can create user

            $id = $this->createUser($input, $verified);
            //build contact name
            $contactName = $this->ui->getDispalyName($input['fname'], $input['lname'], $input['su_username'], $input['email']);


            if (!$verified) {
                //email need to verify
                $this->res = $this->emailVerification($id, 'email', $input['email'], $contactName);

                if ($this->api && $this->res['status'] !== false) {
                    /*
                     * user can't use account until email verified and in mobile app should show resend verification 
                     * option. If user click resend verification email in mobile app it should call resend 
                     * verification end point.
                     */
                    $this->res['data']['emailVerified'] = false;
                }
            } else {
                /**
                 * @notethis not used, so woory later
                 */
                //email welcome and say verifed, sicne this user already verfied
                $subject = 'Welcome to ' . SITE_NAME;
                //get verified Email confirmation Content
                $wembody = $this->ui->verifiedUserWelcomeContent($subject, $input['su_username'], $contactName);
                sendEmail($input['email'], $subject, $wembody, $this->api, $id);
                $txtMsg = 'You have successfully registered to ' . SITE_NAME . '.';
                $this->res['text'] = $txtMsg;
                if ($this->api) {
                    // email verified and in mobile app no need to show resend verification option.                     
                    $this->res['data']['emailVerified'] = true;
                }
            }

            if (!$this->api) {
                //just empty custom custom-section
                $centent = '';
                //content to replace center part
                $this->res['cnt'] = $centent;
            } else {
                //for api,if api used
                $this->res['status'] = true;
                $this->res['data']['name'] = $contactName;
                $this->res['data']['username'] = $input['su_username'];
                $this->res['data']['email'] = $input['email'];
                $this->res['data']['user'] = intval($id); //send user id so if need can use resend verfication etc                
            }
        } else {
            $this->res['status'] = false;
            $this->res['error'] = $res['error'];
            if (!$this->api) {
                if (SIGNUP_CAPTCHAR_VERSION == "1") {
                    //get new recaptchar iframe to resturn with error, so it load new captchar
                    $recaptchaIframe = $this->ui->getRecaptchaIframe();

                    $this->res['recaptcha'] = $recaptchaIframe;
                }
            } else {
                //for api
            }
        }
        return $this->res;
    }

    /*
     * check if string is mathced name format
     * this for first name, last name, user name etc
     * $allowSpaces if true allow spaces ex: first and last name can have spaces
     */

    protected function isValidNameFormat($text, $allowSpaces = false) {
        $space = '';
        if ($allowSpaces)
            $space = '\s';

        if (!preg_match('/^[a-zA-Z]+([a-zA-Z0-9\-\.' . $space . '])*$/i', $text)) {
            //not a valid username
            return false;
        }
        //valid name
        return true;
    }

    /*
     * send password reset link to user email address
     */

    public function requestPasswordResetLink($usernameOremail) {
        //trim user name or email
        $usernameOremail == trim($usernameOremail);
        if ($usernameOremail == '') {
            $this->res['status'] = false;
            $userOrEmailError = langText('error_email_empty');
            if (USERNAME)
                $userOrEmailError = langText('error_username_or_email_empty');
            $this->res['error'] = $userOrEmailError;
            return $this->res;
        } else {
            return $this->sendRsetPasswordLink(array("usernameOremail" => $usernameOremail));
        }

        return $this->res;
    }

    /*
     * resend reset password link to give user
     * by user.email or user.username or user.id
     * $iData: array(id=>{id},usernameOremail=>{usernameOremail})
     */

    public function sendRsetPasswordLink($iData) {
        $data = array();
        $whereSql = '';
        if (isset($iData['id']) && trim($iData['id']) != '') {
            $data['id'] = trim($iData['id']);
            $whereSql = ' id = :id';
        } else if (isset($iData['usernameOremail']) && trim($iData['usernameOremail']) != '') {
            $data['email'] = strtolower(trim($iData['usernameOremail']));
            $data['username'] = strtolower(trim($iData['usernameOremail']));
            $whereSql = ' LOWER(email) = :email OR LOWER(username) = :username';
        } else {
            $this->res['status'] = false;
            $this->res['error'] = langText('invalid_request');
            return $this->res;
        }

        $user = $this->db->row("select email,id,status,fname,lname,username from user where " . $whereSql, $data);
        if ($this->db->RowCount() > 0) {
            //we found data
            if ($user['status'] == 'deleted') {
                $this->res['status'] = false;
                $this->res['error'] = langText('error_account_deleted');
                return $this->res;
            } else if ($user['status'] == 'suspended') {
                $this->res['status'] = false;
                $this->res['error'] = langText('error_account_suspended');
                return $this->res;
            } else {
                $contactName = $this->ui->getDispalyName($user['fname'], $user['lname'], $user['username'], $user['email']);
                $this->res = $this->emailVerification($user['id'], 'password', $user['email'], $contactName, false);
                logAction('email', 'password reset link', 'email password reset link', 'user.class.php, email password reset link, user id : ' . $user['id'] . '', true, $this->api, $user['id']);
                return $this->res;
            }
        } else {
            $this->res['status'] = false;
            $this->res['error'] = langText('error_data_found');
            return $this->res;
        }
    }

    /**
     * resend verification email
     */
    public function resendVerifyEmail($userId, $type) {
        if (!is_numeric($userId)) {
            $this->res['status'] = false;
            $this->res['error'] = 'Sorry! Invalid request.';
            return $this->res;
        } else {
            //check if email already verified

            $user = $this->getUser($userId, array('verified,email2'));
            if (($type == 'email' && $user['verified'] == 1 ) || ($type == 'email2' && empty($user['email2']))) {
                //already vefired
                $this->res['status'] = false;
                $this->res['error'] = 'Email address has been verified already.';
                return $this->res;
            } else {
                $this->res = $this->emailVerification($userId, $type, '', '', true);
                logAction('resend', 'email verification', 'resend email verification', 'user.class.php, resend email verification, user id : ' . $userId . '', true, $this->api, $userId);
            }
        }
        return $this->res;
    }

    public function removeChangeEmail($userId) {
        if (!is_numeric($userId)) {
            $this->res['status'] = false;
            $this->res['error'] = 'Sorry! Invalid request.';
            return $this->res;
        } else {
            $data = array("id" => $userId);
            $this->db->query("update user set email2=NULL where id=:id", $data);
            logAction('delete', 'new email', 'delete new email', 'user.class.php, delete new email(user.email2), user id : ' . $userId . '', true, $this->api, $userId);
            $this->res['rtext'] = '';
            $this->res['text'] = 'New Email Address has been removed.';
        }
        return $this->res;
    }

    /*
     * send verification email
     * may be we will need to send this again if need(like failed or change email) to move as a function
     * @params: $email may be set to empty when use resend
     *          @parmas : $type=email|password|email2
     */

    protected function emailVerification($userId, $type, $email = '', $contactName = '', $resend = false) {
        if (!$resend) {
            $code = $this->insertEmailVerCode($userId, $email, $type);
        } else {
            $emailField = 'email';
            if ($type == 'email2')
                $emailField = 'email2';
            //we get existing code and if it is not less than 5 minutes to expire and send it else insert new        
            $sql = "select v.code,v.expire,u." . $emailField . " from verifications as v left join user as u 
                on v.user_id=u.id where v.user_id =:user_id  and v.type=:type order by v.id desc limit 1";
            $ver = $this->db->query($sql, array("user_id" => $userId, 'type' => $type));
            if ($this->db->RowCount() == 1) {
                //we found a row and it has be one since it is for email verification
                $code = $ver[0]['code'];
                $email = $ver[0][$emailField];
                if (strtotime($ver[0]['expire']) < strtotime("-5 minutes")) {
                    //expired or just expire in 5 minutes so send new one
                    $code = $this->insertEmailVerCode($userId, $email, $type);
                }
            } else {
                //this can happen when user tring this after long time and our records for verfifcation has been deleted form db
                $this->res['status'] = false;
                $this->res['error'] = langText('invalid_request');
                return $this->res;
            }
        }

        $user = $this->getUser($userId, array('fname', 'lname', 'username', 'email'));
        //set contact name if need
        if ($contactName == '')
            $contactName = $this->ui->getDispalyName($user['fname'], $user['lname'], $user['username'], $user['email']);

        $url = SITE_URL . '/?vr=' . $code;
        switch ($type) {
            case 'email':
                //send confrimation email
                $emailSubject = langText('sign_up_confirmation_subject', array('SITE_NAME' => SITE_NAME));

                //get verification Email Content
                $emailBody = $this->ui->verificationEmailContent($emailSubject, $url, $resend, $contactName, '');

                //get email Verification Sent Markup
                $resText = $this->ui->emailVerificationSentMarkup($resend, $userId, $contactName, $this->api);
                $this->res['text'] = $resText;
                break;
            case 'email2':
                //send email conform email for email2
                $emailSubject = langText('confirm_your_new_email_subject');
                //get verification Email2 Content when change current email
                $emailBody = $this->ui->verificationEmail2Content($emailSubject, $url, $email, $contactName);

                $this->res['rtext'] = $this->verifyNewEmailNote($userId, $resend);
                $this->res['text'] = $this->ui->email2VerificationSentMarkup($resend, $email);
                break;
            case 'password':
                //send password rest link to email address
                $emailSubject = langText('reset_password_subject', array('SITE_NAME' => SITE_NAME));
                //get rest Password Link Content
                $emailBody = $this->ui->restPasswordLinkContent($emailSubject, $url, $user['username'], $contactName);
                //get email Rest Password Link markup to show after email reset link sent
                $resText = $this->ui->emailRestPasswordLinkmarkup();

                $this->res['text'] = $resText;
                break;
        }

        sendEmail($email, $emailSubject, $emailBody, $this->api, $userId);

        $this->res['status'] = true;
        return $this->res;
    }

    /*
     * complete verification email, changed email, password change
     */

    public function verifications($vc) {
        //signout if there some one sign in
        $this->unsetSignedInUser();
        //The vefication key should be 32 characters since it's an md5 hash.
        if (strlen($vc) == 32) {
            $ver = $this->db->row("select * from verifications where code = :code", array("code" => trim($vc)));
            //we have found verfication record, so we know user has set prefered language, we will use prefered language from now
            $this->setUserLangageToSession($ver['user_id']);

            if ($this->db->RowCount() == 1) {
                if (strtotime($ver['expire']) < time()) {
                    //expired
                    $this->res['status'] = false;
                    $this->res['error'] = langText('error_code_expired');
                    return $this->res;
                } else {
                    $user = $this->db->row("select fname,lname,username,email,verified,email2,status from user where id=:id", array("id" => $ver['user_id']));
                    //make sure user is active, ie: not deleted or suspened
                    if ($user['status'] == 'deleted') {
                        $this->res['status'] = false;
                        $this->res['error'] = langText('error_account_deleted');
                        return $this->res;
                    } else if ($user['status'] == 'suspended') {
                        $this->res['status'] = false;
                        $this->res['error'] = langText('error_account_suspended');
                        return $this->res;
                    } else {
                        $cont_name = $this->ui->getDispalyName($user['fname'], $user['lname'], $user['username'], $user['email']);

                        //valid and do actions
                        switch ($ver['type']) {
                            case 'email':
                                if ($user['verified'] == 1) {
                                    //already vefired
                                    $this->res['status'] = false;
                                    $this->res['error'] = langText('error_email_already_verified');
                                    return $this->res;
                                } else {
                                    //complete emai verifcation
                                    $data = array("id" => $ver['user_id']);
                                    $this->db->query("update user set verified=1 where id=:id", $data);

                                    //we make user sign in if SIGNIN_ON_EMAIL_VERIFY is true                                    
                                    if (SIGNIN_ON_EMAIL_VERIFY)
                                        $this->signInUserOnEmailVerify($ver['user_id']);

                                    //email welcome and say verifed
                                    $subject = langText('welcome_to', array('SITE_NAME' => SITE_NAME));
                                    //get verified Email confirmation Content
                                    $body = $this->ui->verifiedEmailConfirmationContent($subject, $user['username'], $cont_name);


                                    $append = $this->emailNoteVerifiedAppend();
                                    //get content to show in website just after verfication
                                    $emilVerifiedNote = $this->ui->verifiedConfirmationNote($cont_name, $append);

                                    sendEmail($user['email'], $subject, $body, $this->api, $ver['user_id']);
                                    logAction('verified', 'email verify', 'email verified', 'user.class.php, email verified, user id : ' . $ver['user_id'] . ', email : ' . $user['email'], true, $this->api, $ver['user_id']);

                                    $this->res['cnt'] = '';
                                    $this->res['status'] = true;
                                    $this->res['text'] = $emilVerifiedNote;
                                }

                                //set user id to result
                                $this->res['userId'] = $ver['user_id'];

                                break;
                            case 'email2':
                                //when some one follow verification link for email2 and email2 is empty means already verified
                                if (empty($user['email2'])) {
                                    //already vefired
                                    $this->res['status'] = false;
                                    $this->res['error'] = 'Sorry! Email address has been already verified or removed .';
                                    return $this->res;
                                } else {
                                    //check if email already used in some place
                                    $email2Used = checkEmailUsed($user['email2']);
                                    if ($email2Used > 0 && $ver['user_id'] != $email2Used) {
                                        //$email2Used>0 means email2 is alrady used. but if same account is ok, if not can't allow
                                        //same account also can't happen accoriing to code
                                        $this->res['status'] = false;
                                        $this->res['error'] = 'Sorry! Email address is already associated with another account.';
                                        return $this->res;
                                    } else {
                                        //complete email2 verifcation
                                        $data = array("id" => $ver['user_id'], 'email' => $user['email2']);
                                        $this->db->query("update user set email=:email,email2=NULL where id=:id", $data);

                                        //we make user sign in if SIGNIN_ON_EMAIL_VERIFY is true                                    
                                        if (SIGNIN_ON_EMAIL_VERIFY)
                                            $this->signInUserOnEmailVerify($ver['user_id']);

                                        $subject = 'Email has been verified';
                                        //get verified Email2 confirmation Content when change email done
                                        $body = $this->ui->verifiedEmail2ConfirmationContent($subject, $user['email2'], $user['username'], $cont_name);

                                        sendEmail($user['email2'], $subject, $body, $this->api, $ver['user_id']);
                                        logAction('verified', 'email2 verify', 'email2 verified', 'user.class.php, email2(new email) verified, user id : ' . $ver['user_id'] . ', email : ' . $user['email'], true, $this->api, $ver['user_id']);

                                        $this->res['cnt'] = '';
                                        $this->res['status'] = true;
                                        $this->res['text'] = 'Email address has been verified successfully.';
                                    }
                                    //set user id to result
                                    $this->res['userId'] = $ver['user_id'];
                                }
                                break;
                            case 'password':
                                //we set tempary session data to change password
                                $_SESSION['cp_userid'] = intval($ver['user_id']);
                                logAction('verified', 'password link', 'password link verified', 'user.class.php, password link verified, user id : ' . $ver['user_id'], true, $this->api, $ver['user_id']);

                                $this->res['popupc'] = $this->ui->resetPassMarkup();
                                $this->res['status'] = true;
                                $this->res['cnt'] = '';
                                $this->res['text'] = '';
                                break;
                        }
                    }
                }
            } else {
                //invalid request
                $this->res['status'] = false;
                $this->res['error'] = 'Sorry! Invalid request.';
                return $this->res;
            }
        } else {
            //error
            $this->res['status'] = false;
            $this->res['error'] = 'Sorry! Invalid request.';
            return $this->res;
        }
        return $this->res;
    }

    /**
     * return note to append when email verfied note show in website
     * Specailly this is done to show url url can load next
     */
    protected function emailNoteVerifiedAppend() {
        $append = '';
        return $append;
    }

    /*
     * return user infor for given user.id
     * if fields passed only those fields will be retured
     */

    public function getUser($user_id, $fields = array()) {
        if (empty($fields))
            $feilds_str = 'u.*';
        else
            $feilds_str = implode(',', $fields);
        $sql = "select " . $feilds_str . " from user u where u.id=:user_id";
        $user = $this->db->row($sql, array('user_id' => $user_id));
        return $user;
    }

    /*
     * get user by username (user.username)
     */

    public function getUserByUsername($username, $fields = array()) {
        if (empty($fields))
            $feilds_str = 'u.*';
        else
            $feilds_str = implode(',', $fields);
//make sure we don't return admin user not show in direct load vistors view typing in url
        $adminW = ' user_type!="admin" AND';
        $sql = 'select ' . $feilds_str . ' from user u where ' . $adminW . ' username=:username';
        $user = $this->db->row($sql, array('username' => $username));
//there can't be more than one row for a username since username is unique
        if ($this->db->RowCount() == 1)
            return $user;
        else
            return array();
    }

    /**
     * check current password matched for given user id and password
     * 
     * @param int $user_id
     * @param string $password
     * @param array $user user data, if $user array is not passed we call getUser
     * @return boolean true/false
     */
    public function checkPassword($user_id, $password, $user = array()) {
        if (empty($user))
            $user = $this->getUser($user_id, array('salt', 'phash'));

        if ($this->db->RowCount() == 1) {
            //ok we a valid user data , check password
            $hashRes = $this->hashPassword($password, $user['salt']);
            $hash = $hashRes['hash'];
            if ($hash == $user['phash']) {
                //password matched
                return true;
            }
        }
        return false;
    }

    public function saveBasicSettings($iData, $user_id) {
        //do validation here
        if (!isset($iData['fname']) || trim($iData['fname']) == '') {
            $this->res['status'] = false;
            $this->res['error'] = 'Sorry! First Name can not be empty.';
            return $this->res;
        } elseif (!$this->isValidNameFormat($iData['fname'], true)) {
            //check if valid username
            $this->res['status'] = false;
            $this->res['error'] = 'Invalid First Name, First Name can be only alpha numeric and must start with a letter.';
            return $this->res;
        } else if (!isset($iData['lname']) || trim($iData['lname']) == '') {
            $this->res['status'] = false;
            $this->res['error'] = 'Sorry! Last Name can not be empty.';
            return $this->res;
        } elseif (!$this->isValidNameFormat($iData['lname'], true)) {
            //check if valid username
            $this->res['status'] = false;
            $this->res['error'] = 'Invalid Last Name, Last Name can be only alpha numeric and must start with a letter.';
            return $this->res;
        } else {
            $fname = trim($iData['fname']);
            $lname = trim($iData['lname']);
            //set default time zone
            if (!TIME_ZONE || empty($iData['timezone'])) {
                $iData['timezone'] = 'UTC';
            }

            $sql = "update user set fname=:fname,lname=:lname,php_tz_identifier=:tz_identifier where id=:user_id";
            $data = array('user_id' => $user_id, 'fname' => $fname, 'lname' => $lname, 'tz_identifier' => $iData['timezone']);
            $this->db->query($sql, $data);
            logAction('save', 'Gen Settings', 'save Gen Settings', 'user.class.php, save Gen Settings, data : ' . print_r($data, true), true, $this->api, $user_id);

            //update session variables
            $_SESSION['fname'] = $fname;
            $_SESSION['lname'] = $lname;
            $_SESSION['phpTzIdentifier'] = $iData['timezone'];
            $contactName = $this->ui->getDispalyName($_SESSION['fname'], $_SESSION['lname'], $_SESSION['username'], $_SESSION['email']);
            //get list of lanuages 
            $langs = languageList();

            //return top nav panel since we have changed name
            $this->res['snav'] = $this->ui->sideNavOptions($langs);
            $this->res['contactUs'] = $this->ui->contactSupportModelMrkup($_SESSION['email'], $contactName);
        }
        $this->res['status'] = true; //all ok
        return $this->res;
    }

    /**
     * save user phone number
     * This is as seprate function since it is better to save phone number when they set phone notification for something
     */
    public function savePhone($phone, $user_id, $ignoreValidate = false) {

        if ($phone != '' && !$ignoreValidate) {
            $this->res = $this->validatePhoneNumber($phone);
            if ($this->res['status'] === false) {
                //error so we return error
                return $this->res;
            }
        }

        $sql = 'update user set phone=:phone where id=:user_id';
        $data = array('phone' => $phone, 'user_id' => $user_id);
        $this->db->query($sql, $data);
        logAction('save', 'Phone', 'save Phone', 'user.class.php, savePhone, data : ' . print_r($data, true), true, $this->api, $user_id);

        return $this->res;
    }

    /*
     * valdiate phone number give
     * at the moment check for (+1 sign and 8 to 19 digits) or +1 ### ### #### or +1-###-###-####
     */

    public function validatePhoneNumber($phone) {
        $res = array();
        if (!preg_match("/^(\+1[0-9]{8,19})?(\+1\s[0-9]{3}\s[0-9]{3}\s[0-9]{4})?(\+1\-[0-9]{3}\-[0-9]{3}\-[0-9]{4})?$/", $phone)) {
            //if (!preg_match("/^(\+1\s[0-9]{3}\s[0-9]{3}\s[0-9]{4})$/", $phone)) {
            $res['status'] = false;
            $res['error'] = 'Please enter a valid phone number.';
            return $res;
        }
        $res['status'] = true;
        return $res;
    }

    /**
     * strip phone number spaces and -
     * 
     * we allow phone number like (+1 sign and 8 to 19 digits) or +1 ### ### #### or +1-###-###-####
     */
    public function stripPhoneNumber($phone) {
        $phone = preg_replace(array('/\s/', '/\-/'), '', $phone);
        return $phone;
    }

    /**
     * return note content for verify new email
     */
    public function verifyNewEmailNote($userId, $resend = false) {
        //get note content html for verify new email 
        return $this->ui->verifyNewEmailNote($userId, $resend);
    }

    /**
     * save/change user email settings done for profile page
     */
    protected function validateUsername($username) {

        $this->res['status'] = true;
        //if we don't use username in the system we ignore this validation
        if (!USERNAME)
            return $this->res;

        $username = trim($username);
        if ($username == "") {
            $this->res['status'] = false;
            $this->res['error'] = 'Username can not be empty.';
            return $this->res;
        } elseif (!$this->isValidNameFormat($username, false)) {
            //check if valid username
            $this->res['status'] = false;
            $this->res['error'] = 'Invalid Username, Username can be only contain alpha numeric,.,- and must start with a letter.';
            return $this->res;
        } elseif (strlen($username) < 5) {
            //username has to be at least 5 charter length
            $this->res['status'] = false;
            $this->res['error'] = 'Sorry! Username must contain at least 5 charters.';
            return $this->res;
        } elseif (strlen($username) > 50) {
            //Username must not contain at more than 50 charters
            $this->res['status'] = false;
            $this->res['error'] = 'Sorry! Username must not contain at more than 50 charters.';
            return $this->res;
        } elseif (isReserved($username)) {
            //check if user name has reserved words etc
            $this->res['status'] = false;
            $this->res['error'] = 'Sorry! Username not available.';
            logAction('sign up failed', 'user', 'user sign up failed', 'user.class.php, user sign up failed, error : [' . $this->res['error'] . ' - reserved]', true, $this->api, $this->userId);
            return $this->res;
        } else if (!$this->isUsernameAvailable($username)) {
            $this->res['status'] = false;
            $this->res['error'] = 'Sorry! Username not available.';
            logAction('sign up failed', 'user', 'user sign up failed', 'user.class.php, user sign up failed, error : [' . $this->res['error'] . ' - used]', true, $this->api, $this->userId);
            return $this->res;
        }


        return $this->res;
    }

    /*
     * save/change user email settings done for profile page
     */

    public function saveEmailSettings($iData, $user_id) {
        if (trim($iData['nemail']) == "") {
            $this->res['status'] = false;
            $this->res['error'] = 'New Email Address can not be empty.';
            return $this->res;
        } elseif (!isValidEmailFormat($iData['nemail'])) {
            $this->res['status'] = false;
            $this->res['error'] = 'Please type valid New Email Address.';
            return $this->res;
        } else if (trim($iData['nemail']) == $_SESSION['email']) {
            $this->res['status'] = false;
            $this->res['error'] = 'Sorry! New Email Address and Active Email Address can\'t be same.';
            return $this->res;
        } else if (checkEmailUsed($iData['nemail']) > 0) {
            /*
             * check if email already used as primery email in any other account,
             * since we have checked email is same as logged in account email if we have a place that email is used that is
             * another account
             */
            $this->res['status'] = false;
            $this->res['error'] = 'Sorry! Email already associated to another account.';
            return $this->res;
        } else if (!isset($iData['epassword']) || trim($iData['epassword']) == '') {
            $this->res['status'] = false;
            $this->res['error'] = 'Sorry! Password can not be empty.';
            return $this->res;
        } else if (!$this->checkPassword($user_id, trim($iData['epassword']))) {
            //current password not matched
            $this->res['status'] = false;
            $this->res['error'] = 'Sorry! Password is incorrect.';
            return $this->res;
        } else {
            $contactName = $this->ui->getDispalyName($_SESSION['fname'], $_SESSION['lname'], $_SESSION['username'], $_SESSION['email']);
            //check current email2
            $users = $this->db->query("select email2 from user where id = :user_id", array("user_id" => $user_id));
            if ($users[0]['email2'] != trim($iData['nemail'])) {
                //update new email to email2 
                $data = array('user_id' => $user_id, 'email2' => trim($iData['nemail']));
                $sql = "update user set email2=:email2 where id=:user_id";
                $this->db->query($sql, $data);
                logAction('save', 'email2', 'save email2', 'user.class.php, save email2(change email), data : ' . print_r($data, true), true, $this->api, $user_id);

                //send verfication email
                $this->res = $this->emailVerification($user_id, 'email2', trim($iData['nemail']), $contactName, false);
                if ($this->res['status'] == false)
                    return $this->res;
            } else if (!empty($users[0]['email2'])) {
                //email already saved not verified , if verifed email2 must be empty, resend verfication email                
                $this->res = $this->emailVerification($user_id, 'email2', '', $contactName, true);
                if ($this->res['status'] == false)
                    return $this->res;
            }
        }

        $this->res['status'] = true;
        return $this->res;
    }

    /**
     * change user password done for profile page
     * if $checkCurrPass is true we check if current password correct
     * 
     * @param array $iData
     * @param int $user_id
     * @param boolean $checkCurrPass if ture we validate for curent password, else if we don't
     * @return type
     */
    public function changePassword($iData, $user_id, $checkCurrPass = true) {

        if ($checkCurrPass && (!isset($iData['password']) || trim($iData['password']) == '')) {
            $this->res['status'] = false;
            $this->res['error'] = langText('error_password_current_empty');
            return $this->res;
        } elseif (!isset($iData['npassword']) || trim($iData['npassword']) == '') {
            $this->res['status'] = false;
            $this->res['error'] = langText('error_password_new_empty');
            return $this->res;
        } elseif (strlen(trim($iData['npassword'])) < 6) {
            $this->res['status'] = false;
            $this->res['error'] = langText('error_password_too_short');
            return $this->res;
        } else if (!isset($iData['cpassword']) || trim($iData['cpassword']) == '') {
            $this->res['status'] = false;
            $this->res['error'] = langText('error_confirm_password_empty');
            return $this->res;
        } else if (trim($iData['npassword']) != trim($iData['cpassword'])) {
            $this->res['status'] = false;
            $this->res['error'] = langText('error_password_new_confirm_not_match');
            return $this->res;
        } else {
            $user = $this->getUser($user_id, array('salt', 'phash', 'email', 'username'));
            if ($checkCurrPass && !$this->checkPassword($user_id, trim($iData['password']), $user)) {
                //current password not matched
                $this->res['status'] = false;
                $this->res['error'] = langText('error_password_current_incorrect');
                return $this->res;
            } else if ($this->isPasswordContainUsername($iData['npassword'], $user['username'])) {
                //password contain username
                $this->res['status'] = false;
                $this->res['error'] = langText('error_password_contain_username');
                return $this->res;
            } else if ($this->isPasswordContainEmail($iData['npassword'], $user['email'])) {
                //password contain email
                $this->res['status'] = false;
                $this->res['error'] = langText('error_password_contain_email');
                return $this->res;
            } else {
                //update new password
                $hashRes = $this->hashPassword(trim($iData['npassword']), $user['salt']);
                $hash = $hashRes['hash'];
                $data = array('user_id' => $user_id, 'phash' => $hash);
                $sql = "update user set phash=:phash where id=:user_id";
                $this->db->query($sql, $data);
                logAction('save', 'Password', 'save Password', 'user.class.php, save Password, for user_id : ' . $user_id, true, $this->api, $user_id);
            }
        }

        $this->res['status'] = true;
        $this->res['text'] = langText('password_changed_successfully');
        return $this->res;
    }

    /*
     * reset password if user has followed reset link
     * ie: for correct link we have $_SESSION['cp_userid'] set wtih user id
     */

    public function resetPassword($pass, $conPass) {
        if (!isset($_SESSION['cp_userid']) || $_SESSION['cp_userid'] < 1) {
            //invalid request
            $this->res['status'] = false;
            $this->res['error'] = langText('invalid_request');
            return $this->res;
        } else {
            $iData = array('npassword' => $pass, 'cpassword' => $conPass);
            $this->res = $this->changePassword($iData, $_SESSION['cp_userid'], false);
            if ($this->res['status'] === true) {
                //we should unset $_SESSION['cp_userid']
                unset($_SESSION['cp_userid']);
                $this->res['redirect'] = SITE_URL;
                $this->res['text'] = langText('password_reset_successful');
            }
        }
        return $this->res;
    }

    /**
     * set user.state
     * 
     * $state : suspend|delete|activate
     * $emailNotify : true|false (if true send email notification to user about account status update)
     * 
     * NOTE:this is admin function make sure user is admin own before call this for security
     * 
     */
    public function setUserState($userId, $state, $emailNotify = true) {
        $user = $this->getUser($userId, array('status', 'status_date'));

        if (empty($user)) {
            //invalid user
            $this->res['status'] = false;
            $this->res['error'] = 'Sorry! Invalid user.';
            return $this->res;
        }


        $successText = '';
        switch ($state) {
            case 'suspend':
                if ($user['status'] == 'suspended') {
                    //user already suspended, so can't suspend
                    $this->res['status'] = false;
                    $this->res['error'] = 'Sorry! This user has been suspended already.';
                    return $this->res;
                } else if ($user['status'] == 'deleted') {
                    //user already deleted, so can't suspend
                    $this->res['status'] = false;
                    $this->res['error'] = 'Sorry! Cannot suspend users marked as deleted. This user has been marked as deleted.';
                    return $this->res;
                } else {
                    $successText = 'User has been suspended successfully.';
                    $uState = 'suspended';
                }
                break;
            case 'delete':
                if ($user['status'] == 'deleted') {
                    //user already suspended, so can't suspend
                    $this->res['status'] = false;
                    $this->res['error'] = 'Sorry! This user has been deleted already.';
                    return $this->res;
                } else {
                    $successText = 'User has been deleted successfully.';
                    $uState = 'deleted';
                }

                break;
            case 'activate':
                if ($user['status'] == 'active') {
                    //user already suspended, so can't suspend
                    $this->res['status'] = false;
                    $this->res['error'] = 'Sorry! This user is already active.';
                    return $this->res;
                } else {
                    $successText = 'User has been activated successfully.';
                    $uState = 'active';
                }

                break;
            default :
                //invalid request
                $this->res['status'] = false;
                $this->res['error'] = 'Sorry! Invalid user status change request.';
                return $this->res;
        }

        //set user state   
        $data = array('user_id' => $userId, 'status' => $uState);
        $sql = "update user set status=:status,status_date=NOW() where id=:user_id";
        $this->db->query($sql, $data);
        logAction('update', 'user state', 'update user state', 'user.class.php, update user state, data : ' . print_r($data, true), true, $this->api, $userId);

        //send email notification to user
        if ($emailNotify) {
            
        }

        //return success
        $this->res['status'] = true;
        $this->res['text'] = $successText;
        return $this->res;
    }

    /**
     * save support reqest and email to admin and confirmation to user
     */
    public function supportReqest($email, $name, $description, $type = 'contact') {
//first check email
        $email == trim($email);
        $name = trim($name);
        $description = trim($description);

        if ($name == '') {
            $this->res['status'] = false;
            $this->res['error'] = 'Name cannot be empty.';
            return $this->res;
        } else if ($email == '') {
            $this->res['status'] = false;
            $this->res['error'] = 'Email Address cannot be empty.';
            return $this->res;
        } else if ($description == '') {
            $this->res['status'] = false;
            $this->res['error'] = 'Description cannot be empty.';
            return $this->res;
        } else if ($email != '' && !isValidEmailFormat($email)) {
            $this->res['status'] = false;
            $this->res['error'] = 'Please type valid Email Address.';
            return $this->res;
        } else {
            //insert to db
            $data = array('email' => $email, 'name' => $name, 'description' => $description, 'type' => $type);
            $sql = "INSERT INTO support(email,name,description,type) VALUES(:email,:name,:description,:type)";
            $this->db->query($sql, $data);
            logAction('add', 'support-' . $type, 'add support ' . $type . ' request', 'user.class.php,add support ' . $type . ' request, data : ' . print_r($data, true), true, $this->api, $this->userId);

            $supportId = $this->db->insertId();

            //set title for emails
            $title = 'Contact ' . DOMIN_NAME;
            switch ($type) {
                case 'contact':
                    $title = 'Contact ' . DOMIN_NAME;
                    break;
                case 'advertise':
                    $title = 'Advertise with ' . DOMIN_NAME;
                    break;
            }

            //send email to admin
            $adEmailSubject = ucfirst($title) . ' - ' . ucwords($name) . ' (#' . $supportId . ')';
            //get support Reqest To Admin Email content
            $adEmailBody = $this->ui->supportReqestToAdminEmail($adEmailSubject, $email, $name, $description, $title);
            //user array of email if need
            sendEmail(SUPPORT_EMAIL, $adEmailSubject, $adEmailBody, $this->api, $this->userId);

            //advised to not to send emails confirmation since Spammers will use this to send Spam 23/1/2015 
            /*
              //send email to customer
              $usrEmailSubject = ucfirst($title) . ' (#' . $supportId . ')';
              //get support Reqest To user Email content
              $usrEmailBody = $this->ui->supportReqestToUserEmail($usrEmailSubject, $email, $name, $description, $title);
              //user array of email if need
              sendEmail($email, $usrEmailSubject, $usrEmailBody);
             */

            $this->res['status'] = true;
            $this->res['text'] = 'Your request has been submitted successfully.';
        }
        return $this->res;
    }

    /**
     * make sure token genearted is unique with db table check for remember me seriesId
     * this function call recursivly untill get unique value
     * 
     * @$prep prepend value for genearte string, (table row id )
     */
    protected function getUniqueSeriesId($prep = '') {
        $mytoken = token::get();
        $seriesId = $prep . $mytoken;

        $sql = 'select count(*) from remembered_logins where series_id =:series_id';
        $iData = array("series_id" => trim($seriesId));
        $count = $this->db->column($sql, $iData);
        if (intval($count[0]) > 0)
            return $this->getUniqueSeriesId($prep);
        else
            return $seriesId;
    }

    /*
     * return encoded cookied string with other values in array
     */

    protected function getSetCookieData($username, $seriesId) {
//get cryptographically strong reandom string 
//        $tockeLength = 100;
//        $crypto_strong = false;
        $mytoken = token::get(); //openssl_random_pseudo_bytes($tockeLength, $crypto_strong);
//string to save in cookie
        $cookieStr = 'seriesId=' . $seriesId . '&token=' . $mytoken . '&username=' . $username;
        $encodedStr = base64_encode($cookieStr);
        $expire = date('Y-m-d  H:i:s', time() + COOKIE_TIME);
        return array('token' => $mytoken, 'encodedStr' => $encodedStr, 'expire' => $expire);
    }

    /*
     * set remember me cookie and inset/update records to database
     * 
     * 
     * $setCookie : true|fasle if true set cookie usin php
     * $id : remembered_logins.id for update token 
     * $seriesId :  remembered_logins.series_id for update token 
     * 
     * 
      http://stackoverflow.com/questions/244882/what-is-the-best-way-to-implement-remember-me-for-a-website
      used this strategy described here as best practice:
      1.	When the user successfully logs in with Remember Me checked, a login cookie is issued in addition to the standard session management cookie.
      2.	The login cookie contains the user's username, a series identifier, and a token. The series and token are unguessable random numbers from a suitably large space. All three are stored together in a database table.
      3.	When a non-logged-in user visits the site and presents a login cookie, the username, series, and token are looked up in the database.
      1.	If the triplet is present, the user is considered authenticated. The used token is removedfrom the database. A new token is generated, stored in database with the username and the same series identifier, and a new login cookie containing all three is issued to the user.
      2.	If the username and series are present but the token does not match, a theft is assumed. The user receives a strongly worded warning and all of the user's remembered sessions are deleted.
      3.	If the username and series are not present, the login cookie is ignored.

     */

    protected function setRememberMe($username, $userId, $id = 0, $seriesId = '', $setCookie = false) {

        //get ip of client
        $ip = getClientIp();
        //for getUniqueSeriesId and getSetCookieData
        require_once "logic/base/token.class.php";

        if (empty($seriesId) && empty($id)) {

            //insert mode            
            $seriesId = $this->getUniqueSeriesId();
        }

        $cookieData = $this->getSetCookieData($username, $seriesId);
        $token = $cookieData['token'];
        $encodedStr = $cookieData['encodedStr'];
        $expire = $cookieData['expire'];

        if (empty($id)) {
            //insert new mode
            $sql = 'insert into remembered_logins(username,user_id,token,series_id,ipaddr,created,expire)' .
                    ' values(:username,:user_id,:token,:series_id,:ipaddr,NOW(),:expire)';
            $data = array(
                'username' => $username, 'user_id' => $userId,
                'token' => $token, 'series_id' => $seriesId, 'ipaddr' => $ip,
                'expire' => $expire
            );
        } else {
            //update tocken mode
            $sql = 'update remembered_logins set 
                    token=:token,ipaddr=:ipaddr,expire=:expire' .
                    ' where id=:id and user_id=:user_id';
            $data = array('id' => $id, 'user_id' => $userId, 'token' => $token, 'ipaddr' => $ip, 'expire' => $expire);
        }

        $this->db->query($sql, $data);
        logAction('set', 'cookie', 'set cookie', 'user.class.php ,cookie for user: ' . $userId, true, $this->api, $userId);

        if ($setCookie) {
            //unset old if exist
            $this->unsetRememberCookie();
            //set new
            $this->setRememberCookie($encodedStr);
        }

        return array('encodedStr' => $encodedStr, 'expire' => $expire);
    }

    /*
     * unset remeber me cookie
     */

    protected function unsetRememberCookie() {
        setcookie(COOKIE_NAME, "", time() - 3600);
    }

    /*
     * set remeber me cookie
     */

    protected function setRememberCookie($encodedStr) {
        setcookie(COOKIE_NAME, $encodedStr, time() + COOKIE_TIME, '/', COOKIE_DOMAIN);
    }

    /*
     * return home page page content
     */

    public function loadHomePage() {
        //get home page content
        $centent = homePageContent();

        $this->res['cnt'] = $centent; //content to replace center part
        $this->res['text'] = '';
        $this->res['status'] = true;

        return $this->res;
    }

    /*
     * check is user locked and return true if locked 
     * 
     * even if correct password entered if locked not allow to log in untill time up
     * remove lock after time up allowed to log in
     * 
     * if $clearHistory is ture clear login failed history after check done
     * this is done to clear hostory on sign in and after time up at same function and ignore same query twice execuitng
     */

    protected function isUserLoked($user_id, $clearHistory = false) {
        $lockout = false;
        $user = $this->getUser($user_id, array('locked'));
        //check if locked
        if ($user['locked'] == '1') {
            $lockout = true;
            //login is locked, check if lockout time up,get last failed logins
            $sql = 'SELECT * FROM login_failed where user_id=:user_id ORDER BY failed_on DESC LIMIT 1';
            $lastFailed = $this->db->row($sql, array('user_id' => $user_id));

            //calcualted locked off time stamp
            $lockOffTS = strtotime($lastFailed['failed_on']) + (LOGIN_LOCKOUT_TIME * 60);

            if (time() >= $lockOffTS) {
                //clokout time up, take locked off
                $this->unlockUser($user_id);
                //make sure remove failed login history for user
                $clearHistory = true;
                $lockout = false;
            } else {
                //make sure not clear failed login history for user untill lockout time out
                $clearHistory = false;
            }
        }

        if ($clearHistory) {
            //remove failed login history for user
            $this->clearFailedLoginHistory($user_id);
        }
        return $lockout;
    }

    /*
     * for limit failed login attems
     * 
     * insert failed login details to login_failed 
     * lockout login user if need
     * 
     * @return  return true if lockout now or already locked out
     */

    protected function limitFailedLoginAttempts($user_id) {
        $lockout = false;
        $user = $this->getUser($user_id, array('locked'));
//if already locked we don't insert failed login records, to prevent extending lockout time unnecessarily 
        if ($user['locked'] == '0') {
            //insert to fm_login_failed
            $this->addFailedLoginAttempt($user_id);
            //login falied, get count of failed logins before LOGIN_ATTEMPTS_DURATION minutes
            $from_ts = time() - (LOGIN_ATTEMPTS_DURATION * 60);
            $from_dt = date('Y-m-d H:i:s', $from_ts);
            $sql = 'SELECT count(*) as faild FROM login_failed'
                    . ' WHERE user_id=:user_id AND failed_on>=:failed_on'
                    . ' ORDER BY failed_on DESC';
            $count = $this->db->column($sql, array('user_id' => $user_id, 'failed_on' => $from_dt));
            $lastDurationFailsCount = intval($count[0]);
            //if we have failed LOGIN_ATTEMPTS_LIMIT more than we allow during LOGIN_ATTEMPTS_DURATION time we lock user
            //ex: if 3 logins afftems allowed when 4th one get wrong we block user
            if ($lastDurationFailsCount > LOGIN_ATTEMPTS_LIMIT) {
                //lockout user
                $this->lockUser($user_id);
                $lockout = true;
            }
        } else {
            //already locked 
            $lockout = true;
        }

        return $lockout;
    }

    /*
     * add failed login attempt
     */

    protected function addFailedLoginAttempt($user_id) {
        $sql = 'insert into login_failed(user_id,failed_on,ip_address) values(:user_id,NOW(),:ip_address)';
        $ip_address = getClientIp();
        $iData = array('user_id' => $user_id, 'ip_address' => $ip_address);
        $this->db->query($sql, $iData);
        logAction('failed', 'login', 'login failed', 'user.class.php, for user user.id=' . $user_id . ' login failed.', true, $this->api, $user_id);
    }

    /*
     * Clear Failed Login History for given user
     */

    protected function clearFailedLoginHistory($user_id) {
        $sql = 'DELETE FROM login_failed WHERE user_id=:user_id';
        $this->db->query($sql, array('user_id' => $user_id));
        logAction('clear', 'login failed history', 'clear login failed history', 'user.class.php, for user user.id=' . $user_id . ' login failed history has been cleared.', true, $this->api, $user_id);
    }

    /*
     * lock user temporary 
     * use when failed login attempts happened  
     */

    protected function lockUser($user_id) {
        $sql = 'update user set locked=1 WHERE id=:user_id';
        $this->db->query($sql, array('user_id' => $user_id));
        logAction('lock', 'user', 'lock user', 'user.class.php, user user.id=' . $user_id . ' has been locked.', true, $this->api, $user_id);
//user has been lockout, make sure all remember me removed for security
        $this->clearRememberMeData($user_id);

//send email to user saying that account locked due to continual failed login attempts 
        if (EMAIL_LOCKOUT_NOTIFICATION) {
            //get user information
            $user = $this->getUser($user_id, array('email', 'fname', 'lname', 'username'));
            //get contact name
            $cont_name = $this->ui->getDispalyName($user['fname'], $user['lname'], $user['username'], $user['email']);
            //email subject
            $emailSubject = DOMIN_NAME . ' account locked out temporally!';
            //get lock User Email Content
            $emailBody = $this->ui->lockUserEmailContent($emailSubject, $cont_name);
            //send email
            sendEmail($user['email'], $emailSubject, $emailBody, $this->api, $user_id);
        }
    }

    /*
     * unlock user from temporary lock
     * use when failed login attempts happened  
     */

    protected function unlockUser($user_id) {
        $sql = 'update user set locked=0 WHERE id=:user_id';
        $this->db->query($sql, array('user_id' => $user_id));
        logAction('unlock', 'user', 'unlock user', 'user.class.php, user user.id=' . $user_id . ' has been unlocked.', true, $this->api, $user_id);
    }

    /**
     * return privacy policy page content
     */
    public function privacyPolicy($markup = false) {
//get privacy policy page content html
        $final_markup = $this->ui->privacyPolicy();

        if ($markup)
            return $final_markup;
        else {
            $this->res['cnt'] = $final_markup; //content to replace center part
            $this->res['text'] = '';
            $this->res['status'] = true;

            return $this->res;
        }
    }

    /**
     * return about us page content
     */
    public function aboutUs($markup = false) {
//get about Us page content html
        $final_markup = $this->ui->aboutUs();

        if ($markup)
            return $final_markup;
        else {
            $this->res['cnt'] = $final_markup; //content to replace center part
            $this->res['text'] = '';
            $this->res['status'] = true;

            return $this->res;
        }
    }

    /**
     * return terms of service page content
     */
    public function termsOfService($markup = false) {
//get terms Of Service page content html
        $final_markup = $this->ui->termsOfService();

        if ($markup)
            return $final_markup;
        else {
            $this->res['cnt'] = $final_markup; //content to replace center part
            $this->res['text'] = '';
            $this->res['status'] = true;

            return $this->res;
        }
    }

    /**
     * make sure token genearted is unique with db table check for api token
     * this function call recursivly untill get unique value
     * 
     */
    protected function setUniqueApiToken($userId) {
        $mytoken = token::get();

        $sql = 'select count(*) from device where token =:token';
        $iData = array("token" => $mytoken);
        $count = $this->db->column($sql, $iData);
        if (intval($count[0]) > 0)
            return $this->getUniqueApiToken($userId);
        else {
            $lastloginip = getClientIp();
            $device_make = '';
            $device_model = '';
            $expire = date('Y-m-d  H:i:s', time() + COOKIE_TIME);
            //add token to db
            $data = array('user_id' => $userId, 'token' => $mytoken, 'registered_ip' => $lastloginip,
                'device_make' => $device_make, 'device_model' => $device_model, 'lastused_ip' => $lastloginip,
                'token_expire' => $expire);
            $sql = "INSERT INTO device
                    (user_id,token,registered_ip,lastused_dt,device_make,device_model,lastused_ip,token_expire) 
                    VALUES
                    (:user_id,:token,:registered_ip,NOW(),:device_make,:device_model,:lastused_ip,:token_expire)";
            $this->db->query($sql, $data);
            logAction('add', 'api login token', 'add api login token ,user.class.php, data : ' . print_r($data, true), true, $this->api, $userId);

            return $mytoken;
        }
    }

    /**
     * set user langage settign to session to use later for user id given
     * 
     * @param int $user user id
     */
    protected function setUserLangageToSession($userId) {
        //set lanuage code and file name for later use
        $lanSettng = $this->lanuageSettings($userId);
        if (!empty($lanSettng)) {
            $_SESSION['language_code'] = $lanSettng['code'];
            $_SESSION['language_file_name'] = $lanSettng['file_name'];
            //update default langaues also
            $_SESSION['language_code_default'] = $lanSettng['code'];
            $_SESSION['language_file_name_default'] = $lanSettng['file_name'];
            $_SESSION['language_recaptchar_code_default'] = $lanSettng['recaptchar_code'];
        } else {
            //set default lanuage if no setting found with getDefaultLanguage
            getDefaultLanguage();
            $_SESSION['language_code'] = $_SESSION['language_code_default'];
            $_SESSION['language_file_name'] = $_SESSION['language_file_name_default'];
        }
    }

    /**
     * return lanuage settings for requested user
     * @param type $userId
     */
    protected function lanuageSettings($userId) {
        $sql = 'select ls.* from user u
                left join languages ls on u.language_id=ls.id AND ls.enabled=1 
                where u.id=:id limit 1';
        $langRow = $this->db->row($sql, array('id' => $userId));
        if ($this->db->RowCount() >= 1 && !empty($langRow['file_name'])) {
            //we have found a language
            return $langRow;
        }else
            return array();
    }

    /**
     * return signed in user language file name 
     */
    public function signedInUserLangFileName() {
        $langFileName = '';
        if (!$this->api && is_logged() && !empty($_SESSION['language_file_name'])) {
            $langFileName = $_SESSION['language_file_name'];
        }

        return $langFileName;
    }

    /**
     * return signed in user language code 
     */
    public function signedInUserLangCode() {
        $langCode = '';
        if (!$this->api && is_logged() && !empty($_SESSION['language_code'])) {
            $langCode = $_SESSION['language_code'];
        }

        return $langCode;
    }

    /**
     * change language 
     * this is not for to change or update logged in user lanage 
     * and this for cases user havent logged in
     * 
     * @param type $code
     * @return type
     */
    public function changeLanguage($code) {

        if (trim($code) == '' || is_logged()) {
            $this->res['status'] = false;
            $this->res['error'] = 'Sorry! Invalid request.';
            return $this->res;
        } else {
            setLanguage($code, true);
            $this->res['status'] = true;
        }
        return $this->res;
    }

}

?>