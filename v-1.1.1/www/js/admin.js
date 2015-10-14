current_page = 'admin';
$(document).ready(function() {
    loadAdminSection('events');
});


//load admin section
var adminSecType = '';//hold admin section type currently loaded and used for viewMoreSearchResults func
function loadAdminSection(section, options) {
    //empty current contnt
    $('#admin_cnt').html('');
    //select new section head
    $('#ad_sec_head > li').removeClass('active');
    $('#ad_sec_head .' + section).addClass('active');

    //show loading 
    setLoadingMarkup($('#admin_cnt').parent().parent(), 'add', 'topDiv', 'small', '', '100%', 'auto', false);

    if (typeof options == 'undefined')
        options = {};
    //get section content 
    $.post(SITE_URL+"/?c=admin_aj",
    {
        action: 'loadSection',
        section: section,
        options: options,
        csrf: getCsrf()
    },
    function(a) {
        var result = JSON.parse(a);
        if (result.status) {
            //ok sucess, show hide action buttons
            $('#admin_cnt').html(result.adcnt);
            //show notification
            if (result.text != '')
                showNotificationAlert(result.text, 'success');
            //process section specific actions after load section
            processLoadedSection(section);
        } else {
            ajaxErrAlert(result);
        }
        setLoadingMarkup($('#admin_cnt').parent().parent(), 'remove', 'topDiv', 'small', '', '100%', 'auto', false);
    });

}

//process section specific actions after load section
function  processLoadedSection(section) {
    //    destroySerachResWaypoints();
    switch (section) {        
        case 'events':
            setEventPopupDatePickers('add');
            break;
            
    }
}


/*
 * get th current sort options object 
 * fields, object indexing field and type as its value, types are text|number|other
 * defultOrderBy, defult field to order
 * defultOrderDir, as|sesc
 * idPrefix, string to filter uniquu ids
 */
function getThSortOptions(fields, defultOrderBy, defultOrderDir, idPrefix) {
    //set default order by settings
    var orderByField = defultOrderBy;
    var orderDir = defultOrderDir;

    $.each(fields, function(field, type) {
        if ($('#adm_' + idPrefix + 'cnt .' + idPrefix + 'th_' + field + ' .glyphicon').length > 0) {
            // order by is set
            orderByField = field;
            var glyphiconType = '';
            switch (type) {
                case 'text':
                    glyphiconType = 'glyphicon-sort-by-alphabet';
                    break;
                case 'number':
                    glyphiconType = 'glyphicon-sort-by-order';
                    break;
                case 'other':
                default :
                    glyphiconType = 'glyphicon-sort-by-attributes';
                    break;
            }
            //get direcetion
            if ($('#adm_' + idPrefix + 'cnt .' + idPrefix + 'th_' + field + '  .glyphicon').hasClass(glyphiconType))
                orderDir = 'asc';
            else if ($('#adm_' + idPrefix + 'cnt .' + idPrefix + 'th_' + field + '  .glyphicon').hasClass(glyphiconType + '-alt'))
                orderDir = 'desc';
            //we found order by field and stop loop
            return;
        }
    });

    return {
        orderBy: orderByField,
        orderDir: orderDir
    };
}

/*
 * events
 * 
 * action : add|edit|addPop|editPop|delete|sort
 * confirmed: for delete action
 */
function events(action, uid, options, confirmed) {
    
    //console.log(action);
    if (typeof options == 'undefined')
        options = {};
    
    //get per page and set per page to options
    options.perPage=getPerPage('events');

    var fieldsList = {
        'name': 'text',
        'type': 'other',
        //        'start_date' : 'other',
        //        'end_date' : 'other',
        'apply_deadline': 'other',
        'publish': 'other',
        'last_updated': 'other'
    };

    switch (action) {
        case 'sort':
            //if sort infomation not set get current sort settings , for change per page
            if(typeof options.orderBy == 'undefined')
                options = $.extend(options,getThSortOptions(fieldsList, 'last_updated', 'desc', 'evt_'));
            loadAdminSection('events', options);
            break;
        case 'addPop':
            //show add new popup
            fancybox_destroy();
            $.fancybox({
                autoSize: true,
                autoResize: true,
                autoCenter: true,
                fitToView: true,
                nextClick: false,
                minWidth: '50%',
                scrolling: 'auto',
                href: "#dialog-add-event",
                closeClick: false,
                openEffect: 'none',
                closeEffect: 'none',
                arrows: false,
                padding: [0,15,15,15],
                title:fancyBoxCustomTitile('Add New Event'),
                helpers : {
                    title: {
                        type: 'inside',
                        position: 'top'
                    }
                },
                beforeShow: function() {
                    
                }
            });
            break;
        case 'editPop':            
            //set current order by options  
            if(typeof options.orderBy == 'undefined')
                options = $.extend(options,getThSortOptions(fieldsList, 'last_updated', 'desc', 'evt_'));
            //get edit popup via ajax            
            $.post(SITE_URL+"/?c=admin_aj",
            {
                action: 'event',
                mode: 'editPop',
                uid:uid,
                options: options,
                csrf: getCsrf()
            },
            function(a) {
                var result = JSON.parse(a);
                if (result.status) {
                    //ok sucess, show hide action buttons
                    $('#dialog-edit-event').replaceWith(result.admEEpop);
                    setEventPopupDatePickers('edit');
                    
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
                        href: "#dialog-edit-event",
                        closeClick: false,
                        openEffect: 'none',
                        closeEffect: 'none',
                        arrows: false,
                        padding: [0,15,15,15],
                        title:fancyBoxCustomTitile('Edit Event'),
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
        case 'add':
            //save new 
            setLoadingMarkup('body', 'add', 'topDiv', 'small', '', '100%', 'auto', false);

            //set current order by options  
            if(typeof options.orderBy == 'undefined')
                options = $.extend(options,getThSortOptions(fieldsList, 'last_updated', 'desc', 'evt_'));

            var addEvtPublish=0;
            if($('#add_evt_published').prop('checked')==true)
                addEvtPublish=1;

            $.post(SITE_URL+"/?c=admin_aj",
            {
                action: 'event',
                mode: 'add',
                name: $('#add_evt_name').val(),
                type: $('#add_evt_type').val(),
                start: $('#add_evt_start').val(),
                end: $('#add_evt_end').val(),
                deadline: $('#add_evt_deadline').val(),
                description: $('#add_evt_description').val(),
                publish:addEvtPublish,
                options: options,
                csrf: getCsrf()
            },
            function(a) {
                var result = JSON.parse(a);
                if (result.status) {
                    //ok sucess, show hide action buttons
                    $('#admin_cnt').html(result.adcnt);
                    //show notification
                    if (result.text != '')
                        showNotificationAlert(result.text, 'success');
                    //process section specific actions after load section
                    processLoadedSection('events');
                    //close popup
                    $.fancybox.close(true);
                } else {
                    ajaxErrAlert(result);
                }
                setLoadingMarkup('body', 'remove', 'topDiv', 'small', '', '100%', 'auto', true);
            });

            break;
        case 'edit':
            //save edited
            setLoadingMarkup('body', 'add', 'topDiv', 'small', '', '100%', 'auto', false);

            //set current order by options
            if(typeof options.orderBy == 'undefined')
                options = $.extend(options,getThSortOptions(fieldsList, 'last_updated', 'desc', 'evt_'));

            var editEvtPublish=0;
            if($('#edit_evt_published').prop('checked')==true)
                editEvtPublish=1;
            $.post(SITE_URL+"/?c=admin_aj",
            {
                action: 'event',
                mode: 'edit',
                uid: uid,
                name: $('#edit_evt_name').val(),
                type: $('#edit_evt_type').val(),
                start: $('#edit_evt_start').val(),
                end: $('#edit_evt_end').val(),
                deadline: $('#edit_evt_deadline').val(),
                description: $('#edit_evt_description').val(),
                publish:editEvtPublish,
                options: options,
                csrf: getCsrf()
            },
            function(a) {
                var result = JSON.parse(a);
                if (result.status) {
                    //ok sucess, show hide action buttons
                    $('#admin_cnt').html(result.adcnt);
                    //show notification
                    if (result.text != '')
                        showNotificationAlert(result.text, 'success');
                    //process section specific actions after load section
                    processLoadedSection('events');
                    //close popup
                    $.fancybox.close(true);
                } else {
                    ajaxErrAlert(result);
                }
                setLoadingMarkup('body', 'remove', 'topDiv', 'small', '', '100%', 'auto', true);
            });
            break;
        case 'delete':
            //delete motivational messages
            if (!confirmed){
                var delStrOption=JSON.stringify(options);
                delStrOption=delStrOption.replace(/"/g, "'");
                showConfirm("Are you sure, you want to delete this event?", 'events(\'' + action + '\',' + uid + ',' + delStrOption + ',true)');
            }else {
                
                setLoadingMarkup('body', 'add', 'topDiv', 'small', '', '100%', 'auto', false);
                
                //set current order by options
                if(typeof options.orderBy == 'undefined')
                    options = $.extend(options,getThSortOptions(fieldsList, 'last_updated', 'desc', 'evt_'));

                $.post(SITE_URL+"/?c=admin_aj",
                {
                    action: 'event',
                    mode: 'delete',
                    uid: uid,
                    options: options,
                    csrf: getCsrf()
                },
                function(a) {
                    var result = JSON.parse(a);
                    if (result.status) {
                        //ok sucess
                        $('#admin_cnt').html(result.adcnt);
                        //show notification
                        if (result.text != '')
                            showNotificationAlert(result.text, 'success');
                        //process section specific actions after load section
                        processLoadedSection('events');
                    } else {
                        ajaxErrAlert(result);
                    }

                    setLoadingMarkup('body', 'remove', 'topDiv', 'small', '', '100%', 'auto', true);
                });
                break;
            }
    }
}


/*
 * eventNotifications
 *
 * action : add|edit|addPop|editPop|delete|list
 * confirmed: for delete action
 * eventId : id notifications belongs to
 * ui: notification id
 */
function eventNotifications(action,eventId, uid, options, confirmed) {
    
    //console.log(action);
    if (typeof options == 'undefined')
        options = {};
    
    //get per page and set per page to options
    options.perPage=getPerPage('eventNotifications');

    var fieldsList = {
        'title': 'text',
        'publish': 'other',
        'last_updated': 'other'
    };

    switch (action) {
        case 'list':
            //if sort infomation not set get current sort settings , for change per page
            if(typeof options.orderBy == 'undefined')
                options = $.extend(options,getThSortOptions(fieldsList, 'last_updated', 'desc', 'evtn_'));
            
            //show loading 
            setLoadingMarkup($('#admin_cnt').parent().parent(), 'add', 'topDiv', 'small', '', '100%', 'auto', false);

            //get section content 
            $.post(SITE_URL+"/?c=admin_aj",
            {
                action: 'eventNotifications',
                mode: 'list',
                eventId:eventId,
                options: options,
                csrf: getCsrf()
            },
            function(a) {
                var result = JSON.parse(a);
                if (result.status) {
                    //ok sucess, show hide action buttons
                    $('#adm_evt_cnt').html(result.admEnCnt);
                    //show notification
                    if (result.text != '')
                        showNotificationAlert(result.text, 'success');
                   
                } else {
                    ajaxErrAlert(result);
                }
                setLoadingMarkup($('#admin_cnt').parent().parent(), 'remove', 'topDiv', 'small', '', '100%', 'auto', false);
            });

            break;
        case 'addPop':
            //show add new popup
            fancybox_destroy();
            $.fancybox({
                autoSize: true,
                autoResize: true,
                autoCenter: true,
                fitToView: true,
                nextClick: false,
                minWidth: '50%',
                scrolling: 'auto',
                href: "#dialog-add-eventn",
                closeClick: false,
                openEffect: 'none',
                closeEffect: 'none',
                arrows: false,
                padding: [0,15,15,15],
                title:fancyBoxCustomTitile('Add New Notification'),
                helpers : {
                    title: {
                        type: 'inside',
                        position: 'top'
                    }
                },
                beforeShow: function() {
               
                }
            });
            break;
        case 'editPop':
            //set current order by options  
            if(typeof options.orderBy == 'undefined')
                options = $.extend(options,getThSortOptions(fieldsList, 'last_updated', 'desc', 'evtn_'));
            //get edit popup via ajax            
            $.post(SITE_URL+"/?c=admin_aj",
            {
                action: 'eventNotifications',
                mode: 'editPop',
                eventId:eventId,
                uid:uid,
                options: options,
                csrf: getCsrf()
            },
            function(a) {
                var result = JSON.parse(a);
                if (result.status) {
                    //ok sucess, show hide action buttons
                    $('#dialog-edit-eventn').replaceWith(result.admENpop);
                    
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
                        href: "#dialog-edit-eventn",
                        closeClick: false,
                        openEffect: 'none',
                        closeEffect: 'none',
                        arrows: false,
                        padding: [0,15,15,15],
                        title:fancyBoxCustomTitile('Edit Notification'),
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
        case 'add':
            //save new 
            setLoadingMarkup('body', 'add', 'topDiv', 'small', '', '100%', 'auto', false);

            //set current order by options  
            if(typeof options.orderBy == 'undefined')
                options = $.extend(options,getThSortOptions(fieldsList, 'last_updated', 'desc', 'evtn_'));

            var addEvtnPublish=0;
            if($('#add_evtn_publish').prop('checked')==true)
                addEvtnPublish=1;
            
            $.post(SITE_URL+"/?c=admin_aj",
            {
                action: 'eventNotifications',
                mode: 'add',
                eventId:eventId,
                title: $('#add_evtn_title').val(),
                text: $('#add_evtn_text').val(),
                publish:addEvtnPublish,
                options: options,
                csrf: getCsrf()
            },
            function(a) {
                var result = JSON.parse(a);
                if (result.status) {
                    //ok sucess, show hide action buttons
                    $('#adm_evt_cnt').html(result.admEnCnt);
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
        case 'edit':
            //save edited
            setLoadingMarkup('body', 'add', 'topDiv', 'small', '', '100%', 'auto', false);

            //set current order by options
            if(typeof options.orderBy == 'undefined')
                options = $.extend(options,getThSortOptions(fieldsList, 'last_updated', 'desc', 'chalp_'));

            var editEvtnPublish=0;
            if($('#edit_evtn_publish').prop('checked')==true)
                editEvtnPublish=1;
            
            $.post(SITE_URL+"/?c=admin_aj",
            {
                action: 'eventNotifications',
                mode: 'edit',
                eventId:eventId,
                uid: uid,
                title: $('#edit_evtn_title').val(),
                text: $('#edit_evtn_text').val(),
                publish:editEvtnPublish,
                options: options,
                csrf: getCsrf()
            },
            function(a) {
                var result = JSON.parse(a);
                if (result.status) {
                    //ok sucess, show hide action buttons
                    $('#adm_evt_cnt').html(result.admEnCnt);
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
        case 'delete':
            //delete motivational messages
            if (!confirmed){
                var delStrOption=JSON.stringify(options);
                delStrOption=delStrOption.replace(/"/g, "'");
                showConfirm("Are you sure, you want to delete this event notification?", 'eventNotifications(\'' + action + '\',' + eventId + ',' + uid + ',' + delStrOption + ',true)');
            }else {
                
                setLoadingMarkup('body', 'add', 'topDiv', 'small', '', '100%', 'auto', false);
                
                //set current order by options
                if(typeof options.orderBy == 'undefined')
                    options = $.extend(options,getThSortOptions(fieldsList, 'last_updated', 'desc', 'evtn_'));

                $.post(SITE_URL+"/?c=admin_aj",
                {
                    action: 'eventNotifications',
                    mode: 'delete',
                    eventId:eventId,
                    uid: uid,
                    options: options,
                    csrf: getCsrf()
                },
                function(a) {
                    var result = JSON.parse(a);
                    if (result.status) {
                        //ok sucess
                        $('#adm_evt_cnt').html(result.admEnCnt);
                        //show notification
                        if (result.text != '')
                            showNotificationAlert(result.text, 'success');
                        
                    } else {
                        ajaxErrAlert(result);
                    }

                    setLoadingMarkup('body', 'remove', 'topDiv', 'small', '', '100%', 'auto', true);
                });
                break;
            }
    }
}

/*
 * mode : add|edit
 */
function setEventPopupDatePickers(mode){
    $('#'+mode+'_evt_start').datetimepicker({
        todayBtn:true,
        todayHighlight:true,
        minView:2,
        format:'M dd, yyyy',
        autoclose:true
    });
    $('#'+mode+'_evt_end').datetimepicker({
        todayBtn:true,
        todayHighlight:true,
        minView:2,
        format:'M dd, yyyy',
        autoclose:true
    });
    
    $('#'+mode+'_evt_deadline').datetimepicker({
        todayBtn:true,
        todayHighlight:true,
        minView:2,
        format:'M dd, yyyy',
        autoclose:true
    });
}

/*
 * eventUsers
 *
 * action : list
 * confirmed: for delete action
 * eventId : id users belongs to
 * ui: user id
 */
function eventUsers(action,eventId, uid, options, confirmed) {
    
    //console.log(action);
    if (typeof options == 'undefined')
        options = {};
    
    //get per page and set per page to options
    options.perPage=getPerPage('eventUsers');

    var fieldsList = {
        'name': 'text',
        'email': 'text',
        'username': 'text',
        'grade':'number',
        'school_name':'text',
        'joined': 'other'
    };

    switch (action) {
        case 'list':
            //if sort infomation not set get current sort settings , for change per page
            if(typeof options.orderBy == 'undefined')
                options = $.extend(options,getThSortOptions(fieldsList, 'joined', 'desc', 'evtu_'));
            
            //show loading 
            setLoadingMarkup('body', 'add', 'topDiv', 'small', '', '100%', 'auto', false);

            //get section content 
            $.post(SITE_URL+"/?c=admin_aj",
            {
                action: 'eventUsers',
                mode: 'list',
                eventId:eventId,
                options: options,
                csrf: getCsrf()
            },
            function(a) {
                var result = JSON.parse(a);
                if (result.status) {
                    //ok sucess, show hide action buttons
                    $('#adm_evt_cnt').html(result.admEuCnt);
                    //show user
                    if (result.text != '')
                        showUserAlert(result.text, 'success');
                   
                } else {
                    ajaxErrAlert(result);
                }
                setLoadingMarkup('body', 'remove', 'topDiv', 'small', '', '100%', 'auto', false);
            });

            break;
      
    }
}


