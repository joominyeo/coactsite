current_page='profile';
$( document ).ready(function() {      
    initTooTips();
});


function initTooTips(){

}

/*
 *save content in settings tab panels
 */

function saveSettings(panel){
    
    setLoadingMarkup($('#fm_'+panel).parent(),'add','topDiv', 'small', '','100%', 'auto',true);
    
    var data=$('#fm_'+panel).serialize(); 
    
    $.post(SITE_URL+"/?c=profile_aj", 
    {
        action: 'saveSettings',
        panel:panel,
        data: data  ,
        csrf:getCsrf()                 
    },
    function(a) {
        var result = JSON.parse(a);
        if(result.status){                         
            //ok we are good ,if need something can do here  
            switch(panel){
                case 'basic':
                    //repalce nav bar so we have latest name on it
                    if(result.snav!=''){
                        //set/update nav panel
                        setNavPanel(result.snav);
                        setContactUsPanel(result);
                    }
                
                    break;
                case 'username':
                    if(result.rtext!='')
                        $('#fm_username').replaceWith(result.rtext);
                    break;
                case 'email':
                    $('#epassword').val('');
                    //give message if any
                    if(result.text!='')
                        showNotificationAlert(result.text,'success');
                    if($('#mail_newnote_row').length>0) 
                        $('#mail_newnote_row').remove();            
                    if(result.rtext!='')
                        $('#mail_pass_row').after(result.rtext);
                    break;
                case 'pass':
                    //give password changed sucess message
                    if(result.text!='')
                        showNotificationAlert(result.text,'success');
                    //clear input boxes
                    $('#password').val('');
                    $('#npassword').val('');
                    $('#cpassword').val('');
                    break;
                    
            }
            
           
        }else{
            ajaxErrAlert(result);
        }
        
        setLoadingMarkup($('#fm_'+panel).parent(),'remove','topDiv', 'small', '','100%', 'auto',true);
    });
}

function resendCmailV(id){
    setLoadingMarkup($('#fm_email').parent(),'add','topDiv', 'small', '','100%', 'auto',true);
    
    $.post(SITE_URL+"/?c=profile_aj", {
        action: 'resendcmv',
        id:id,
        csrf:getCsrf()
    },
    function(a) {
        var result = JSON.parse(a);
        if(result.status){            
            //ok we are good ,if need something can do here
            if(result.text!='')
                showNotificationAlert(result.text,'success');
            if($('#mail_newnote_row').length>0) 
                $('#mail_newnote_row').remove();            
            if(result.rtext!='')
                $('#mail_pass_row').after(result.rtext);
        }else{
            ajaxErrAlert(result);
        }

        setLoadingMarkup($('#fm_email').parent(),'remove','topDiv', 'small', '','100%', 'auto',true);
    });
}

function removeCmailV(id){
    setLoadingMarkup($('#fm_email').parent(),'add','topDiv', 'small', '','100%', 'auto',true);
    
    $.post(SITE_URL+"/?c=profile_aj", {
        action: 'removecmv',
        id:id,
        csrf:getCsrf()
    },
    function(a) {
        var result = JSON.parse(a);
        if(result.status){            
            //ok we are good ,if need something can do here
            $('#nemail').val('');
            if(result.text!='')
                showNotificationAlert(result.text,'success'); 
            if($('#mail_newnote_row').length>0) 
                $('#mail_newnote_row').remove();            
            if(result.rtext!='')
                $('#mail_pass_row').after(result.rtext);
        }else{
            ajaxErrAlert(result);
        }

        setLoadingMarkup($('#fm_email').parent(),'remove','topDiv', 'small', '','100%', 'auto',true);
    });
}
