<?php

/*
 * hold footer ui functions
 */
require_once "ui.common.class.php";

class uiEvent extends uiCommon {

    public function __construct() {
        
    }

    /**
     * return school Events List ui html
     * 
     * @param array $event event data
     */
    public function schoolEventsList($eventsRows, $page, $pageCount, $perPage, $onclickS, $onclickE, $uniquePrefix = '', $options = array()) {

        //start final markup
        $finalMarkup = '';
        $finalMarkup .= '<div class="row"><div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="sch_evtn_cnt" style="margin-top:10px;">';
        //row to hold eventNotifications messages table 
        $finalMarkup .= '   <div class="row"><div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">';
        //motivational eventNotifications table        
        $finalMarkup .= '       <table class="table table-striped event-list-table" style="background-color: white;">
                                    <tbody>';
        $finalMarkup .= '               ' . $eventsRows;
        $finalMarkup .= '           </tbody>
                                </table>';
        $finalMarkup .= '   </div></div>';
        //row to hold eventNotifications table end
        //for pagination row - start
        $finalMarkup .='               <div class="row">';
        $finalMarkup .='                   <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">';
        //set pagination ui
        $finalMarkup .= paginationUi($page, $pageCount, $perPage, $onclickS, $onclickE, $uniquePrefix, false, $options, 'text-center');
        $finalMarkup .='                   </div>';
        $finalMarkup .='               </div>';
        //for pagination row - end
        $finalMarkup .= '</div></div>';

        return $finalMarkup;
    }

    /**
     * schoolEventsRow
     * 
     * @param array $event
     * @param string $lastUpdatedLocal
     * @param string $join
     * @param string $leave
     * @return string
     */
    public function schoolEventsRow($event, $lastUpdatedLocal, $join, $leave, $notifBtn) {

        $eventInfor = $this->eventInfor($event, $lastUpdatedLocal, $join, $leave, $notifBtn);

        $row = '<tr id="tr_schevt_' . $event['id'] . '">
                    <td>
                        <dl style="margin-bottom:0px;">
                            ' . $eventInfor . '
                        </dl>
                    </td>
                </tr>';
        return $row;
    }

    /**
     * return event infor formmated to display
     * 
     * @param type $event
     * @param type $lastUpdatedLocal
     * @param type $join
     * @param type $leave
     * @param type $notifBtn
     * @return string
     */
    private function eventInfor($event, $lastUpdatedLocal, $join = '', $leave = '', $notifBtn = '') {
        $name = $event['name'];
        $startDay = formatDate($event['start_date'], 'M d, Y');
        $endDay = formatDate($event['end_date'], 'M d, Y');
        $Deadline = formatDate($event['apply_deadline'], 'M d, Y');
        $description = $event['description'];
        $type = ucwords($event['type']);

        $actions = '';
        if ($join != '' || $leave != '' || $notifBtn != '')
            $actions = '<span class="pull-right">' . $join . $notifBtn . $leave . '</span>';

        $eventInfor = ' <dt>
                            ' . $actions . '
                            <h4>' . $this->et($name) . '</h4>
                        </dt>
                        <dd><p>' . nl2br($this->et($description)) . '</p></dd>
                        <dd>
                            <span>Start: <mark>' . $startDay . '</mark></span>
                            <span>End: <mark>' . $endDay . '</mark></span>
                            <span>Deadline: <mark>' . $Deadline . '</mark></span>
                        </dd>
                        <dd>' . $type . ', <small><em>' . $lastUpdatedLocal . '</em></small></dd>';
        return $eventInfor;
    }

    /**
     * return event join Btn
     * 
     * @param int $eventId event id
     * @param int $page 
     */
    public function schoolEventJoinBtn($eventId, $optionsStr = '{}') {
        $addNew = $this->htmlButton('Join', 'schoolEvents(\'join\',' . $eventId . ',0,' . $optionsStr . ');', 'btn btn-primary  btn-xs pull-right');
        return $addNew;
    }

    /**
     * return event leave Btn
     * 
     * @param int $eventId event id
     * @param int $userEventId user event id
     * @param int $page 
     */
    public function schoolEventLeaveBtn($eventId, $userEventId = 0, $optionsStr = '{}') {
        $addNew = $this->htmlButton('Leave', 'schoolEvents(\'leave\',' . $eventId . ',' . $userEventId . ',' . $optionsStr . ');', 'btn btn-danger btn-xs pull-right');
        return $addNew;
    }

    /**
     * return school Event Notification Btn
     * 
     * @param int $eventId event id
     * @param int $userEventId user event id
     * @param int $page 
     */
    public function schoolEventNotificationBtn($eventId, $userEventId = 0, $optionsStr = '{}') {
        $addNew = $this->htmlButton('<span class="glyphicon glyphicon-bell"></span>', 'schoolEvents(\'notificationPop\',' . $eventId . ',' . $userEventId . ',' . $optionsStr . ');', 'btn btn-info btn-xs pull-right', 'margin-left:5px;');
        return $addNew;
    }

    /**
     * return event events row if no event found
     */
    public function noSchoolEventsRow() {
        $row = ' <tr><td colspan="5">
                    <div class="alert alert-gray">No events found.</div>
                </td></tr>';
        return $row;
    }

    /**
     * Event Email to User Content
     */
    public function eventEmailUserContent($subject, $event, $mode, $contactName = '', $phpTzIdentifier = 'UTC') {
        $name = $event['name'];

        $message = '';
        if ($mode == 'leave') {
            $message .= '<p>You have <b>left</b> event "' . $this->et($name) . '" successfully.</p>';
        } else {
            $message .= '<p>You have <b>joined</b> event "' . $this->et($name) . '" successfully.</p>';
        }

        $lastUpdatedLocal = toLocalDateTime($event['last_updated'], $phpTzIdentifier, 'M d, Y  H:i:s');
        $eventInfor = $this->eventInfor($event, $lastUpdatedLocal);

        //event information
        $message .='<p>' . $eventInfor . '</p>';

        $emailBody = $this->emailBody($message, $subject, $contactName, '', true, SITE_NAME . ' ' . $subject, '');
        return $emailBody;
    }

    /**
     * return event Email Admin Content content
     */
    public function eventEmailAdminContent($subject, $event, $user, $mode, $userContactName = '', $phpTzIdentifier = 'UTC') {

        $name = $event['name'];

        $message = '';
        if ($mode == 'leave') {
            $message .= '<p>"' . $userContactName . '" have <b>left</b> event "' . $this->et($name) . '" successfully.</p>';
        } else {
            $message .= '<p>"' . $userContactName . '" have <b>joined</b> event "' . $this->et($name) . '" successfully.</p>';
        }

        $lastUpdatedLocal = toLocalDateTime($event['last_updated'], $phpTzIdentifier, 'M d, Y  H:i:s');
        $eventInfor = $this->eventInfor($event, $lastUpdatedLocal);
        //event information
        $message .='<p>' . $eventInfor . '</p>';

        //user details
        $message .='<p>
                    User Email: ' . $this->et($user['email']) . '
                    <br>User Username: ' . $this->et($user['username']) . '
                    </p>';


        $emailBody = $this->emailBody($message, $subject, 'Admin', '', true, SITE_NAME . ' ' . $subject, '');

        return $emailBody;
    }

    /**
     * return event Notifications List ui html
     * 
     */
    public function eventNotificationsList($eventsRows, $page, $pageCount, $perPage, $onclickS, $onclickE, $uniquePrefix = '', $options = array()) {

        //start final markup
        $finalMarkup = '';
        $finalMarkup .= '<div class="row"><div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col" id="sch_evtn_cnt" style="margin-top:10px;">';
        //row to hold eventNotifications messages table 
        $finalMarkup .= '   <div class="row"><div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">';
        //motivational eventNotifications table        
        $finalMarkup .= '       <table id="sch_evtn_table" class="table table-striped" style="background-color: white;">
                                    <tbody>';
        $finalMarkup .= '               ' . $eventsRows;
        $finalMarkup .= '           </tbody>
                                </table>';
        $finalMarkup .= '   </div></div>';
        //row to hold eventNotifications table end
        //for pagination row - start
        $finalMarkup .='               <div class="row">';
        $finalMarkup .='                   <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">';
        //set pagination ui
        $finalMarkup .= paginationUi($page, $pageCount, $perPage, $onclickS, $onclickE, $uniquePrefix, false, $options, 'text-center');
        $finalMarkup .='                   </div>';
        $finalMarkup .='               </div>';
        //for pagination row - end
        $finalMarkup .= '</div></div>';

        return $finalMarkup;
    }

    /**
     * return event Notifications row 
     */
    public function eventNotificationsRow($notification, $lastUpdatedLocal) {
        $notificationInfor=$this->notificationInfor($notification, $lastUpdatedLocal);
        $row = '<tr id="tr_evtn_' . $notification['id'] . '">
                    <td style="position: relative;">
                        '.$notificationInfor.'
                    </td>
                </tr>';
        return $row;
    }

    /**
     * notificaitonInfor
     * 
     * @param type $notification
     * @param type $lastUpdatedLocal
     * @return string
     */
    private function notificationInfor($notification, $lastUpdatedLocal) {
        $html = '<dt>
                    <h4>' . $this->et($notification['title']) . '</h4>
                 </dt>
                 <dd><p>' . nl2br($this->et($notification['text'])) . '</p></dd>
                 <dd><small><em>' . $lastUpdatedLocal . '</em></small></dd>';
        return $html;
    }

    /**
     * return event notifications row if no event found
     */
    public function noEventNotificationsRow() {
        $row = ' <tr><td colspan="5">
                    <div class="alert alert-gray">No events notifications found.</div>
                </td></tr>';
        return $row;
    }

    /**
     * event Notifications Dialogs
     * 
     * @param array $event event data
     * @param string $mode add | edit
     * @param bool $placeHolderOnly if true return place holder only
     * @param string $notification notification data
     * @param array $options options array
     * @return string
     */
    public function eventNotificationsDialogs($event, $placeHolderOnly = false, $notification = '', $options = array(), $phpTzIdentifier = 'UTC') {
        $mode = 'view';

        $markup = '<div id="dialog-' . $mode . '-eventn" style="display: none;">';
        if (!$placeHolderOnly) {
            $lastUpdatedLocal = toLocalDateTime($event['last_updated'], $phpTzIdentifier, 'M d, Y  H:i:s');

            $eventInfor = $this->eventInfor($event, $lastUpdatedLocal);

            $markup .= '<div class="row" style="padding-top:10px;">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                                ' . $eventInfor . '
                            </div>
                        </div>  
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col" id="' . $mode . '_evtn_data">
                               ' . $notification . '
                            </div>
                        </div>                         
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                                <button type="button" style="margin-right: 10px;" class="btn btn-purple-light btn-sm pull-right" onclick="$.fancybox.close(true);">Close</button>                               
                            </div>
                        </div>';
        }
        $markup .= '</div>';
        return $markup;
    }

}

?>