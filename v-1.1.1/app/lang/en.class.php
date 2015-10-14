<?php

/**
 * language content are move in to a class for better performance and easy use. 
 * functions are static so we can use like below 
 * when naming file please follow format en.class.php en is to  indicate English 
 * ex: it.class.php for Italian 
 * 
 * do not change class name Lang or public static function langText or any funciton in this class , 
 * you only change $lang values and add new values if need.
 * do not removed any values from $lang.
 * 
 * usage example 1:
 * Lang::langText('keynanme');
 * 
 * usage example 2:
 * $lan=new Lang();
 *  $lan->langText('keynanme');
 * 
 * you can set parameters for text as below
 * in $lang array value text param keys must be like {{{KEYNAME}}} 
 * key name must to upper case, there must not any spaces left brackets and key name 
 * {{{KEYNAME}}}
 * ex: Welcome back {{{NAME}}}
 * 
 * when call $lan->langText('keynanme') you can pass $params array
 * 
 * ex:
 * $params=array('COUNT'=>5,'NAME'=>'My Mame');
 * $lan->langText('keynanme',$params);
 * 
 * keep in mind if you add params to $lang array texts you must pass values from them using $params array
 */
class Lang {

    /**
     *  language array
     *  all text you need to add place here with unique index, you can use comments to make clear sections 
     *  keep most important ones, common etc at bottom so it will not replaces by mistake.
     *  Index is word replaced spaces with underscore, there may me some words preceded to index name to make sure uniqueness if needed 
     */
    public static $lang = array(
        //nav bar
        //    
        "remember_me" => "Remember Me",
        "forgot_password" => "Forgot Password",
        //profile
        "profile_user_settings" => 'User Settings',
        "change_email" => "Change Email",
        "change_password" => "Change Password",
        "active_email" => "Active Email",
        "new_email" => "New Email",
        "current_password" => "Current Password",
        "new_password" => "New Password",
        "confirm_password" => "Confirm Password",
        "username" => "Username",
        "username_or_email" => "Username or Email",
        "your_username" => "Your username",
        "your_public_profile" => "Your public profile",
        "new_email_address" => "New Email Address",
        //other 
        "salutation" => "Hi",
        //common
        "sign_up" => "Sign Up",
        "sign_in" => "Login",
        "email" => "Email",
        "password" => "Password",
        "contact_us" => "Contact us",
        "first_name" => "First Name",
        "last_name" => "Last Name",
        "save" => "Save",
        "about" => "About",
        "time_zone" => "Time Zone",
        "email_address" => "Email Address",
        "events" => "Events",
        "comments" => "Comments",
        "continue" => "Continue",
        "sign_in_instead" => "Log In Instead",
        "already_signed_up_q" => "Already signed up?",
        "resend_verification" => "Resend Verification",
        "welcome_back" => "Welcome back {{{NAME}}} !",
        "you_just_sign_out" => "You just sign out. See you later {{{NAME}}}!",
        "login_successful" => "Login Successful",
        "back" => "Back",
        "sign_up_confirmation_subject" => "Sign Up Confirmation - {{{SITE_NAME}}}!",
        "confirm_your_new_email_subject" => "Confirm your new email address!",
        "reset_password_subject" => "Reset {{{SITE_NAME}}} password!",
        "thank_you_signing_up" => "Thank you {{{NAME}}} for signing up.",
        "verifications_email_resent" => "Verifications email has been resent.",
        "verifications_finalize_your_registration" => "You will be receiving an e-mail with a link to confirm and finalize your registration.",
        "verifications_email_content_sign_up" => "Click on this <a href=\"{{{URL}}}\">link</a> to verify your email address. You can also copy paste this URL <a href=\"{{{URL}}}\">{{{URL}}}</a> to your browser's address bar.",
        "email_signature" => "- Folks at {{{SITE_NAME}}}",
        "welcome_to" => "Welcome to {{{SITE_NAME}}} !",
        "email_verified_successfully" => "Email address has been verified successfully.",
        "thank_you_confirming_registration" => "Thank you {{{NAME}}} for confirming your registration.{{{APPEND}}}",
        "enter_your_registered_email" => "Enter your registered email address. Click next to receive instructions <br>for resetting your password to your email address.",
        "close" => "Close",
        "next" => "Next",
        "reset_password" => "Reset Password",
        "reset_password_email_messsage" => "Please click on this <a href=\"{{{URL}}}\">link</a> to reset your password or copy paste URL {{{URL}}} to your Browse address bar.",
        "reset_password_link_sent" => "Password reset link has been sent to your email address.<br>Please follow instruction in the email.",
        "enter_new_password_and_save" => "Please enter your new password and click on \"Save\" button",
        "password_reset_successful" => "Password reset successful.",
        "password_changed_successfully" => "Password has been changed successfully.",
        //validations and errors        
        "invalid_request" => "Sorry! Invalid request.",
        "error_email_exist" => "Sorry! Email already exist.",
        "error_access_denied_country" => "Sorry ! Access denied for your country.",
        "error_access_denied_ip" => "Sorry ! Access denied for your IP Address.",
        "error_already_logged" => "Invalid request. Already logged in.",
        "error_first_name_empty" => "First Name can not be empty.",
        "error_first_name_too_long" => "First Name can not be longer than 50 characters.",
        "error_first_name_format" => "Invalid First Name, First Name can be only alpha numeric and must start with a letter.",
        "error_last_name_empty" => "Last Name can not be empty.",
        "error_last_name_too_long" => "Last Name can not be longer than 50 characters.",
        "error_last_name_format" => "Invalid Last Name, Last Name can be only alpha numeric and must start with a letter.",
        "error_email_empty" => "Email Address can not be empty.",
        "error_email_too_long" => "Email Address can not be longer than 75 characters.",
        "error_email_format" => "Please type valid Email Address.",
        "error_email_not_verified" => "Sorry! Email not verified.",
        "error_password_empty" => "Password can not be empty.",
        "error_password_too_short" => "Password can not be less than 6 charaters.",
        "error_password_contain_username" => "Sorry! Password contain username.",
        "error_password_contain_email" => "Sorry! Password contain email.",
        "error_password_current_empty"=>"Sorry! Current Password can not be empty.",
        "error_password_new_empty" => "Sorry! New Password can not be empty.",
        "error_password_new_confirm_not_match" => "Sorry! New Password and confirm password does not match.",
        "error_password_current_incorrect" => "Sorry! Current Password is incorrect.",
        "error_confirm_password_empty" => "Confirm Password can not be empty.",
        "error_passwords_not_match" => "Passwords do not match.",
        "error_username_empty" => "Username can not be empty.",
        "error_recaptcha_empty" => "Recaptcha cannot be empty.",
        "error_recaptcha_incorrect" => "The recaptcha wasn't entered correctly.",
        "error_username_or_email_empty" => "Username Or Email cannot be empty.",
        "error_account_deleted" => "Sorry! This account has been deleted.",
        "error_account_suspended" => "Sorry! This account has been suspended.",
        "error_account_locked" => "Sorry! Your account has been locked out temporally.",
        "error_locked_try_again_after" => "Please try again after {{{LOGIN_LOCKOUT_TIME}}} minutes.",
        "error_invalid_email_password" => "Sorry! Invalid Email or Password.",
        "error_invalid_username_email_password" => "Sorry! Invalid Username, Email or Password.",
        "error_code_expired" => "Sorry! Code has been expired.",
        "error_email_already_verified" => "Email address has been verified already.",
        "error_data_found" => "Sorry! No data found for your request.",
        ///custom site langs all after there
        "school" => "School",
        "grade" => "Grade",
        "change_name"=>"Change Name",
        "username_infor" => "Username Information",
        //custom site error
        "error_school_empty" => "School can not be empty.",
        "error_grade_empty" => "Grade can not be empty.",
    );

    // "" => "",

    /**
     * lang for js usage 
     * we don't need all above so we add only what we want here
     * same rules for $lang applies to $langJs also
     * 
     * @var type 
     */
    public static $langJs = array(
        "error_first_name_empty" => "First Name can not be empty.",
        "error_last_name_empty" => "Last Name can not be empty.",
        "error_email_empty" => "Email Address can not be empty.",
        "error_password_empty" => "Password can not be empty.",
        "error_password_too_short" => "Password can not be less than 6 charaters.",
        "error_confirm_password_empty" => "Confirm Password can not be empty.",
        "error_passwords_not_match" => "Passwords do not match.",
        "error_username_empty" => "Username can not be empty.",
        "error_recaptcha_empty" => "Recaptcha cannot be empty.",
        "error_recaptcha_loading" => "Please wait! Recaptcha is loading.",
        "error_name_empty" => "Name can not be empty.",
        "error_description_empty" => "Description cannot be empty.",
        "error_username_or_email_empty" => "Username Or Email cannot be empty.",
        "forgot_password" => "Forgot Password?",
        "contact_us" => "Contact Us",
        "advertise_with_us" => "Advertise with us",
        //custom site  langs all after there
        "error_school_empty" => "School can not be empty.",
        "error_grade_empty" => "Grade can not be empty.",
    );

    /**
     * return text matched to key in $lang array
     * if we initiate object can't accss $lang directly
     * 
     * @param type $key
     * @param array $params parameters to replace variabeles in string ex:array('COUNT'=>5,'NAME'=>'My Mame')     * 
     * @return string if key found resurn string if not return empty
     */
    public static function langText($key, $params = array()) {
        $val = '';
        if (!empty(self::$lang[$key])) {
            $val = self::$lang[$key];
//            print_r($params);
            //if has parms parse them
            if (!empty($params))
                $val = self::paraseLangParams($val, $params);
        }

        return $val;
    }

    /**
     * parase given params , in text to parse paramms param keys myst be like, 
     * key name must to uppper case in text, there must not any spaces left brakets and key name
     * {{{KEYNAME}}}
     * ex: welcome back {{{NAME}}}
     * 
     * @param array $params parameters to replace variabeles in string ex: array('COUNT'=>5,'NAME'=>'My Mame')
     */
    private static function paraseLangParams($val, $params = array()) {

        foreach ($params as $key => $param) {
            $val = str_replace('{{{' . strtoupper($key) . '}}}', $param, $val);
        }
        return $val;
    }

    /**
     * return js lang array json encoded
     * 
     * @param bool $jsonEncode if true return json encode language array to use in js
     */
    public static function getJsLang($jsonEncode = true) {
        if ($jsonEncode)
            return json_encode(self::$langJs);
        else
            return self::$langJs;
    }

}

?>