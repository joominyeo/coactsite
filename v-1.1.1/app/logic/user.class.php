<?php

/*
 * all user related functions goes here
 */
require_once "base/user.base.class.php";

class user extends userBase {

    public function __construct($userId = 0, $api = false) {
        parent::__construct($userId, $api);
    }

    /**
     * echeck if email available return status= true if available else return false
     * 
     * @param string $email
     * @return array
     */
    public function signUpCheckEmailAvailable($email) {
        if (trim($email) == "") {
            $this->res['status'] = false;
            $this->res['error'] = langText('error_email_empty');
            return $this->res;
        } else if (!validateMaxLength($email, 75)) {
            $this->res['status'] = false;
            $this->res['error'] = langText('error_email_too_long');
            return $this->res;
        } elseif (!isValidEmailFormat($email)) {
            $this->res['status'] = false;
            $this->res['error'] = langText('error_email_format');
            return $this->res;
        } else if (checkEmailUsed($email) > 0) {
            //make sure unique email
            $this->res['status'] = false;
            $this->res['error'] = langText('error_email_exist');
            logAction('sign up failed', 'user', 'user sign up failed', 'user.class.php, user sign up failed, error : [' . $this->res['error'] . ']', true, $this->api, $this->userId);
            return $this->res;
        }

        //all ok
        $this->res['status'] = true;
        return $this->res;
    }

    /**
     * direct page load home page sing up
     * 
     * @param array $data data to populate sing up fields
     * 
     * @return array
     */
    public function directLoadSignUpPage($data = array()) {
        $signUpPage = $this->signUpPage(true, $data);
        $footJs = '$( document ).ready(function() {  
                        setCustomSectionBackground(true);
                   });';

        require_once "ui/ui.landpage.class.php";
        $uiLandPage = new uiLandPage();
        //get complete page content with sign up
        $cnt = $uiLandPage->getLandingPageUi($signUpPage, '');

        return array('cnt' => $cnt, 'footJs' => $footJs);
    }
    
        /**
     * direct page load home page sing in
     * 
     * @param array $data data to populate sing up fields
     * 
     * @return array
     */
    public function directLoadSignInPage($data = array()) {
        $signUpPage = $this->signInPage(true, $data);
        $footJs = '$( document ).ready(function() {  
                        setCustomSectionBackground(true);
                        setElementVerticalCenter(\'#custom-section > .container-fluid > .row\',50,200);
                   });';

        require_once "ui/ui.landpage.class.php";
        $uiLandPage = new uiLandPage();
        //get complete page content with sign up
        $cnt = $uiLandPage->getLandingPageUi($signUpPage, '');

        return array('cnt' => $cnt, 'footJs' => $footJs);
    }

    /**
     * return sign up page content
     * 
     * @param array $data data to populate sing up fields
     */
    public function signUpPage($markup = false, $data = array()) {
        $langs = languageList();
        //create option array to send to sign up page, set languages array       
        $options = array('langs' => $langs);

        //set default lanuage code (current language) to populate
        $langRes = getDefaultLanguage();
        //get languages.id value for languages.code
        $langId = getLanguageIdByCode($langRes['code']);
        $options['langId'] = $langId;

        $schoolSelect = $this->schoolSelect(0, 'school', 'school', '', '', 'input-medium form-control', '');
        $options['schoolSelect'] = $schoolSelect;

        $gradeSelect = $this->gradeSelect(0, 'grade', 'grade', '', '', 'input-medium form-control', '');
        $options['gradeSelect'] = $gradeSelect;

        //get profile page content html
        $final_markup = $this->ui->signUpPageMrkup($options);

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
            "hash" => $hash, "salt" => $salt, 'verified' => $verified,
            "school_id" => $input['school'], "grade" => $input['grade']);
        $sql = "insert into user (status,fname,lname,email,username,php_tz_identifier,phash,salt,created,verified,school_id,grade)" .
                " values ( :status, :fname, :lname,:email,:username,:php_tz_identifier, :hash, :salt,NOW(),:verified,:school_id,:grade)";
        $this->db->query($sql, $data);

        $id = $this->db->insertId();
        //unset login has so not goes to logs
        unset($data['hash']);
        logAction('sign up', 'user', 'user sign up', 'user.class.php, user sign up, data : [' . print_r($data, true) . ']', true, $this->api, $id);

        return $id;
    }

    /**
     * return school Select element
     * 
     *
     * @param int $schoolId contry Id to poulate 
     * @param string $name element name
     * @param string $id element id
     * @param string $extraAttributes extra attribute ex: set to 'disable="disable"'
     * @param string $onchange javascript function/s to execute on change
     * @param string $class class/es
     * @param string $style style codes
     * @return string
     */
    protected function schoolSelect($schoolId = 0, $name = '', $id = '', $extraAttributes = '', $onchange = '', $class = '', $style = '') {

        //get school list
        $schools = $this->schoolList();

        $select = $this->ui->schoolSelect($schools, $schoolId, $name, $id, $extraAttributes, $onchange, $class, $style);
        return $select;
    }

    /**
     * return enabled school list ascending order
     */
    public function schoolList() {
        $sql = 'SELECT id,name 
                FROM school WHERE disabled=0 AND deleted=0 
                ORDER BY school.name ASC ';
        $schools = $this->db->query($sql, array());

        if ($this->db->RowCount() > 0) {
            return $schools;
        } else {
            return array();
        }
    }

    /**
     * return grade Select element
     * 
     *
     * @param int $gradeId grade Id to poulate 
     * @param string $name element name
     * @param string $id element id
     * @param string $extraAttributes extra attribute ex: set to 'disable="disable"'
     * @param string $onchange javascript function/s to execute on change
     * @param string $class class/es
     * @param string $style style codes
     * @return string
     */
    protected function gradeSelect($gradeId = 0, $name = '', $id = '', $extraAttributes = '', $onchange = '', $class = '', $style = '') {

        //get grade list
        $grades = $this->gradeList();

        $select = $this->ui->gradeSelect($grades, $gradeId, $name, $id, $extraAttributes, $onchange, $class, $style);
        return $select;
    }

    /**
     * return enabled grade list ascending order
     */
    public function gradeList() {

        $grades = array('9' => 'Grade 9', '10' => 'Grade 10', '11' => 'Grade 11', '12' => 'Grade 12');

        return $grades;
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
        } else if (trim($input['school']) == "") {
            $res['state'] = false;
            $res['error'] = langText('error_school_empty');
        } else if (trim($input['grade']) == "") {
            $res['state'] = false;
            $res['error'] = langText('error_grade_empty');
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

}

?>