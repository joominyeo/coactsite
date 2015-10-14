//to hold current page layout name in case if we need
var current_page='';

$( document ).ready(function() {    
    init();   
    
    //scrol to top
    scrollToViewTop();
});


function init(){    
    //Handles menu drop down and make sure form in drop down does not submit and relaod page
    $('.dropdown-menu').find('form').click(function (e) {
        e.stopPropagation();
    }).submit(function( event ) {
        event.preventDefault();
    });
        
}

/*
 *show nofication alert
 *msg : msg to show in alert
 *msgType:success|info|warning|danger (optinal)
 *fade: true|false (optinal)
 *fadeoutDelay:delay (in ms) (optinal)
 *closable:true|false (Allow alert to be closable through a close icon.) (optinal)
 *html:true|false (optinal)
 */
function showNotificationAlert(msg,msgType,fade,fadeDelay,closable,html){
    if(typeof msg=='undefined')
        return;
    else if(msg=='')
        return;
    
    if(typeof msgType=='undefined')
        msgType='danger';//Alert style, omit alert- from style name of bootstrap.
    if(typeof fade=='undefined')
        fade=true;
    if(typeof fadeoutDelay=='undefined' || parseInt(fadeDelay)<1)
        fadeDelay=1000;
    if(typeof closable=='undefined')
        closable=true;
    if(typeof html!='undefined' && !html){
        html=false;
    }else{
        html=msg;
        msg=false;  
    }

    //make sure old message hide if not already hidden or closed
    $('#alert-top').html('');
    //show alert
    $('#alert-top').notify({
        message: {
            html: html, 
            text: msg
        },
        type:msgType,
        closable:closable,
        fadeOut: {
            enabled: fade, 
            delay: fadeDelay
        }
    }).show();
    //hide x if fade is ture
    if(fade===true){
        if(!$('#alert-top .close').hasClass('hide'))
            $('#alert-top .close').addClass('hide');
    }
    
}

/*
 * show modal dialog instead js confirma and any place need confimation from use
 * msg : msg to show in alert
 * yesCallback: call back fucntion to execute on yes
 * noCallback: call back fucntion to execute on no
 */
function showConfirm(msg,yesCallback,noCallback){
    if(typeof yesCallback!='undefined') yesCallback=yesCallback+';';
    else yesCallback='';
    
    if(typeof noCallback!='undefined') noCallback=noCallback+';';
    else noCallback='';
    
    var ctHtml='<h4 class="confirm-msg">'+msg+'</h4>';
    ctHtml+='<div style="text-align:center;padding-top:10px;"><input type="button" value="YES" onclick="$.fancybox.close(true);'+yesCallback+'" class="btn btn-sm btn-default">';
    ctHtml+='<input type="button" style="margin-left:15px;" value="NO" onclick="$.fancybox.close(true);'+noCallback+'" class="btn btn-sm btn-default"></div>';
    
    $.fancybox({
        content:ctHtml,
        closeBtn:false,
        margin :[5,5,5,5],
        padding:[15,20,5,20],
        maxWidth:'80%',
        minWidth:'10%',
        autoSize:true,
        autoResize:true,
        autoCenter:true,
        fitToView:true,
        nextClick :false,
        scrolling:'no',
        arrows :false,        
        openEffect: 'elastic',
        closeEffect: 'elastic'
    
    });
}

//valdiate sign up before send ajax request and show errors
function validateSignUp(){
    if(SIGNUP_NAME=="1" && $.trim($('#fname').val())==''){
        showNotificationAlert(langText('error_first_name_empty')); 
        return false;
    }else if(SIGNUP_NAME=="1" && $.trim($('#lname').val())==''){
        showNotificationAlert(langText('error_last_name_empty')); 
        return false;
    }else if($.trim($('#school').val())==''){
        showNotificationAlert(langText('error_school_empty')); 
        return false;
    }else if($.trim($('#grade').val())==''){
        showNotificationAlert(langText('error_grade_empty')); 
        return false;
    }else if($.trim($('#email').val())==''){
        showNotificationAlert(langText('error_email_empty')); 
        return false;
    }else if($.trim($('#password').val())==''){
        showNotificationAlert(langText('error_password_empty'));
        return false;
    }else if($('#password').val().length< 6){
        showNotificationAlert(langText('error_password_too_short'));
        return false;
    }else if(SIGNUP_PASS2=="1" &&  $.trim($('#cpassword').val())==''){
        showNotificationAlert(langText('error_confirm_password_empty'));
        return false;
    }else if(SIGNUP_PASS2=="1" &&  $('#cpassword').val()!=$('#password').val()){
        showNotificationAlert(langText('error_passwords_not_match'));
        return false;
    }else if(USERNAME=="1" && $.trim($('#su_username').val())==''){
        showNotificationAlert(langText('error_username_empty'));
        return false;
    } else if(SIGNUP_CAPTCHAR=="1" &&  
        ((SIGNUP_CAPTCHAR_VERSION=="1" && $('#recaptcha_iframe').contents().find('#recaptcha_response_field').length==0)
            || (SIGNUP_CAPTCHAR_VERSION=="2" ) && $('#site_recaptcha_col .g-recaptcha').length==0)){
        showNotificationAlert(langText('error_recaptcha_loading'));
        return false;
    } else if(SIGNUP_CAPTCHAR=="1"  && 
        ((SIGNUP_CAPTCHAR_VERSION=="1" &&  $.trim($('#recaptcha_iframe').contents().find('#recaptcha_response_field').val())=='')
            || (SIGNUP_CAPTCHAR_VERSION=="2" && grecaptcha.getResponse()==''))){
        
        showNotificationAlert(langText('error_recaptcha_empty'));
        return false;
    }
     
    //all ok
    return true;
}

function signUp(){
    if(validateSignUp()){
        setLoadingMarkup($('body'),'add','topDiv', 'small', '','100%', 'auto',true);
    
        var recaptcha_response_field='';
        var recaptcha_challenge_field='';
        if($('#recaptcha_iframe').contents().find('#recaptcha_response_field').length>0){
            recaptcha_response_field=$('#recaptcha_iframe').contents().find('#recaptcha_response_field').val();
            recaptcha_challenge_field=$('#recaptcha_iframe').contents().find('#recaptcha_challenge_field').val()
        }
        
        $.post(SITE_URL+"/?c=user_aj", {
            action: 'signup',
            data:$('#fmSignUp').serialize(),
            recaptcha_response_field:recaptcha_response_field,
            recaptcha_challenge_field:recaptcha_challenge_field,
            tzOffset:getTzOffset(),
            csrf:getCsrf()
        },
        function(a) {
            var result = JSON.parse(a);
            
            if(result.status){            
                //ok we are good ,if need something can do here
                //change set url to home in browser address bar and do not relaod
                ChangeUrl('',false);
                //empty sustom section
                $('#custom-section').html('');
                scrollViewTo('#specialannouncements');
                //                //if content to replace center ara
                //                if(result.cnt!='') $('#custom-section').html(result.cnt);
                //show notification
                showNotificationAlert(result.text,'success',false,3000,true,true); 
            
            }else{ 
                //set new rechapthar
                if(SIGNUP_CAPTCHAR_VERSION=="1" && typeof result.recaptcha!='undefined' && result.recaptcha!='')
                    $('#site_recaptcha_col').html(result.recaptcha);
                else
                    grecaptcha.reset();
                //show error notification
                ajaxErrAlert(result);
                
            }
            setLoadingMarkup($('body'),'remove','topDiv', 'small', '','100%', 'auto',true);
    
        });
    }
}

function signIn(){
    setLoadingMarkup($('body'),'add','topDiv', 'small', '','100%', 'auto',true);
    
    $.post(SITE_URL+"/?c=user_aj", {
        action: 'signin',
        data:$('#fmSignIn').serialize(),
        csrf:getCsrf()
    },
    function(a) {
        var result = JSON.parse(a);
        //set csrf
        setCsrf(result.csrf);
        
        if(result.status){  
            deleteRememberMeCookie();    
            
            //set admin
            admin=result.admin;
            //set/update nav panel
            setNavPanel(result.snav);
            setContactUsPanel(result);
            var contSection='school-section';
            if(admin==true){
                //after sign in we change url to home and but do not relaod
                ChangeUrl('',false);  
                contSection='custom-section';
                setCustomSectionBackground(false);
            }else{
                //redreict to school page 
                ChangeUrl(SITE_URL+'/school',true);
            }
            
            //empty custom section
            $('#'+contSection).html('');
            //if content to replace center ara
            if(result.cnt!='') $('#'+contSection).html(result.cnt);
            //call again to make sure new content returend intialize
            init();
            //show school section
            scrollViewTo('#'+contSection);
                
            //set remember me cookie if remember me set
            if(typeof result.rememberMe!='undefined' && result.rememberMe!='')
                setRememberMeCookie(result.rememberMe);
                
            //show welcome message              
            showNotificationAlert(result.text,'success'); 
            
        }else{
            ajaxErrAlert(result);
            //show resend verificaiton button if email not verified yet
            if($('#fmSignIn #resendv_btn').length>0)
                $('.resendv-devs').remove();
            if(typeof result.resendv!='undefined' && $.trim(result.resendv)!='')
                $('#sign-in').parent().after(result.resendv);
        }
        
        setLoadingMarkup($('body'),'remove','topDiv', 'small', '','100%', 'auto',true);
    
    });
}

function signOut(){
    setLoadingMarkup($('body'),'add','topDiv', 'small', '','100%', 'auto',true);
    $.post(SITE_URL+"/?c=user_aj", {
        action: 'signout',
        csrf:getCsrf()
    },
    function(a) {
        var result = JSON.parse(a);
        
        if(result.status){
            deleteRememberMeCookie();            
            //ok we are good ,if need something can do here
           
            //update admin variable
            admin=false;
            //after sign out we change url to home and but do not relaod
            ChangeUrl('',false);  
            
            //show notfication
            showNotificationAlert(result.text,'success'); 
            //set/update nav panel
            setNavPanel(result.snav);
            setContactUsPanel(result);
            //empty custom section if have anything 
            $('#custom-section').html('');
            //empty school section if have anything 
            $('#school-section').html('');
            //call again to make sure new content returend intialize
            init();
        }else{
            ajaxErrAlert(result);
        }
        setLoadingMarkup($('body'),'remove','topDiv', 'small', '','100%', 'auto',true);    
    });
}


/*
 * load main content of the page 
 * options as json object
 * ex : {option1:value1,option1:value1, ...}
 * 
 */
function loadMainContent(page,options){
    var post,ajFile;
    switch(page){
        case 'profile':
            post={
                action: 'load',
                csrf:getCsrf()
            };
            ajFile='profile_aj';
            break;
        case 'adminSettings':
            post={
                action: 'load',
                csrf:getCsrf()
            };
            ajFile='admin_aj';
            break;
        case 'home':
            post={
                action: 'homepage',
                csrf:getCsrf()
            };
            ajFile='user_aj';
            break;
        case 'signUpPage':
            post={
                action: 'signUpPage',
                csrf:getCsrf()
            };
            ajFile='user_aj';
            break;
        case 'signInPage':
            post={
                action: 'signInPage',
                csrf:getCsrf()
            };
            ajFile='user_aj';
            break;
        default:
            return;
    }
    
    //if options passed we send them also with the request
    if(typeof options!='undefined' && typeof options =='object'){
        $.extend(post, options);
    }    
    //    console.log(post);
    setLoadingMarkup($('#centent'),'add','topDiv', 'small', '','100%', '400px');
    
    $.post(SITE_URL+"/?c="+ajFile,post ,
        function(a) {
            var result = JSON.parse(a);     
        
            if(result.status){            
                //ok we are good ,if need something can do here
                processLoadMainContent(page,post,result,options);            
            }else{
                ajaxErrAlert(result);
            }
            
            setLoadingMarkup($('#centent'),'remove','topDiv', 'small', '','100%', '400px');
        });
} 

function processLoadMainContent(page,post,result,options){
  
                    
    if(result.text!='') {
        showNotificationAlert(result.text,'success'); 
    }
    //if content to replace center ara
    if(result.cnt!='') {
        var toElem='#custom-section';
        //if we have set elment to attched content via options
        if(typeof options!='undefined' && typeof options.toElem!='undefined')
            toElem=options.toElem;
                    
        $(toElem).html(result.cnt);        
      
        
        switch(page){ 
            case 'profile':
                //set url to home
                ChangeUrl('',false); 
                setCustomSectionBackground(false);
                break;
            case 'adminSettings':
                //set url to home
                ChangeUrl('',false); 
                break;
            case 'home':
                current_page='home';
                //set url to home
                ChangeUrl('',false); 
                break;
            case 'signUpPage':
                ChangeUrl('',false);        
                setCustomSectionBackground(true);
                break;
            case 'signInPage':
                //set url to home
                ChangeUrl('',false);  
                setCustomSectionBackground(true);
                setElementVerticalCenter('#custom-section > .container-fluid > .row',50,200);
                break;            
            default:
                //set url to home
                ChangeUrl('',false); 
        }
    }
    
    //scrol to top
    scrollToViewTop();
}

function ajaxErrAlert(result){   
    if(typeof result.error!='undefined' && result.error!=''){
        //we just show error message if not empty 
        showNotificationAlert(result.error); 
    }
    
    if(typeof result.flogout!='undefined' && result.flogout==true){
        //session time out or user forced to loged out ,call logout activities
        ChangeUrl('',false); 
    }
    
}

function errorLogout(result){
    var cookieD=$.cookie(COOKIE_NAME);
    
    if(isUrlHasPath()){
        //we just call page reload so it will load site/username data
        reloadUrl();
    }else if(typeof cookieD!='undefined'){
        //we reload here so if remember login is there auto log in happens
        reloadUrl(); 
    }else{
        var needInt=false;
        if(typeof result.snav !='undefined' && result.snav!='') {
            //set/update nav panel
            setNavPanel(result.snav);
            setContactUsPanel(result);
            needInt=true;
        }
        //if content to replace center ara
        if(typeof result.cnt !='undefined' && result.cnt!='') {
            $('#centent').html(result.cnt);
            needInt=true;
        }
        //call again to make sure new content returend intialize
        if(needInt) {
            init();
        }
    }
}

function resendv(id,mode){
    setLoadingMarkup($('body'),'add','topDiv', 'small', '','100%', 'auto',true);
    $.post(SITE_URL+"/?c=user_aj", {
        action: 'resendv',
        id:id,
        csrf:getCsrf()
    },
    function(a) {
        var result = JSON.parse(a);
        if(result.status){            
            //ok we are good ,if need something can do here            
            if(mode=='signin'){
                //we remove send verfication button from sign in drop down
                if($('#fmSignIn #resendv_btn').length>0)
                    $('.resendv-devs').remove();
                //                //hide sign in drop down
                //                $('#dropdown-signin').dropdown('toggle');
                //clear up login_email_or_username id and login_password and rememebr me
                $('#fmSignIn #login_email_or_username').val('');
                $('#fmSignIn #login_password').val('');
                $('#fmSignIn #remember-me').prop('checked',false);            
            }
            //show notification
            showNotificationAlert(result.text,'success',false,3000,true,true); 
        }else{
            ajaxErrAlert(result);
        }
    
        setLoadingMarkup($('body'),'remove','topDiv', 'small', '','100%', 'auto',true);
    });
}

/*
 * this function will set given value to elemnt id passed
 * This is done to set drop down value to hidden input and sent via ajax to use for save
 */
function setvalue(elId,value,namevId,nameVal){
    $('#'+elId).val(value);
    //if drop down we set name as selected value
    if($('#'+namevId).length>0)
        $('#'+namevId).html(nameVal);
}

/*
 *return browser timezone offset
 */
function getTzOffset(){
    //get time zone offset
    var dt = new Date();
    var tz_offset=-(dt.getTimezoneOffset());//in minutes , ex: 330 sri lanka
    return tz_offset;
}

/**
 * return month array
 * start from 1
 */
function getMonthArray(){
    var mNameArr = new Array();
    mNameArr[1] = "January";
    mNameArr[2] = "February";
    mNameArr[3] = "March";
    mNameArr[4] = "April";
    mNameArr[5] = "May";
    mNameArr[6] = "June";
    mNameArr[7] = "July";
    mNameArr[8] = "August";
    mNameArr[9] = "September";
    mNameArr[10] = "October";
    mNameArr[11] = "November";
    mNameArr[12] = "December";
    
    return mNameArr;
}

function getMonthArrayShot(){
    var mNameArr = new Array();
    mNameArr[1] = "Jan";
    mNameArr[2] = "Feb";
    mNameArr[3] = "Mar";
    mNameArr[4] = "Apr";
    mNameArr[5] = "May";
    mNameArr[6] = "Jun";
    mNameArr[7] = "Jul";
    mNameArr[8] = "Aug";
    mNameArr[9] = "Sep";
    mNameArr[10] = "Oct";
    mNameArr[11] = "Nov";
    mNameArr[12] = "Dec";
    
    return mNameArr;
}
/**
 * return week array 
 * start from 0 as sunday
 */
function getWeekArray(){
    var weekday = new Array();
    weekday[0]=  "Sunday";
    weekday[1] = "Monday";
    weekday[2] = "Tuesday";
    weekday[3] = "Wednesday";
    weekday[4] = "Thursday";
    weekday[5] = "Friday";
    weekday[6] = "Saturday";
    
    return weekday;
}


/*
 *strtime ='' or has be format 22:18:26
 */
function getDateString(format,strdate,appendTime,strtime) {
    /* 
     * strdate : if not empty this has to date string  format accepted by javascript date Date Object
     * format : we return date string according to this format
     *          format='utc'|'locale'|'mysql'(format for mysql db)|'default'(like dd/mm/yyyy hh:mm:ss)
     */
    var datestr='',month='',monthName='',day='',year='',hours='',minutes='',seconds='',dayIndex='';
    var fdate ,curDtTime,curTime,curTimeTzp,mNameArr,wNameArr;
    
    
    if(typeof format=='undefined') format='default';
    if(typeof strdate=='undefined') strdate='';
    if(typeof appendTime=='undefined') appendTime=false;
    if(typeof strtime=='undefined') strtime='';
    
    curDtTime=new Date();
    curTime=curDtTime.toTimeString();
    
    if(strdate!='' && appendTime){
        if(strtime!=''){
            //if time provided and if strdate has time we need remove it and append strtime
            strdate=strdate.replace(/\s\d\d:\d\d:\d\d$/,'');
            //get only local time zone reference potion            
            curTimeTzp=$.trim(curTime.replace(/^\d\d:\d\d:\d\d/,''));
            strdate=strdate+' '+strtime+' '+curTimeTzp;
        }else
            strdate=strdate+' '+curTime;
    }
    
    if(strdate!='') fdate= new Date(strdate);
    else fdate= new Date();
    
    switch(format){
        case 'utc':
            datestr=fdate.toUTCString();
            break;
        case 'day':
            wNameArr = getWeekArray();
            dayIndex=fdate.getDay()
            datestr=wNameArr[dayIndex];            
            break;
        case 'date':
            datestr=fdate.getDate();
            break;
        case 'locale':
            datestr=fdate.toLocaleString();
            break;
        case 'mysql':
            //this is going to be UTC value with format matched to mysql
            month=fdate.getUTCMonth()+1;
            day=fdate.getUTCDate();
            year=fdate.getUTCFullYear();
            hours=fdate.getUTCHours();
            minutes=fdate.getUTCMinutes();
            seconds=fdate.getUTCSeconds();
            if(day<10) day='0'+day;       
            if(month<10) month='0'+month;
            if(hours<10) hours='0'+hours;                        
            if(minutes<10)minutes='0'+minutes;
            if(seconds<10)seconds='0'+seconds; 
            
            datestr=year+'-'+month+'-'+day+' '+hours+':'+minutes+':'+seconds;
            break;
        case 'DD-Mon-YYYY':
            // day(number) month(string), year(4 digts), ex: 08 November 2013
            mNameArr = getMonthArrayShot();
            month=fdate.getMonth()+1;
            monthName=mNameArr[month];
            day=fdate.getDate();
            year=fdate.getFullYear();
            if(day<10) day='0'+day;       
            
            datestr=day+'-'+monthName+'-'+year;
            break;
        case 'Month DD, YYYY':
            // day(number) month(string), year(4 digts), ex: 08 November 2013
            mNameArr = getMonthArray();
            month=fdate.getMonth()+1;
            monthName=mNameArr[month];
            day=fdate.getDate();
            year=fdate.getFullYear();
            if(day<10) day='0'+day;       
            
            datestr=monthName+' '+day+', '+year;
            break;
        case 'DD Month YYYY':
            // day(number) month(string), year(4 digts), ex: 08 November 2013
            mNameArr = getMonthArray();
            month=fdate.getMonth()+1;
            monthName=mNameArr[month];
            day=fdate.getDate();
            year=fdate.getFullYear();
            if(day<10) day='0'+day;       
            if(month<10) month='0'+month;
            
            datestr=day+' '+monthName+' '+year;
            break;
        case 'MM/DD/YYYY':
            // day(number) month(number), year(4 digts), ex: 08/05/2013
            month=fdate.getMonth()+1;
            day=fdate.getDate();
            year=fdate.getFullYear();
            if(day<10) day='0'+day;       
            if(month<10) month='0'+month;

            datestr=month+'/'+day+'/'+year;
            break;
        case 'MM/DD/YYYY HH:mm:ss':
            // day(number) month(number), year(4 digts), ex: 08/05/2013 10:12:13
            month=fdate.getMonth()+1;
            day=fdate.getDate();
            year=fdate.getFullYear();
            hours=fdate.getHours();
            minutes=fdate.getMinutes();
            seconds=fdate.getSeconds();
            
            if(day<10) day='0'+day;       
            if(month<10) month='0'+month;
            if(hours<10) hours='0'+hours;                        
            if(minutes<10)minutes='0'+minutes;
            if(seconds<10)seconds='0'+seconds; 
            
            datestr=month+'/'+day+'/'+year+' '+hours+':'+minutes+':'+seconds;
            break;
        default:
            month=fdate.getMonth()+1;
            day=fdate.getDate();
            year=fdate.getFullYear();
            hours=fdate.getHours();
            minutes=fdate.getMinutes();
            seconds=fdate.getSeconds();
            if(day<10) day='0'+day;       
            if(month<10) month='0'+month;
            if(hours<10) hours='0'+hours;                        
            if(minutes<10)minutes='0'+minutes;
            if(seconds<10)seconds='0'+seconds;   
            
            datestr=day+'/'+month+'/'+year+' '+hours+':'+minutes+':'+seconds;
    
    }
    
    return datestr;
}  

/*
 * mode =view|reqestemail|setpass
 */
function resetPassword(mode){
    
    switch(mode){
        case 'view':
            $.fancybox({
                autoSize:true,
                autoResize:true,
                autoCenter:true,
                fitToView:true,
                nextClick :false,
                scrolling:'auto',
                href:"#resetPass",
                closeClick	: false,
                openEffect	: 'none',
                closeEffect	: 'none',
                arrows :false,
                beforeShow:function() {  
                    //make sure rpass_username_email empty
                    $('#rpass_username_email').val('');
                },
                padding: [0,15,15,15],
                title:fancyBoxCustomTitile(langText('forgot_password')),
                helpers : {
                    title: {
                        type: 'inside',
                        position: 'top'
                    }
                }
            });           
            
            
            break;
        case 'reqestemail':
            
            if($.trim($('#rpass_username_email').val())==''){
                var usernameEmailError=langText('error_email_empty');
                if(USERNAME=="1")
                    usernameEmailError=langText('error_username_or_email_empty');
                showNotificationAlert(usernameEmailError); 
            }else{
                setLoadingMarkup($('body'),'add','topDiv', 'small', '','100%', 'auto',true);
                $.post(SITE_URL+"/?c=user_aj", {
                    action: 'resetplink',
                    username_or_email:$('#rpass_username_email').val(),
                    csrf:getCsrf()
                },
                function(a) {
                    var result = JSON.parse(a);
                    if(result.status){            
                        //ok we are good ,if need something can do here
                        $.fancybox.close(true);
                        //show notification
                        showNotificationAlert(result.text,'success'); 
                    }else{
                        ajaxErrAlert(result);
                    }
                    setLoadingMarkup($('body'),'remove','topDiv', 'small', '','100%', 'auto',true);
                });
            }
            
            break;
        case 'setpass':
            setLoadingMarkup($('body'),'add','topDiv', 'small', '','100%', 'auto',true);
            $.post(SITE_URL+"/?c=user_aj", {
                action: 'changepass',
                rpassword:$('#rnpassword').val(),
                rcpassword:$('#rcpassword').val(),
                csrf:getCsrf()
            },
            function(a) {
                var result = JSON.parse(a);
                if(result.status){            
                    //ok we are good ,if need something can do here
                    showNotificationAlert(result.text,'success');
                    //close popup
                    $.fancybox.close(true);
                }
                else{
                    ajaxErrAlert(result);
                }
                setLoadingMarkup($('body'),'remove','topDiv', 'small', '','100%', 'auto',true);
            });
            break;
    
    }
}

function fancybox_destroy() {
    $(document).unbind('click.fb-start');
}

/*
 * check ele has scrol bar
 * type = horizontal|vertical|both
 */
function hasScrollBar(type,ele){
    var $ele=$('#'+ele).get(0);
    var hasVerticalScrollbar= $ele.scrollHeight>$ele.clientHeight;
    var hasHorizontalScrollbar= $ele.scrollWidth>$ele.clientWidth;
    switch(type){
        case 'horizontal':
            return hasHorizontalScrollbar;            
            break;
        case 'vertical ':
            return hasVerticalScrollbar;  
            break;
        case 'both':
            return (hasVerticalScrollbar && hasHorizontalScrollbar);  
            break;
    }

}

function selectText(ele){
    ele.select();
}

function shareFb(shreUrl){
    var url='https://www.facebook.com/sharer/sharer.php?u='+shreUrl;
    window.open(url, 'Share facebook', "height=200,width=550,left=100,top=100");
}

/*
 *change url to given url 
 *if url empty load home
 *if reload false just change browser url and not load page actually
 */
function ChangeUrl(url,reload,title){
    var newURL;
    if(typeof title =='undefined')
        title=SITE_NAME;
    
    if(url==''){
        //we dont have url set
        if(reload===true){
            //if reload url after set
            loadHome();        
        }else{
            //if not reload url after set if url not same as new url
            //load home
            newURL=SITE_URL;
            if(hasPushState() && window.location.href.toLowerCase()!=newURL.toLowerCase())
                window.history.pushState("{}", title, newURL);
        }
    }else{
        //we have url set
        if(reload===true){
            //if reload url after set
            newURL=url;
            window.location.replace(newURL);
        }else{
            //if not reload url after set
            newURL=url;
            if(hasPushState() && window.location.href.toLowerCase()!=newURL.toLowerCase())
                window.history.pushState("{}", SITE_NAME, newURL);
        }
    }

}

/*
 * return true of browser has push state
 */
function hasPushState(){    
    if ( typeof window.history=='object' && typeof  window.history.pushState=='function' )
        return true;
    return false;
}

/*
 * reaload current url or page
 */
function reloadUrl(){
    window.location.reload();
}

/*
 * user hostname url with no paths parameters appened to it
 */
function loadHome(){
    var newURL=SITE_URL;
    window.location.replace(newURL);
}

/*
 * get hosted name, in case hosted in subfolder in url using SITE_URL
 * ex: if we have urls like http://example.com/abc/cd example.com is host name
 */
function getHostName(){
    var hostName=location.hostname;
    //make sure host name is lower case
    hostName=hostName.toLowerCase()
    
    return hostName;
}

/*
 * get hosted parth url, in case hosted in subfolder in url using SITE_URL
 * ex: if we have urls like http://example.com/abc/cd abc/cd is hosted path
 */
function getHostedPath(){
    //get hosted parth url, in case hosted in subfolder in url using SITE_URL
    var hostedPath = SITE_URL;
    var hostName=getHostName();
    //make sure hosted path is lower case
    hostedPath=hostedPath.toLowerCase();
    hostedPath=hostedPath.replace('http://','');
    hostedPath=hostedPath.replace('https://','');
    hostedPath=hostedPath.replace('://','');
    hostedPath=hostedPath.replace(hostName,'');        
        
    return hostedPath;
}

/**
 * return url path
 * if we has url like site/username, site/signup
 * we return username, signup etc.
 * if no url part return empty string
 */
function getUrlPath(){
    var hostedPath=getHostedPath();
    var pathname = $.trim(window.location.pathname);
    //make sure pathname is lowercase
    pathname=pathname.toLowerCase();
    //replace hosted path to get real path in case hosted in subfolder
    pathname=pathname.replace(hostedPath,'');
    //remove front and end slahses and any get parameters 
    pathname=pathname.replace(/\?.*$/,"").replace(/^\\/,"/").replace(/\/$/,"");
    return pathname;
}

/*
 * check if url loaded has part name in it
 * done do check if site/username exisit
 */
function isUrlHasPath() {
    var pathname=getUrlPath();
    //    console.log(pathname);
    if(pathname!='')
        return true;
    else return false;
}

/*
 * scroll so user can view top of page. 
 * ie:nav bar at top and top of contet
 */

function scrollToViewTop(){
//    var navBarH=75;
//    if($('.navbar').height()>0)
//        navBarH=$('.navbar').height()
//    else if($('.navbar .navbar-container-one').length>0)
//        navBarH=115;
//    
//    $('body, html').animate({
//        scrollTop : $('#centent').offset().top-navBarH
//    }, 750, 'easeOutExpo');
}

//cehck if browser is IE
function isIE() {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");
    
    if (msie > 0)      // If Internet Explorer, return version number
        return true;
    else                 // If another browser, return 0
        return false;

}

/**
 * show contact support form in view mode
 * submit email to admin and confirmation to user and inset request to db
 * 
 * mode: view|submit
 * type: contact|advertise
 */
function contactSupport(mode,type){
    if(typeof type=='undefined' )
        type='contact';
        
    var title=langText('contact_us');    
    switch(type){
        case 'contact':
            title=langText('contact_us'); 
            break;
        case 'advertise':
            title=langText('advertise_with_us'); 
            break;
    }
    
    switch(mode){  
        case 'view':
            //popup support form
            //hide sign in drop down
            
            $.fancybox({
                autoSize:true,
                autoResize:true,
                autoCenter:true,
                minWidth:'35%',
                fitToView:true,
                nextClick :false,
                scrolling:'auto',
                href:"#supportPopup",
                closeClick	: false,
                openEffect	: 'none',
                closeEffect	: 'none',
                arrows :false,
                padding: [0,15,15,15],
                title:fancyBoxCustomTitile(title),
                helpers : {
                    title: {
                        type: 'inside',
                        position: 'top'
                    }
                },
                beforeShow:function() {  
                    //                    $('#supportPopupEmail').val('');
                    //                    $('#supportPopupName').val('');
                    $('#supportPopupDes').val('');
                    $('#supportPopupType').val(type);                    
                }
            });    
            break;
        case 'submit':
            if($.trim($('#supportPopupName').val())=='')
                showNotificationAlert(langText('error_name_empty'));             
            else  if($.trim($('#supportPopupEmail').val())=='')
                showNotificationAlert(langText('error_email_empty'));
            else if($.trim($('#supportPopupDes').val())=='')
                showNotificationAlert(langText('error_description_empty')); 
            else{   
                setLoadingMarkup($('body'),'add','topDiv', 'small', '','100%', 'auto',true);
                $.post(SITE_URL+"/?c=user_aj", {
                    action: 'support',
                    email:$('#supportPopupEmail').val(),
                    name:$('#supportPopupName').val(),
                    type:$('#supportPopupType').val(),
                    description:$('#supportPopupDes').val(),
                    csrf:getCsrf()
                },
                function(a) {
                    var result = JSON.parse(a);
                    if(result.status){            
                        //ok we are good ,if need something can do here
                        //                        $('#supportPopupEmail').val('');
                        //                        $('#supportPopupName').val('');
                        $('#supportPopupDes').val('');
                        $('#supportPopupType').val('')
                        
                        showNotificationAlert(result.text,'success');
                        $.fancybox.close(true);
                    }
                    else{
                        ajaxErrAlert(result);
                    }
                    setLoadingMarkup($('body'),'remove','topDiv', 'small', '','100%', 'auto',true);
                });
            }
            break;
    }
}

function setLoadingMarkup(elem,action,type, version, id , width, height,coverParent, image) {
    if(action=='remove'){
        if(!$('#loading-wrapper').hasClass('hide'))
            $('#loading-wrapper').addClass('hide');
    }else{
        if($('#loading-wrapper').hasClass('hide')){
            $('#loading-wrapper').removeClass('hide');
            //lets make sure loading icon at middle
            setElementVerticalCenter('#loading-wrapper img',75,24);            
        }         
         
    }
   
}

/*
 * set Element Screen Vertical Center
 * elementSelector string jquery selector 
 * defaultOffset int
 * elementDefaultH int
 */
function setElementVerticalCenter(elementSelector,defaultOffset,elementDefaultH){    
    var marginTop=verticalCenterOffset(elementSelector,defaultOffset,elementDefaultH);
    $(elementSelector).css('margin-top',marginTop+'px');
}

/*
 * get vertical offset to center an element
 * 
 * elementSelector string jquery selector 
 * defaultOffset int
 * elementDefaultH int
 */
function verticalCenterOffset(elementSelector,defaultOffset,elementDefaultH){
    var offset=50;
    var elementH=50;
    if(typeof elementDefaultH!='undefined' && $.isNumeric(elementDefaultH))
        elementH=parseInt(elementDefaultH);

    if(typeof defaultOffset!='undefined' && $.isNumeric(defaultOffset))
        offset=parseInt(defaultOffset);
    
    if(typeof elementSelector!='undefined' && typeof $(elementSelector)!='undefined' && $(elementSelector).length>0){
        var  outerHeight=$(elementSelector).outerHeight();
        if(parseInt(outerHeight)>0)
            elementH=outerHeight;
    }        
    
    
    var centerFromTop=windowCenterFromTop();
    //if element is longer than screen we don't center is just used default padding to do work
    if(centerFromTop!=0 && (centerFromTop*2)>elementH)
        offset=centerFromTop-parseInt(elementH/2);
            
    return offset;
}

/*
 * return center if window from top in px
 */
function windowCenterFromTop(){
    var centerFromTop=0;
    if(typeof window.innerHeight!='undefined' && window.innerHeight>0)
        centerFromTop=(window.innerHeight/2);
            
    return centerFromTop;
}

/*
 * set markup to show loading for given element
 * ie:loading image 
 * 
 * elem : jquery element object to set loading image. ex: $('#elemn1').ex: $('.elemn1')]
 * action:add|remove
 * position:true|false , if true add/remove depending on action position: relative; to css of element 
 * version: thumbnail|icon|small|large|medium  
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
 * coverParent : if set true (default) when load loading box cover us parent element of given elem.
 *               else conver us elem only
 * 
 * 
 * setLoadingMarkup('centerDev', 'tiny', '','100%', '16px') 
 * 
 * Note: this is copy of same name fucntion in ui.php so when update this please update php also.
 *       make sure places use new loading has one child element.
 *       make sure places show loading element is imediate children of a column
 */

function setLoadingMarkup2(elem,action,type, version, id , width, height,coverParent, image) {
    //stop this for now
    //    return false;
    
    
    if(typeof id=='undefined')
        id = '';
    if(typeof width=='undefined')
        width = 'auto';
    if(typeof height=='undefined')
        height = 'auto';
    if(typeof image=='undefined')
        image = '';
    if(typeof coverParent=='undefined')
        coverParent = true;

    if(action=='remove'){
        //if loading image added already remove it
        var rmLoadElm=elem.children('.ajax-loading');
        if(rmLoadElm.length>0){
            //remove loading element
            rmLoadElm.remove();
        }
    }else{   
        //add/show loading image

        image=getLoadingImage(version,image);

        var markup = '';
        var style='';
        var devStyle='';
       
        
        switch (type) {
            case 'imageTag':
                //just loading image link
                style = 'width:' + width + ';height:' + height + ';max-width:100%;max-height:100%;';
                markup = '<img id="' + id + '" class="ajax-loading" src="'+APP_IMG_URL+'/' + image + '" style="' + style + '">';
                break;
            case 'centerDev':
                //image in a dev with auto size image horizontal centred and if height is number vertically too

                style = 'width:auto;height:auto;max-width:100%;max-height:100%';
                //for this set dev with 100%
                devStyle = 'text-align: center; width:' + width + ';height:' + height + ';';
                devStyle+='line-height: ' + height + ';';

                markup = '<div id="' + id + '" class="ajax-loading"  style="' + devStyle + '"><img class="" src="'+APP_IMG_URL+'/' + image + '" style="' + style + '"></div>';

                break;
            case 'topDiv':
                var elemChildren=elem.children();
                //make sure elem child left:0;top:0; first since it affect to height
                //note this case make sure elemenent has one one child                
                elemChildren.css('left',0);
                elemChildren.css('top',0);
                
                var elemH=elem.height(); 
                var elemParent=elem.parent();
                //get child element hieght
                var elemChdH=elemChildren.height();    
                //get element parent height 
                var elemPaH=elemParent.height();   
                //set dev minheight         
                var loadDevMinH=elemPaH+'px';
                
                
                //This will show loading image top of current elements there.
                style = 'width:auto;height:auto;max-width:90%;max-height:100%;';
                style+='opacity: 1 ! important;';
                //style+='background-color: white;';
                //style+='filter: alpha(opacity=100);';
                
                //for this set dev with 100%
                devStyle = 'left:0;top:0;z-index: 9999;position: absolute;';
                devStyle+='background-color: #ecf0f1;';
                devStyle+='opacity: 0.5;';
                // devStyle+='filter: alpha(opacity=20);';
                devStyle+= 'text-align: center; width:' + width + ';height:' + height + ';';
                //align image to vertical middle
                //devStyle+='line-height: ' + height + ';';
                
                if(!coverParent){
                    //we only cover same element area
                    loadDevMinH=elemH+'px';
                    devStyle+='max-height:' + loadDevMinH + ';';
                }
                //add this last so it get more prority
                devStyle+='min-height:' + loadDevMinH + ';';

                markup = '<div id="' + id + '" class="ajax-loading" style="' + devStyle + '">';
                markup += '<img class="" src="'+APP_IMG_URL+'/' + image + '" style="' + style + '">';
                markup += '</div>';
                //make sure element position: relative; form element css
                //elem.css( 'position', 'relative' );//no need since all columns are relative
                
                
                //elemChildren.css('position', 'absolute');
                
                elem.append(markup);
                var editLoadElm=elem.find('.ajax-loading');
                if(editLoadElm.length>0){
                    editLoadElm.css('line-height',parseInt(editLoadElm.height()/2)+'px');                    
                }
                
                //   console.log(elemChildren);
                break;
            case 'backDiv':
                //div has loading image as background image
                //later add this
                break;
        }
    }

//return markup;
}

//return loading image for getLoadingMarkup function 
function getLoadingImage(version,image){
    if (image == '') {
        switch (version) {
            case 'thumbnail':
                image = 'loading-124x124.gif';
                break;
            case 'small':
                image = 'loading-124x124.gif';
                break;
            case 'medium':
                image = 'loading-124x124.gif';
                break;
            case 'large':
                image = 'loading-124x124.gif';
                break;
            case 'icon':
                image = 'loading-124x124.gif';
                break;
            case 'tiny':
                image = 'loading-24x24.gif';
                break;
        }
    }
    return image;
}


function setRememberMeCookie(rememberMe){    
    var expire=parseInt(parseInt(COOKIE_TIME)/(3600*24));
    $.cookie(COOKIE_NAME, rememberMe.encodedStr, {
        domain:COOKIE_DOMAIN,
        expires: expire, 
        path: '/'
    });
}

function deleteRememberMeCookie(){ 
    //console.log($.cookie(COOKIE_NAME))
    var expire=parseInt(parseInt(COOKIE_TIME)/(3600*24));

    if($.cookie(COOKIE_NAME)!='undefined'){
        var st=$.removeCookie(COOKIE_NAME, {
            expires: expire,  
            path: '/'
        }); 
        //        console.log(st);
        if(!st){
            st=$.removeCookie(COOKIE_NAME, {
                domain:COOKIE_DOMAIN,
                expires: expire,  
                path: '/'
            });
        }
    //        console.log(st);
    }

}

/*
 * set csrf data
 */
function setCsrf(csrfnew){
    csrf=csrfnew;
}

/*
 * return csrf data
 */
function getCsrf(){
    return csrf; 
}

/**
 * return custom title html for fancybox
 */
function fancyBoxCustomTitile(title){
    var cTitle='<div class="fancybox-custom-title">'+title+'<div class="vertical–separator pull-right"></div></div>';
    cTitle+='';
    return cTitle;
}

/**
 * show Quick Start Instructions
 */
function showQuickStart(){
    $( "#quickStartPopup" ).dialog({
        minWidth: "60%",
        appendTo:".profile-page-container",
        width: "100%",
        maxHeight:"100%"
    });
}

/**
 * set/update  nav panel
 */
function setNavPanel(snav){    
    //set content
    $('#sidebar-wrapper').html(snav); 
    initNavOptions();
}

/**
 * set/update contact us panel
 */
function setContactUsPanel(result){ 
    if(typeof result.contactUs!='undefined' && result.contactUs!='')
        $('#supportPopup').replaceWith(result.contactUs); 
}


function initPhoneFormatTooltip(placement){
    var formats='Valid formats:<br>+1##########<br>+1 ### ### ####<br>+1-###-###-#### ';

    $('.phone-number-formats-help').tooltip({
        title:formats,
        placement:placement,
        html:true
    });
}

/*
 * return rows per page for pagination
 * 
 * uniquePrefix prfix word so we can use elements unique id when need   
 */
function getPerPage(uniquePrefix){
    if(typeof uniquePrefix=='undefined')
        uniquePrefix='';
    
    var perPage=0;
    if($('#'+uniquePrefix+'PerpageValue').length>0 && $('#'+uniquePrefix+'PerpageValue').val()!='')
        perPage=parseInt($('#'+uniquePrefix+'PerpageValue').val());    
    
    return perPage;

}


/*
 * change language for default lanuage setting and this is not for 
 * logged in user langage setting change
 */
function changeLanguage(code){
    setLoadingMarkup($('body'),'add','topDiv', 'small', '','100%', 'auto',true);
    $.post(SITE_URL+"/?c=user_aj", {
        action: 'changeLanguage',
        code:code,
        csrf:getCsrf()
    },
    function(a) {
        var result = JSON.parse(a);
        
        if(result.status){
            reloadUrl();
        }else{
            ajaxErrAlert(result);
            setLoadingMarkup($('body'),'remove','topDiv', 'small', '','100%', 'auto',true); 
        }
           
    });
}

/*
 * return language text
 */
function langText(key){    
    if(typeof langJs=="undefined")
        showNotificationAlert('Language has not been setup. Contact administrator.');
    else if(typeof langJs[key]!="undefined")
        return langJs[key];  
    return '';    
}

/*
 * set direct url to browser when content using loaded ajax 
 */
function setDirectUrlToAjaxLoaded(result){
    if(typeof result.directUrl!='undefined' && result.directUrl!='')
        ChangeUrl(result.directUrl,false,SITE_NAME);
}

/*
 * scrol to show element id
 */
function scrollToShow(elementId,offsetAdjest){
    if(typeof offsetAdjest=='undefined')
        offsetAdjest=150;
    var offset=$("#"+elementId).offset().top-offsetAdjest;
    $('html, body').animate({
        scrollTop: offset
    }, "fast");
}

