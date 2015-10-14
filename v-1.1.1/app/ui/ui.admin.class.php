<?php

/*
 * hold footer ui functions
 */
require_once "ui.common.class.php";

class uiAdmin extends uiCommon {

    public function __construct() {
        
    }

    //return seaction list markup for left column
    private function sectionList() {
        $finalMarkup = '<ul id="ad_sec_head" class="nav nav-pills nav-stacked">
                            <li class="events"><a onclick="loadAdminSection(\'events\')" href="javascript:void(0);">Events</a></li>
                        </ul>';
        return $finalMarkup;
    }

    /*
     * return admin page ui html 
     */

    public function adminPage($reload) {
        $finalMarkup = '';
        $finalMarkup .= '<div class="container-fluid admin-page-container">';
        $finalMarkup .= '<div class="row">';
        $finalMarkup .= '   <div class="col-md-12">';
        $finalMarkup .= '       <h3>' . SITE_NAME . ' Administration</h3>';
        $finalMarkup .= '   </div>';
        $finalMarkup .= '</div>';
        //admin main layout
        $finalMarkup .='<div class="row admin">';
        $finalMarkup .= '   <div class="col-md-12">';
        $finalMarkup .='        <div class="row">';
        //left side
        $finalMarkup .= '           <div class="col-md-2" style="background-color: #E6E6E6;padding-right:0px;padding-left:0px;">';
        $finalMarkup .= $this->sectionList();
        $finalMarkup .= '           </div>';
        //right side
        $finalMarkup .= '           <div class="col-md-10" style="background-color: #BDBDBD;">';
        $finalMarkup .='                <div class="row" style="padding:5px 5px 5px 5px;">';
        $finalMarkup .= '                   <div id="admin_cnt" class="col-md-12" style="min-height:450px;">';
        $finalMarkup .= '                   </div>';
        $finalMarkup .= '               </div>';
        $finalMarkup .= '           </div>';
        $finalMarkup .= '       </div>';
        $finalMarkup .= '   </div>';
        $finalMarkup .= '</div>';
        $finalMarkup .= '</div><!-- ./container-fluid -->';

        if (!$reload) {
            $finalMarkup .='<script src="' . JS_URL . '/bootstrap-datetimepicker.js"></script>';
            $finalMarkup .='<script src="' . JS_URL . '/admin.js"></script>';
        }
        return $finalMarkup;
    }

    /*
     * return admin section content area header/title row
     */

    public function sectionHeadRow($headText) {
        $finalMarkup = '<div class="row" style="background-color: #BCA9F5;">
                            <div class="col-md-12">';
        $finalMarkup .= '       <div style="width:100%;padding:10px 20px 10px 20px;">
                                    <span class="badge" style="font-size:150%;background-color: #D2C5F7;padding:5px 5px 5px 5px;">' . $headText . '</span>
                                </div>';
        $finalMarkup .= '   </div>';
        $finalMarkup .= '</div>';
        return $finalMarkup;
    }

    /*
     * return Fields Table Column Headrs th ui html
     */

    public function fieldsTableColumnHeadrsTh($idPrefix, $field, $onclcik, $fieldName, $fieldThIcon) {
        $th = '<th><a class="' . $idPrefix . 'th_' . $field . '" onclick="' . $onclcik . '" href="javascript:void(0)">' . $fieldName . '' . $fieldThIcon . '</a></th>';
        return $th;
    }

    /*
     * fieldsTableColumnHeadrsThGlyphiconType
     */

    public function fieldsTableColumnHeadrsThGlyphiconType($type) {
        $glyphiconType = '';
        switch ($type) {
            case 'text':
                $glyphiconType = 'glyphicon-sort-by-alphabet';
                break;
            case 'number':
                $glyphiconType = 'glyphicon-sort-by-order';
                break;
            case 'other':
            default :
                $glyphiconType = 'glyphicon-sort-by-attributes';
                break;
        }
        return $glyphiconType;
    }

    /*
     * fieldsTableColumnHeadrsThGlyphiconSpan
     * $alt; if true append [-alt] at the end of $glyphiconType name
     */

    public function fieldsTableColumnHeadrsThGlyphiconSpan($glyphiconType, $alt = false) {
        if ($alt)
            $glyphiconType.='-alt';
        $fieldThIcon = '<span class="glyphicon ' . $glyphiconType . '"></span>';
        return $fieldThIcon;
    }

    /**
     * return events Selction ui html
     */
    public function eventsSelction($columnHeadrs, $eventsRows, $page, $pageCount, $perPage, $onclickS, $onclickE, $uniquePrefix = '') {
        //add new button
        $addNew = $this->eventsAddNewBtn();

        //start final markup
        $finalMarkup = '';
        $headText = SITE_NAME . ' Events';
        $finalMarkup .=$this->sectionHeadRow($headText);
        $finalMarkup .= '<div class="row"><div class="col-md-12" id="adm_evt_cnt" style="margin-top:10px;">';
        //row to hold add new button
        $finalMarkup .= '   <div class="row" style="padding-bottom:10px;"><div class="col-md-12">' . $addNew . '</div></div>';
        //row to hold motivational messages table 
        $finalMarkup .= '   <div class="row"><div class="col-md-12">';
        //motivational messages table        
        $finalMarkup .= '       <table class="table table-striped" style="background-color: white;">
                                    <thead><tr>
                                        ' . $columnHeadrs . '
                                        <th class="events-action-th"></th>
                                    </tr></thead>
                                    <tbody>';
        $finalMarkup .= '               ' . $eventsRows;
        $finalMarkup .= '           </tbody>
                                </table>';
        $finalMarkup .= '   </div></div>';
        //row to hold events table end
        //for pagination row - start
        $finalMarkup .='               <div class="row">';
        $finalMarkup .='                   <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">';
        //set pagination ui
        $finalMarkup .= paginationUi($page, $pageCount, $perPage, $onclickS, $onclickE, $uniquePrefix);
        $finalMarkup .='                   </div>';
        $finalMarkup .='               </div>';
        //for pagination row - end
        $finalMarkup .= '</div></div>';

        $finalMarkup .=$this->eventsDialogs('add');
        //for edit popup we only return place holder so we can use it to populate edit popup via ajax
        $finalMarkup .=$this->eventsDialogs('edit', true);


        return $finalMarkup;
    }

    /**
     *  return events Add New Btn
     */
    public function eventsAddNewBtn() {
        $addNew = $this->htmlButton('Add New Events', 'events(\'addPop\',0)', 'btn btn-primary pull-right');
        return $addNew;
    }

    /**
     * events Dialogs
     * 
     * @param string $mode add | edit
     * @return string
     */
    private function eventsDialogsO($mode = 'add') {
        $markup = '<div id="dialog-' . $mode . '-event" style="display: none;">  
                        <div class="row" style="padding-top:10px;"> 
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                                <label>Fill Event Details</label>
                            </div>
                        </div>
                        <div class="row" style="padding-top:10px;">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                                <div class="form-group">
                                    <label for="' . $mode . '_chal_name">Name</label>
                                    <input type="text" id="' . $mode . '_chal_name" name="' . $mode . '_chal_name"  value="" class="form-control" placeholder="Name" maxlength="50">
                                </div>
                            </div>
                        </div>  
                        <div class="row">
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no-padding-col">
                                <div class="form-group">
                                    <label for="' . $mode . '_chal_days">Days</label>
                                    <input type="text" id="' . $mode . '_chal_days" name="' . $mode . '_chal_days"  value="" class="form-control " placeholder="Days" maxlength="4">
                                </div>
                            </div>                            
                        </div> 
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 no-padding-col">
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                          <input id="' . $mode . '_chal_enabled" name="' . $mode . '_chal_enabled"  type="checkbox" checked> Enabled
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                                <input type="hidden" id="' . $mode . '_chal_id" value="0">
                                <button id="' . $mode . '-save-btn" type="button" class="btn btn-purple-light btn-sm pull-right" onclick="events(\'' . $mode . '\',0);">Save Event</button>
                                <button type="button" style="margin-right: 10px;" class="btn btn-purple-light btn-sm pull-right" onclick="$.fancybox.close(true);">Close</button>                               
                            </div>
                        </div>
                   </div>';
        return $markup;
    }

    /**
     * event event Dialogs
     * 
     * @param string $mode add | edit
     * @param bool $placeHolderOnly if true return place holder only
     * @param array $event event data
     * @param array $options options array
     * @return string
     */
    public function eventsDialogs($mode = 'add', $placeHolderOnly = false, $event = array(), $options = array()) {
        $markup = '<div id="dialog-' . $mode . '-event" style="display: none;">';
        if (!$placeHolderOnly) {
            $eventId = 0;
            $name = $description = $startDay = $endDay = $Deadline = $type = '';
            $publishChecked = 'checked';

            if (!empty($event)) {
                //popuplate at edit mode
                $eventId = $event['id'];
                $name = $event['name'];
                $startDay = formatDate($event['start_date'], 'M d, Y');
                $endDay = formatDate($event['end_date'], 'M d, Y');
                $Deadline = formatDate($event['apply_deadline'], 'M d, Y');
                if ($event['publish'] == 0)
                    $publishChecked = '';
                $description = $event['description'];
                $type = $event['type'];
            }

            $optionStr = '{}';
            if (!empty($options))
                $optionStr = str_replace('"', "'", json_encode($options));
            $onclickSave = 'events(\'' . $mode . '\',' . $eventId . ',' . $optionStr . ');';

            $evenTypeSelect = $this->evenTypeSelect($type, $mode . '_evt_type', $mode . '_evt_type', '', '', $class = 'input-medium form-control', '');

            $requiredIcon = '<small><span class="glyphicon glyphicon glyphicon-asterisk required"></span></small>';
            $markup .= '<div class="row" style="padding-top:10px;"> 
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                                <label>Fill Event Details</label>
                            </div>
                        </div>
                        <div class="row" style="padding-top:10px;">
                            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 no-left-padding-col">
                                <div class="form-group">
                                    <label for="' . $mode . '_evt_name">Name ' . $requiredIcon . '</label>
                                    <input type="text" id="' . $mode . '_evt_name" name="' . $mode . '_evt_name"  value="' . $name . '" class="form-control" placeholder="Name" maxlength="50">
                                </div>
                            </div>
                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 no-right-padding-col">
                                <div class="form-group">
                                    <label for="school"> Type ' . $requiredIcon . '</label>
                                    ' . $evenTypeSelect . '
                                </div>
                            </div>
                        </div>  
                        <div class="row">
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no-left-padding-col">
                                <div class="form-group">
                                    <label for="' . $mode . '_evt_start">Start Date ' . $requiredIcon . '</label>                                    
                                    <input type="text" id="' . $mode . '_evt_start" name="' . $mode . '_evt_start"  value="' . $startDay . '" class="form-control " placeholder="" readonly  style="background-color: #FFFFFF;cursor: pointer;" maxlength="20">
                                </div>
                            </div> 
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 ">
                                <div class="form-group">
                                    <label for="' . $mode . '_evt_end">End Date ' . $requiredIcon . '</label>
                                    <input type="text" id="' . $mode . '_evt_end" name="' . $mode . '_evt_end"  value="' . $endDay . '" class="form-control " placeholder="" readonly  style="background-color: #FFFFFF;cursor: pointer;" maxlength="20">
                                </div>
                            </div>  
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 ">
                                <div class="form-group">
                                    <label for="' . $mode . '_evt_deadline">Deadline ' . $requiredIcon . '</label>
                                    <input type="text" id="' . $mode . '_evt_deadline" name="' . $mode . '_evt_deadline"  value="' . $Deadline . '" class="form-control " placeholder="" readonly  style="background-color: #FFFFFF;cursor: pointer;" maxlength="20">
                                </div>
                            </div>  
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no-right-padding-col" style="padding-top:20px;">
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                          <input id="' . $mode . '_evt_published" name="' . $mode . '_evt_published"  type="checkbox" ' . $publishChecked . '> Publish
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row" >
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                                <div class="form-group">
                                    <label for="' . $mode . '_evt_description">Description ' . $requiredIcon . '</label>
                                    <textarea id="' . $mode . '_evt_description" name="' . $mode . '_chalp_description" placeholder=""  class="form-control" rows="5">' . $description . '</textarea>    
                                </div>
                            </div>
                        </div>  
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                                <input type="hidden" id="' . $mode . '_evt_id" value="' . $eventId . '">
                                <button id="' . $mode . '-evt-save-btn" type="button" class="btn btn-purple-light btn-sm pull-right" onclick="' . $onclickSave . '">Save Event</button>
                                <button type="button" style="margin-right: 10px;" class="btn btn-purple-light btn-sm pull-right" onclick="$.fancybox.close(true);">Close</button>                               
                            </div>
                        </div>';
        }
        $markup .= '</div>';
        return $markup;
    }

    /**
     * return event type Select element
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
    private function evenTypeSelect($selectedType = '', $name = '', $id = '', $extraAttributes = '', $onchange = '', $class = '', $style = '') {

        $types = array('internships' => 'Internships', 'job shadows' => 'Job Shadows', 'volunteering' => 'Volunteering');
        $optMarkup = '';
//        //empty option
//        $optMarkup.=$this->htmlSelectOptions('', 'select grade', '', '', '', 'empty_option', '');
        if (!empty($types)) {
            foreach ($types as $val => $opt) {
                $optSelected = '';
                if ($selectedType == $val)
                    $optSelected .= 'selected';

                $optMarkup.=$this->htmlSelectOptions($this->et($val), $this->et($opt), '', '', $optSelected);
            }
        }
        $select = $this->htmlSelect($optMarkup, $name, $id, $extraAttributes, $onchange, $class, $style);

        return $select;
    }

    /**
     * return event row 
     */
    public function eventRow($event, $lastUpdatedLocal, $edit, $delLink, $notificationBtn, $userBtn, $phpTzIdentifier) {
        $published = '<div class="label label-success">yes</div>';
        if ($event['publish'] == '0')
            $published = '<div class="label label-danger">no</div>';

        $row = '<tr id="tr_evt_' . $event['id'] . '" >
                    <td style="position: relative;"><div style="width:100%;">' . $this->et($event['name']) . '</div></td>
                    <td style="position: relative;"><div style="width:100%;">' . $this->et(ucwords($event['type'])) . '</div></td>';
//        $row .= '   <td style="position: relative;"><div style="width:100%;">' . $this->et(toLocalDateTime($event['start_date'], $phpTzIdentifier, 'M d, Y')) . '</div></td>
//                    <td style="position: relative;"><div style="width:100%;">' . $this->et(toLocalDateTime($event['end_date'], $phpTzIdentifier, 'M d, Y')) . '</div></td>';
        $row .= '   <td style="position: relative;"><div style="width:100%;">' . $this->et(formatDate($event['apply_deadline'], 'M d, Y')) . '</div></td>
                    <td style="position: relative;"><div style="width:100%;">' . $published . '</div></td>
                    <td style="position: relative;"><div style="width:100%;">' . $lastUpdatedLocal . '</div></td>
                    <td style="position: relative;" class="action-td"><div style="width:100%;">' . $delLink . $edit . $userBtn . $notificationBtn . '</div></td>
                </tr>';
        return $row;
    }

    /**
     * return event edit button
     */
    public function eventEditBtn($eventId, $page = 1) {
        $btn = '<a href="javascript:void(0)" class="pull-right" onclick="events(\'editPop\',' . $eventId . ',{page:' . $page . '})"><span class="glyphicon glyphicon-edit"></span></a>';
        return $btn;
    }

    /**
     * return event delete button
     */
    public function eventDeleteBtn($eventId, $page = 1) {
        $btn = '<a href="javascript:void(0)" class="pull-right" onclick="events(\'delete\',' . $eventId . ',{page:' . $page . '})"><span class="glyphicon glyphicon-remove-circle"></span></a>';
        return $btn;
    }

    /**
     * return event notifications button
     */
    public function eventNotificationBtn($eventId) {
        $btn = '<button class="btn btn-xs btn-default pull-right" onclick="eventNotifications(\'list\',' . $eventId . ',0,{page:1})">notifications</button>';
        return $btn;
    }

    /**
     * return event user button
     */
    public function eventUserBtn($eventId) {
        $btn = '<button class="btn btn-xs btn-default pull-right" onclick="eventUsers(\'list\',' . $eventId . ',0,{page:1})">users</button>';
        return $btn;
    }

    /**
     * return event row if no event found
     */
    public function noEventRow() {
        $row = ' <tr><td colspan="6">
                    <div class="alert alert-gray">No events found.</div>
                </td></tr>';
        return $row;
    }

    /**
     * return event Notifications List ui html
     * 
     * @param array $event event data
     */
    public function eventNotificationsList($event, $columnHeadrs, $eventsRows, $page, $pageCount, $perPage, $onclickS, $onclickE, $uniquePrefix = '') {
        //add new button
        $addNew = $this->eventNotificationsAddNewBtn($event['id']);

        //start final markup
        $finalMarkup = '';
        $finalMarkup .=$this->eventNotificationHead($event);
        $finalMarkup .= '<div class="row"><div class="col-md-12" id="adm_evtn_cnt" style="margin-top:10px;">';
        //row to hold add new button
        $finalMarkup .= '   <div class="row" style="padding-bottom:10px;"><div class="col-md-12">' . $addNew . '</div></div>';
        //row to hold eventNotifications messages table 
        $finalMarkup .= '   <div class="row"><div class="col-md-12">';
        //motivational eventNotifications table        
        $finalMarkup .= '       <table class="table table-striped" style="background-color: white;">
                                    <thead><tr>
                                        ' . $columnHeadrs . '
                                        <th class="eventsn-action-th"></th>
                                    </tr></thead>
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
        $finalMarkup .= paginationUi($page, $pageCount, $perPage, $onclickS, $onclickE, $uniquePrefix);
        $finalMarkup .='                   </div>';
        $finalMarkup .='               </div>';
        //for pagination row - end
        $finalMarkup .= '</div></div>';

        $finalMarkup .=$this->eventNotificationsDialogs($event, 'add');
        //for edit popup we only return place holder so we can use it to populate edit popup via ajax
        $finalMarkup .=$this->eventNotificationsDialogs($event, 'edit', true);


        return $finalMarkup;
    }

    /**
     * return event Notifications Add New Btn
     * 
     * @param int $eventId event id
     */
    public function eventNotificationsAddNewBtn($eventId) {
        $addNew = $this->htmlButton('Add New Notification', 'eventNotifications(\'addPop\',' . $eventId . ',0)', 'btn btn-primary pull-right');
        return $addNew;
    }

    /**
     * event Notifications Dialogs
     * 
     * @param array $event event data
     * @param string $mode add | edit
     * @param bool $placeHolderOnly if true return place holder only
     * @param array $notification notification data
     * @param array $options options array
     * @return string
     */
    public function eventNotificationsDialogs($event, $mode = 'add', $placeHolderOnly = false, $notification = array(), $options = array()) {
        $markup = '<div id="dialog-' . $mode . '-eventn" style="display: none;">';
        if (!$placeHolderOnly) {
            $notificationId = 0;
            $title = $text = '';
            $publishChecked = 'checked';

            if (!empty($notification)) {
                //popuplate at edit mode
                $notificationId = $notification['id'];
                $title = $notification['title'];

                if ($notification['publish'] == 0)
                    $publishChecked = '';
                $text = $notification['text'];
            }
            $optionStr = '{}';
            if (!empty($options))
                $optionStr = str_replace('"', "'", json_encode($options));
            $onclickSave = 'eventNotifications(\'' . $mode . '\',' . $event['id'] . ',' . $notificationId . ',' . $optionStr . ');';

            $markup .= '<div class="row" style="padding-top:10px;"> 
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                                <label>Fill Notification Details</label>
                            </div>
                        </div>
                        <div class="row" style="padding-top:10px;">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                                <div class="form-group">
                                    <label for="' . $mode . '_evtn_title">Title</label>
                                    <input type="text" id="' . $mode . '_evtn_title" name="' . $mode . '_evtn_title"  value="' . $title . '" class="form-control" placeholder="Title" maxlength="50">
                                </div>
                            </div>
                        </div>  
                        <div class="row">
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 no-padding-col">
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                          <input id="' . $mode . '_evtn_publish" name="' . $mode . '_evtn_publish"  type="checkbox" ' . $publishChecked . '> Publish
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row" >
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                                <div class="form-group">
                                    <label for="' . $mode . '_evtn_text">Text</label>
                                    <textarea id="' . $mode . '_evtn_text" name="' . $mode . '_evtn_text" placeholder=""  class="form-control" rows="5">' . $text . '</textarea>    
                                </div>
                            </div>
                        </div>  
                        <div class="row" >
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                                <h6>Note: when publish is checked and save email notification will be as well as publishing. Email sending can take more time, so please wait.</small></h6>
                            </div>
                        </div>  
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 no-padding-col">
                                <input type="hidden" id="' . $mode . '_evtn_id" value="' . $notificationId . '">
                                <button id="' . $mode . '-evtn-save-btn" type="button" class="btn btn-purple-light btn-sm pull-right" onclick="' . $onclickSave . '">Save Notification</button>
                                <button type="button" style="margin-right: 10px;" class="btn btn-purple-light btn-sm pull-right" onclick="$.fancybox.close(true);">Close</button>                               
                            </div>
                        </div>';
        }
        $markup .= '</div>';
        return $markup;
    }

    /**
     * return event Notifications row 
     */
    public function eventNotificationsRow($notification, $lastUpdatedLocal, $edit, $delLink) {
        $publish = '<div class="label label-success">yes</div>';
        if ($notification['publish'] == '0')
            $publish = '<div class="label label-danger">no</div>';

        $row = '<tr id="tr_evtn_' . $notification['id'] . '">
                    <td style="position: relative;"><div style="width:100%;">' . $this->et($notification['title']) . '</div></td>
                    <td style="position: relative;"><div style="width:100%;">' . $publish . '</div></td>
                    <td style="position: relative;"><div style="width:100%;">' . $lastUpdatedLocal . '</div></td>
                    <td style="position: relative;" class="action-td"><div style="width:100%;">' . $delLink . $edit . '</div></td>
                </tr>';
        return $row;
    }

    /**
     * return event Notifications edit button
     * 
     * @param int $eventId event id
     * @param int $uid event notification id
     * @param int $page page number
     * @return string
     */
    public function eventNotificationsEditBtn($eventId, $uid, $page = 1) {
        $btn = '<a href="javascript:void(0)" class="pull-right" onclick="eventNotifications(\'editPop\',' . $eventId . ',' . $uid . ',{page:' . $page . '})"><span class="glyphicon glyphicon-edit"></span></a>';
        return $btn;
    }

    /**
     * return event Notifications delete button
     * 
     * @param int $eventId event id
     * @param int $uid event notification id
     * @param int $page page number
     * @return string
     */
    public function eventNotificationsDeleteBtn($eventId, $uid, $page = 1) {
        $btn = '<a href="javascript:void(0)" class="pull-right" onclick="eventNotifications(\'delete\',' . $eventId . ',' . $uid . ',{page:' . $page . '})"><span class="glyphicon glyphicon-remove-circle"></span></a>';
        return $btn;
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
     * event Notification list Head
     * 
     * @param array $event
     * @return string
     */
    public function eventNotificationHead($event) {
        $finalMarkup = '<div class="row">
                            <div class="col-md-12">';
        $finalMarkup .= '       <ol class="breadcrumb">
                                    <li><a href="javascript:void(0);" onclick="loadAdminSection(\'events\')">Events</a></li>
                                    <li class="active">' . $this->et($event['name']) . ' <em>: Notifications</em></li>
                                </ol>';
        $finalMarkup .= '   </div>';
        $finalMarkup .= '</div>';
        return $finalMarkup;
    }

    /**
     * Event Notification Email Content to User 
     */
    public function eventNotificationEmailContent($subject, $event, $data, $mode, $contactName = '') {

        $message = '';
        $message .='<p>' . $this->notificationInfor($data) . '</p>';

        //event information
        $phpTzIdentifier = getPhpTzIdentifierFromSession();
        $lastUpdatedLocal = toLocalDateTime($event['last_updated'], $phpTzIdentifier, 'M d, Y  H:i:s');
        $eventInfor = $this->eventInfor($event, $lastUpdatedLocal);
        $message .='<hr><p>                    
                    ' . $eventInfor . '
                    </p>';

        $emailBody = $this->emailBody($message, $subject, $contactName, '', true, SITE_NAME . ' ' . $subject, '');
        return $emailBody;
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
    private function eventInfor($event, $lastUpdatedLocal) {
        $name = $event['name'];
        $startDay = formatDate($event['start_date'], 'M d, Y');
        $endDay = formatDate($event['end_date'], 'M d, Y');
        $Deadline = formatDate($event['apply_deadline'], 'M d, Y');
        $description = $event['description'];
        $type = ucwords($event['type']);

        $eventInfor = ' <dt>
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
     * notificaitonInfor
     * 
     * @param type $notification
     * @param type $lastUpdatedLocal
     * @return string
     */
    private function notificationInfor($notification, $lastUpdatedLocal = '') {
        $html = '<dt>
                    <h4>' . $this->et($notification['title']) . '</h4>
                 </dt>
                 <dd><p>' . nl2br($this->et($notification['text'])) . '</p></dd>';
        if ($lastUpdatedLocal != '')
            $html = ' <dd><small><em>' . $lastUpdatedLocal . '</em></small></dd>';
        return $html;
    }

    /**
     * return event Users List ui html
     * 
     * @param array $event event data
     */
    public function eventUsersList($event, $columnHeadrs, $eventsRows, $page, $pageCount, $perPage, $onclickS, $onclickE, $uniquePrefix = '') {

        //start final markup
        $finalMarkup = '';
        $finalMarkup .=$this->eventUserHead($event);
        $finalMarkup .= '<div class="row"><div class="col-md-12" id="adm_evtu_cnt" style="margin-top:10px;">';
        //row to hold eventUsers messages table 
        $finalMarkup .= '   <div class="row"><div class="col-md-12">';
        //motivational eventUsers table        
        $finalMarkup .= '       <table class="table table-striped" style="background-color: white;">
                                    <thead><tr>
                                        ' . $columnHeadrs . '
                                    </tr></thead>
                                    <tbody>';
        $finalMarkup .= '               ' . $eventsRows;
        $finalMarkup .= '           </tbody>
                                </table>';
        $finalMarkup .= '   </div></div>';
        //row to hold eventUsers table end
        //for pagination row - start
        $finalMarkup .='               <div class="row">';
        $finalMarkup .='                   <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">';
        //set pagination ui
        $finalMarkup .= paginationUi($page, $pageCount, $perPage, $onclickS, $onclickE, $uniquePrefix);
        $finalMarkup .='                   </div>';
        $finalMarkup .='               </div>';
        //for pagination row - end
        $finalMarkup .= '</div></div>';


        return $finalMarkup;
    }

    /**
     * return event users row if no event found
     */
    public function noEventUsersRow() {
        $row = ' <tr><td colspan="6">
                    <div class="alert alert-gray">No events users found.</div>
                </td></tr>';
        return $row;
    }

    /**
     * return event Users row 
     */
    public function eventUsersRow($user, $phpTzIdentifier) {
        $joinedLocal = toLocalDateTime($user['joined'], $phpTzIdentifier, 'M d, Y  H:i:s');

        $row = '<tr id="tr_evtu_' . $user['id'] . '">
                    <td style="position: relative;"><div style="width:100%;">' . $this->et($user['name']) . '</div></td>
                    <td style="position: relative;"><div style="width:100%;">' . $this->et($user['email']) . '</div></td>
                    <td style="position: relative;"><div style="width:100%;">' . $this->et($user['username']) . '</div></td>
                    <td style="position: relative;"><div style="width:100%;">' . $this->et($user['grade']) . '</div></td>
                    <td style="position: relative;"><div style="width:100%;">' . $this->et($user['school_name']) . '</div></td>
                    <td style="position: relative;"><div style="width:100%;">' . $this->et($joinedLocal) . '</div></td>
                </tr>';
        return $row;
    }

    /**
     * event User list Head
     * 
     * @param array $event
     * @return string
     */
    public function eventUserHead($event) {
        $finalMarkup = '<div class="row">
                            <div class="col-md-12">';
        $finalMarkup .= '       <ol class="breadcrumb">
                                    <li><a href="javascript:void(0);" onclick="loadAdminSection(\'events\')">Events</a></li>
                                    <li class="active">' . $this->et($event['name']) . ' <em>: Users</em></li>
                                </ol>';
        $finalMarkup .= '   </div>';
        $finalMarkup .= '</div>';
        return $finalMarkup;
    }

}

?>