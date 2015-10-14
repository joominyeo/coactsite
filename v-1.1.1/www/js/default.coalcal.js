//jQuery(document).ready(function($){
////these function for cd-popup we don't seems using it
//    window.onload = function(){
//        $('.cd-popup').addClass('is-visible');
//    }
//
//    //open popup
//    $('.cd-popup-trigger').on('click', function(event){
//        event.preventDefault();
//        $('.cd-popup').addClass('is-visible');
//    });
//
//    //close popup
//    $('.cd-popup').on('click', function(event){
//        if( $(event.target).is('.cd-popup-close') || $(event.target).is('.cd-popup') ) {
//            event.preventDefault();
//            $(this).removeClass('is-visible');
//        }
//    });
//    //close popup when clicking the esc keyboard button
//    $(document).keyup(function(event){
//        if(event.which=='27'){
//            $('.cd-popup').removeClass('is-visible');
//        }
//    });
//    });

$(document).ready(function() { 
    initNav();
});
    
/*
* scrol to given selector to show,
* if scroll happned return false to so can use to prevent default actions and else return true
* 
* ex: elementSelector=#section-one
* 
*/
function scrollViewTo(elementSelector){
    //empty custom section if have anything in it for all other options than show custom-section
    if(elementSelector!='#custom-section')        
        $('#custom-section').html('');
    //change set url to home in browser address bar and do not relaod
    ChangeUrl('',false);
                
    displaySections(elementSelector);
            
    var target = $(elementSelector);
    target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
    if (target.length) {
        $('html,body').animate({
            scrollTop: target.offset().top
        }, 1000);
        return false;
    }
    return true;
}    

//disply section according  to elementSelector requested to display
function displaySections(elementSelector){
    if(elementSelector=='#custom-section'){
        //hide all default sections
        if(!$('.default-section').hasClass('hide'))
            $('.default-section').addClass('hide');
        //hide school section
        if(!$('#school-section').hasClass('hide'))
            $('#school-section').addClass('hide');        
        //show custom section only
        if($('#custom-section').hasClass('hide'))
            $('#custom-section').removeClass('hide'); 
    }else if(elementSelector=='#school-section'){
        //hide all default sections
        if(!$('.default-section').hasClass('hide'))
            $('.default-section').addClass('hide');
        //hide custom section 
        if(!$('#custom-section').hasClass('hide'))
            $('#custom-section').addClass('hide'); 
        //we show only school section only
        if($('#school-section').hasClass('hide'))
            $('#school-section').removeClass('hide');
    }else{
        //hide school section
        if(!$('#school-section').hasClass('hide'))
            $('#school-section').addClass('hide'); 
        //hide custom section 
        if(!$('#custom-section').hasClass('hide'))
            $('#custom-section').addClass('hide'); 
        //show all default sections
        if($('.default-section').hasClass('hide'))
            $('.default-section').removeClass('hide');
    }
}

function initNav(){
    initNavMenuToggle();
    initNavOptions();
}

//set nav toggle
function initNavMenuToggle(){
    // Opens the sidebar menu
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#sidebar-wrapper").toggleClass("active");
    });
}

// set Scrolls to the selected menu item on the page
function initNavOptions() {
    // Closes the sidebar menu
    $("#menu-close").click(function(e) {
        e.preventDefault();
        $("#sidebar-wrapper").toggleClass("active");
    });


    $('a[href*=#]:not([href=#])').click(function() {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') || location.hostname == this.hostname) {
            return scrollViewTo(this.hash);
        }
    });
}

//set cutom section a backgournd in .custom_backgroud class
function setCustomSectionBackground(add){
    if(add==true){
        if(!$('#custom-section').hasClass('custom_backgroud'))
            $('#custom-section').addClass('custom_backgroud');
    }else{
        if($('#custom-section').hasClass('custom_backgroud'))
            $('#custom-section').removeClass('custom_backgroud');
    }
}

/*
 * schoolEvents
 *
 * action : list|join|leave
 * confirmed: for leave action
 * eventId : id notifications belongs to
 * ui: unique id for record for leave action
 */
function schoolEvents(action,eventId, uid, options, confirmed) {
    
    //console.log(action);
    if (typeof options == 'undefined')
        options = {};
    
    //get per page and set per page to options
    options.perPage=getPerPage('eventNotifications');

    switch (action) {
        case 'list':
            //show loading 
            setLoadingMarkup('body', 'add', 'topDiv', 'small', '', '100%', 'auto', false);

            //get section content 
            $.post(SITE_URL+"/?c=profile_aj",
            {
                action: 'schoolEvents',
                mode: 'list',
                eventId:eventId,
                options: options,
                csrf: getCsrf()
            },
            function(a) {
                var result = JSON.parse(a);
                if (result.status) {
                    //ok sucess, show hide action buttons                    
                    updateSchoolBordContent(result,options);
                    //show notification
                    if (result.text != '')
                        showNotificationAlert(result.text, 'success');
                   
                } else {
                    ajaxErrAlert(result);
                }
                setLoadingMarkup('body', 'remove', 'topDiv', 'small', '', '100%', 'auto', false);
            });

        
            break;
        case 'join':
            //save new 
            setLoadingMarkup('body', 'add', 'topDiv', 'small', '', '100%', 'auto', false);
            
            $.post(SITE_URL+"/?c=profile_aj",
            {
                action: 'schoolEvents',
                mode: 'join',
                eventId:eventId,
                options: options,
                csrf: getCsrf()
            },
            function(a) {
                var result = JSON.parse(a);
                if (result.status) {
                    //ok sucess, show hide action buttons
                    updateSchoolBordContent(result,options)
                    //show notification
                    if (result.text != '')
                        showNotificationAlert(result.text, 'success');
                  
                    //close popup
                    $.fancybox.close(true);
                } else {
                    ajaxErrAlert(result);
                }
                setLoadingMarkup('body', 'remove', 'topDiv', 'small', '', '100%', 'auto', true);
            });

            break;
       
        case 'leave':
            //delete motivational messages
            if (!confirmed){
                var delStrOption=JSON.stringify(options);
                delStrOption=delStrOption.replace(/"/g, "'");
                showConfirm("Are you sure, you want to leave this event?", 'schoolEvents(\'' + action + '\',' + eventId + ',' + uid + ',' + delStrOption + ',true)');
            }else {
                
                setLoadingMarkup('body', 'add', 'topDiv', 'small', '', '100%', 'auto', false);
         
                $.post(SITE_URL+"/?c=profile_aj",
                {
                    action: 'schoolEvents',
                    mode: 'leave',
                    eventId:eventId,
                    uid: uid,
                    options: options,
                    csrf: getCsrf()
                },
                function(a) {
                    var result = JSON.parse(a);
                    if (result.status) {
                        //ok sucess
                        updateSchoolBordContent(result,options)
                        //show notification
                        if (result.text != '')
                            showNotificationAlert(result.text, 'success');
                        
                    } else {
                        ajaxErrAlert(result);
                    }

                    setLoadingMarkup('body', 'remove', 'topDiv', 'small', '', '100%', 'auto', true);
                });
               
            }
            break;
        case 'notificationPop':            
            //show loading 
            setLoadingMarkup('body', 'add', 'topDiv', 'small', '', '100%', 'auto', false);
            //get edit popup via ajax            
            $.post(SITE_URL+"/?c=profile_aj",
            {
                action: 'schoolEvents',
                mode: 'notificationPop',
                eventId:eventId,
                options: options,
                csrf: getCsrf()
            },
            function(a) {
                var result = JSON.parse(a);
                if (result.status) {
                    //ok sucess, show hide action buttons
                    $('#dialog-view-eventn').replaceWith(result.schEvtNotCntpop);
                    
                    //show notification
                    if (result.text != '')
                        showNotificationAlert(result.text, 'success');
                    //show edit popup
                    fancybox_destroy();
                    $.fancybox({
                        autoSize: true,
                        autoResize: true,
                        autoCenter: true,
                        fitToView: true,
                        nextClick: false,
                        minWidth: '50%',
                        scrolling: 'auto',
                        href: "#dialog-view-eventn",
                        closeClick: false,
                        openEffect: 'none',
                        closeEffect: 'none',
                        arrows: false,
                        padding: [0,15,15,15],
                        title:fancyBoxCustomTitile('Event Notification'),
                        helpers : {
                            title: {
                                type: 'inside',
                                position: 'top'
                            }
                        },
                        beforeShow: function() {
                        }
                    });
                    
                } else {
                    ajaxErrAlert(result);
                }
                setLoadingMarkup('body', 'remove', 'topDiv', 'small', '', '100%', 'auto', true);
            });
            break;            
        case 'notification':
            //show loading 
            setLoadingMarkup('body', 'add', 'topDiv', 'small', '', '100%', 'auto', false);

            //get section content 
            $.post(SITE_URL+"/?c=profile_aj",
            {
                action: 'schoolEvents',
                mode: 'notification',
                eventId:eventId,
                options: options,
                csrf: getCsrf()
            },
            function(a) {
                var result = JSON.parse(a);
                if (result.status) {
                    //ok sucess, show hide action buttons
                    $('#view_evtn_data').html(result.schEvtNotCnt);
                    //show notification
                    if (result.text != '')
                        showNotificationAlert(result.text, 'success');
                   
                } else {
                    ajaxErrAlert(result);
                }
                setLoadingMarkup('body', 'remove', 'topDiv', 'small', '', '100%', 'auto', false);
            });

            break;
    }
}

/*
 * update school bord content column
 */
function updateSchoolBordContent(result,options){
    var contentElemt='';
    if(typeof options.filter!='undefined' && typeof options.filter.type!='undefined'){
        if(options.filter.type=='internships')
            contentElemt='schBordInternships';
        else if(options.filter.type=='job shadows')
            contentElemt='schBordJobShadows';
        else if(options.filter.type=='volunteering')
            contentElemt='schBordVolunteering';
    }
    if(contentElemt!='')
        $('#'+contentElemt).html(result.schEvtCnt);
}


