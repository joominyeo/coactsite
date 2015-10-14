<?php

class admin {
    /*
     * variables
     */

    private $res = array('status' => true, 'text' => '', 'error' => '');
    private $userId;
    private $db;
    private $ui;
    private $user;

    /**
     * set true if calls is from api
     * @var bool 
     */
    private $api = false;

    public function __construct($userId = 0, $api = false) {
        $this->db = new DB(DBCONN, DBUSER, DBPASS);
        $this->userId = $userId;
        $this->api = $api;

        require_once "ui/ui.admin.class.php";
        $this->ui = new uiAdmin();
    }

    /**
     * set user class to $this->user
     */
    private function setUserClass() {
        require_once "logic/user.class.php";
        $this->user = new user($this->userId, $this->api);
    }

    /*
     * return admin page
     * if $markup true return only markup html
     * $reload =false //indicate page load, reload by user, default not reload
     */

    public function adminPage($markup = false, $reload = false) {
        $finalMarkup = $this->ui->adminPage($reload);
        if ($markup)
            return $finalMarkup;
        else {
            $this->res['cnt'] = $finalMarkup;
            $this->res['text'] = '';
            $this->res['status'] = true;
            return $this->res;
        }
    }

    /**
     * return give section area markup 
     * 
     * @param type $section
     * @param type $options
     * @param type $phpTzIdentifier
     * @return type
     */
    public function loadAdminSection($section, $options, $phpTzIdentifier = 'UTC') {
        switch ($section) {
            case 'events':
                $this->res = $this->eventsSelction($phpTzIdentifier, $options);
                break;
            default :
                $this->res['status'] = false;
                $this->res['error'] = 'Sorry! Invalid Request.';
        }
        return $this->res;
    }

    /*
     * return order by from options sent via ajax
     * 
     * $options : wihch has orderBy, orderDir indexes if need order by, these can be empty
     * $defultOrderBy : default order by field
     * $defultOrderDir : asc|desc default order by direction 
     * $fields: array of fields indexed by field id and and field type as value
     * filed array field type can be text|number|other 
     * $fields = array('ad_location' => 'text',
      'ad_title' => 'text',
      'ad_type' => 'text',
      'last_updated' => 'other'
      );
     * 
     * 
     */

    private function getOrderByFromOptions($options, $fields, $defultOrderBy = '', $defultOrderDir = '') {

        $orderByField = '';
        $orderDir = '';
        //set defult order by direction
        if (trim($defultOrderDir) != '') {
            if (in_array($defultOrderDir, array('asc', 'desc')))
                $defultOrderDir = 'desc';
            else
                $defultOrderDir = 'asc';
        }

        //set defult order by and direction
        if (trim($defultOrderBy) != '') {
            $orderByField = $defultOrderBy;
            //now since default order by field is set we can set default orderby direction
            $orderDir = $defultOrderDir;
        }

        if (!empty($options['orderBy']) && in_array($options['orderBy'], array_keys($fields)))
            $orderByField = $options['orderBy'];
        if (!empty($options['orderDir'])) {
            if ($options['orderDir'] == 'asc')
                $orderDir = 'asc';
            else
                $orderDir = 'desc';
        }
        //if order by fields and direction not set set deault order by direction 
        if ($orderByField != '' && $orderDir == '')
            $orderDir = $defultOrderDir;


        return array('orderByField' => $orderByField, 'orderDir' => $orderDir);
    }

    /*
     * return table coolumn header with ordre by scritpa and css
     * 
     * $orderByField; field name to order by
     * $orderDir: asc|desc
     * 
     * $fields: array of fields indexed by field id and and field type as value
     * filed array field type can be text|number|other 
     * filed array field type can be text|number|other 
     * $fields = array('ad_location' => 'text',
      'ad_title' => 'text',
      'ad_type' => 'text',
      'last_updated' => 'other'
      );
     * 
     * $fieldNames : field names index by field id
      $fieldNames=array(
      'ad_location' => 'Ad Location',
      'ad_type' => 'Ad Type',
      'ad_title' => 'Ad Title',
      'last_updated' => 'Last Updated'
      );
     * 
     * $onclickS : start of onclick th functions str
     * $onclickE : end of onclick th function str
     * ex : 
     * //except order by bject string {} other parts of onclick funcitons
      $onclickS = 'ads(\'sort\',0,';
      $onclickE = ');';
     * 
     * $idPrefix : prefeix to ids
     * 
     */

    private function getFieldsTableColumnHeadrs($fields, $fieldNames, $orderByField, $orderDir, $onclickS, $onclickE, $idPrefix = '', $page = 1) {

        $thHtml = '';
        foreach ($fields as $field => $type) {
            $fieldThIcon = '';
            $fieldThDir = 'asc';
            $fieldName = $fieldNames[$field];
            if ($orderByField == $field) {
                $glyphiconType = $this->ui->fieldsTableColumnHeadrsThGlyphiconType($type);
                if ($orderDir == 'asc') {
                    //if now order by asc next we have do desc
                    $fieldThDir = 'desc';
                    $fieldThIcon = $this->ui->fieldsTableColumnHeadrsThGlyphiconSpan($glyphiconType, false);
                } else {
                    $fieldThDir = 'asc';
                    $fieldThIcon = $this->ui->fieldsTableColumnHeadrsThGlyphiconSpan($glyphiconType, true);
                    ;
                }
            }
            //set th for field            
            $onclcik = $onclickS . '{orderBy:\'' . $field . '\',orderDir:\'' . $fieldThDir . '\',page:' . $page . '}' . $onclickE;
            $th = $this->ui->fieldsTableColumnHeadrsTh($idPrefix, $field, $onclcik, $fieldName, $fieldThIcon);
            $thHtml.=$th;
        }

        return $thHtml;
    }

    /**
     * load content related to events section
     * other options $options
     */
    private function eventsSelction($phpTzIdentifier = 'UTC', $options = array(), $markup = false) {
        //get per page and page number from options
        $pageSettings = $this->pageSettingsFromOptions($options);

        //get per page 
        $perPage = getPerPage($pageSettings['perPage']);
        //page requested
        $pageR = $pageSettings['page'];

        //fields we show in table
        $fields = array(
            'name' => 'text', 'type' => 'other',
//            'start_date' => 'other', 'end_date' => 'other',
            'apply_deadline' => 'other', 'publish' => 'other', 'last_updated' => 'other'
        );
        $fieldNames = array(
            'name' => 'Name', 'type' => 'Type',
//            'start_date' => 'Start', 'end_date' => 'End',
            'apply_deadline' => 'Deadline', 'publish' => 'other', 'last_updated' => 'Last Updated'
        );
        //get order by 
        $orderByRes = $this->getOrderByFromOptions($options, $fields, 'last_updated', 'desc');
        //set ordery by from options
        $orderByField = $orderByRes['orderByField'];
        $orderDir = $orderByRes['orderDir'];

        $eventsRes = $this->eventsRows($phpTzIdentifier, $orderByField, $orderDir, $pageR, $perPage);
        $eventsRows = $eventsRes['markup'];
        $pageCount = $eventsRes['pageCount'];
//                $nextPage = $eventsRes['nextPage'];
        //page we can return after validation etc
        $page = $eventsRes['page'];

        //except order by bject string {} other parts of onclick funcitons
        $onclickS = 'events(\'sort\',0,';
        $onclickE = ');';

        $columnHeadrs = $this->getFieldsTableColumnHeadrs($fields, $fieldNames, $orderByField, $orderDir, $onclickS, $onclickE, 'evt_', $page);

        $finalMarkup = $this->ui->eventsSelction($columnHeadrs, $eventsRows, $page, $pageCount, $perPage, $onclickS, $onclickE, 'events');

        if ($markup)
            return $finalMarkup;
        else {
            $this->res['adcnt'] = $finalMarkup;
            $this->res['text'] = '';
            $this->res['status'] = true;
            return $this->res;
        }
    }

    /**
     * return events rows
     */
    private function eventsRows($phpTzIdentifier = 'UTC', $orderBy = 'last_updated', $orderDir = 'desc', $page = 1, $perPage = ROWS_PER_PAGE) {
        //make sure order by has
        if (empty($orderBy))
            $orderBy = 'last_updated';
        if (empty($orderDir))
            $orderDir = 'desc';

        $tbAlias = 'ch';
        $table = 'event';
        $whereSql = ' WHERE deleted=0 ';

        $sqlData = array();
        //total count sql
        $countSql = 'SELECT count(*) FROM ' . $table . ' ' . $tbAlias . $whereSql;
        //get pagination infor
        $paginInfor = getPageInfor($countSql, $sqlData, $perPage, $page);
        $pageCount = $paginInfor['pageCount'];
        $nextPage = $paginInfor['nextPage'];
        $page = $paginInfor['page'];
        //get limit sql
        $limit = getLimitSql($page, $perPage);

        //get all messages
        $sql = 'SELECT ' . $tbAlias . '.*';
        $sql .= ' FROM ' . $table . ' ' . $tbAlias;
        $sql .= $whereSql;
        $sql .= ' order by ' . $tbAlias . '.' . $orderBy . ' ' . $orderDir . $limit;

        $events = $this->db->query($sql, $sqlData);
        $finalMarkup = '';
        if ($this->db->RowCount() > 0) {
            foreach ($events as $event) {
                $edit = $this->ui->eventEditBtn($event['id'], $page);
                $delLink = $this->ui->eventDeleteBtn($event['id'], $page);
                $notificationBtn = $this->ui->eventNotificationBtn($event['id']);
                $userBtn = $this->ui->eventUserBtn($event['id']);

                $lastUpdatedLocal = toLocalDateTime($event['last_updated'], $phpTzIdentifier, 'M d, Y  H:i:s');
                $finalMarkup .=$this->ui->eventRow($event, $lastUpdatedLocal, $edit, $delLink, $notificationBtn, $userBtn, $phpTzIdentifier);
            }
        } else {
            //no messages
            $finalMarkup .=$this->ui->noEventRow();
        }

//        return $finalMarkup;
        return array('markup' => $finalMarkup, 'pageCount' => $pageCount, 'nextPage' => $nextPage, 'page' => $page);
    }

    /**
     * add/edit/delete events
     * 
     * $mode:add|edit|delete|editpop
     */
    public function events($mode, $iData = array()) {

        switch ($mode) {
            case 'add':
                $this->res = $this->validateEvent($iData);
                if ($this->res['status'] == false) {
                    //we have error so return
                    return $this->res;
                }

                //set publish                
                $publish = $this->getEventPublish($iData);


                $sql = 'INSERT INTO 
                        event(name,type,start_date,end_date,apply_deadline,description,publish,created,last_updated) 
                        VALUES(:name,:type,:start_date,:end_date,:apply_deadline,:description,:publish,NOW(),NOW())';
                $data = array(
                    'name' => trim($iData['name']), 'type' => $iData['type'],
                    'start_date' => mysqlDate($iData['start']), 'end_date' => mysqlDate($iData['end']),
                    'apply_deadline' => mysqlDate($iData['deadline']), 'description' => trim($iData['description']),
                    'publish' => $publish
                );
                $this->db->query($sql, $data);
                logAction('add', 'event', 'add event', 'admin.class.php , event ' . trim($iData['name']) . '(' . $this->db->insertId() . ') added by user : ' . $this->userId, true, $this->api, $this->userId);
                //get latest list
                $options = array();
                if (!empty($iData['options']))
                    $options = $iData['options'];

                $finalMarkup = $this->eventsSelction($iData['phpTzIdentifier'], $options, true);

                $this->res['adcnt'] = $finalMarkup;
                $this->res['text'] = '';
                $this->res['status'] = true;

                return $this->res;

                break;
            case 'edit':
                if (empty($iData['uid'])) {
                    $error_text = 'Sorry! Invalid request.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }

                $this->res = $this->validateEvent($iData);
                if ($this->res['status'] == false) {
                    //we have error so return
                    return $this->res;
                }

                //set publish                
                $publish = $this->getEventPublish($iData);

                $sql = 'UPDATE event set 
                        name=:name,type=:type,start_date=:start_date,end_date=:end_date,
                        apply_deadline=:apply_deadline,description=:description,publish=:publish,last_updated=NOW() 
                        WHERE id=:id';
                $data = array(
                    'name' => trim($iData['name']), 'type' => $iData['type'],
                    'start_date' => mysqlDate($iData['start']), 'end_date' => mysqlDate($iData['end']),
                    'apply_deadline' => mysqlDate($iData['deadline']), 'description' => trim($iData['description']),
                    'publish' => $publish, 'id' => $iData['uid']
                );

                $this->db->query($sql, $data);
                logAction('edit', 'event', 'edit event', 'admin.class.php , event id=' . $iData['uid'] . ' by user : ' . $this->userId, true, $this->api, $this->userId);
                //get latest list
                $options = array();
                if (!empty($iData['options']))
                    $options = $iData['options'];

                $finalMarkup = $this->eventsSelction($iData['phpTzIdentifier'], $options, true);

                $this->res['adcnt'] = $finalMarkup;
                $this->res['text'] = '';
                $this->res['status'] = true;

                return $this->res;

                break;
            case 'delete':
                if (empty($iData['uid'])) {
                    $error_text = 'Sorry! Invalid request.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }

                //we do not delete at once sicne user may have used this event, 
                //this will be deleted by cron job later so if something went wrong admin can get help from dev team to restore
                $sql = 'update event set deleted=1,last_updated=NOW() WHERE id=:id';
                $data = array('id' => $iData['uid']);
                $this->db->query($sql, $data);
                logAction('delete', 'events', 'delete events', 'admin.class.php , events id=' . $iData['uid'] . ' delete by user : ' . $this->userId, true, $this->api, $this->userId);

                //get latest list
                $options = array();
                if (!empty($iData['options']))
                    $options = $iData['options'];

                $finalMarkup = $this->eventsSelction($iData['phpTzIdentifier'], $options, true);

                $this->res['adcnt'] = $finalMarkup;
                $this->res['text'] = '';
                $this->res['status'] = true;

                return $this->res;

                break;
            case 'editPop':
                //if challege id or notification id not passed invalid request
                if (empty($iData['uid'])) {
                    $error_text = 'Sorry! Invalid request.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }
                //get event data
                $event = $this->getEvent($iData['uid']);

                //get latest list
                $options = array();
                if (!empty($iData['options']))
                    $options = $iData['options'];

                $finalMarkup = $this->ui->eventsDialogs('edit', false, $event, $options);

                $this->res['admEEpop'] = $finalMarkup;
                $this->res['text'] = '';
                $this->res['status'] = true;

                return $this->res;

                break;
        }
    }

    /**
     * get Event publish value from input data
     * 
     * @param array $iData
     * @return int 1 or 0
     */
    private function getEventPublish($iData) {
        $publish = 0;
        if (!empty($iData['publish']) && $iData['publish'] == 1)
            $publish = 1;

        return $publish;
    }

    /**
     * validate event data
     * 
     * @param array $iData
     * @return array
     */
    private function validateEvent($iData) {
        if (empty($iData['name']) || trim($iData['name']) == '') {
            $error_text = 'Sorry! Event name cannot be empty.';
            $this->res['status'] = false;
            $this->res['error'] = $error_text;
            return $this->res;
        } else if (empty($iData['type']) || trim($iData['type']) == '') {
            $error_text = 'Sorry! Event type cannot be empty.';
            $this->res['status'] = false;
            $this->res['error'] = $error_text;
            return $this->res;
        } else if (empty($iData['start']) || trim($iData['start']) == '') {
            $error_text = 'Sorry! Event start date cannot be empty.';
            $this->res['status'] = false;
            $this->res['error'] = $error_text;
            return $this->res;
        } else if (empty($iData['end']) || trim($iData['end']) == '') {
            $error_text = 'Sorry! Event end date cannot be empty.';
            $this->res['status'] = false;
            $this->res['error'] = $error_text;
            return $this->res;
        } else if (empty($iData['description']) || trim($iData['description']) == '') {
            $error_text = 'Sorry! Event description cannot be empty.';
            $this->res['status'] = false;
            $this->res['error'] = $error_text;
            return $this->res;
        }

        $this->res['status'] = true;
        return $this->res;
    }

    /**
     * get per page and page number from options
     * 
     * @param array $options
     * @return array
     */
    private function pageSettingsFromOptions($options) {
        $perPage = 0;
        $page = 1;
        if (!empty($options['perPage']))
            $perPage = intval($options['perPage']);
        if (!empty($options['page']))
            $page = intval($options['page']);
        return array('page' => $page, 'perPage' => $perPage);
    }

    /**
     * event notifications list
     * 
     * @param int $eventId event id
     * @param array $options array options
     * @param array $event challange data
     */
    private function eventNotificationsList($eventId, $phpTzIdentifier = 'UTC', $options = array(), $markup = false, $event = array()) {
        //get per page and page number from options
        $pageSettings = $this->pageSettingsFromOptions($options);

        //get per page 
        $perPage = getPerPage($pageSettings['perPage']);
        //page requested
        $pageR = $pageSettings['page'];

        //fields we show in table
        $fields = array('title' => 'text', 'publish' => 'other', 'last_updated' => 'other');
        $fieldNames = array('title' => 'Title', 'publish' => 'Publish', 'last_updated' => 'Last Updated');
        //get order by 
        $orderByRes = $this->getOrderByFromOptions($options, $fields, 'last_updated', 'desc');
        //set ordery by from options
        $orderByField = $orderByRes['orderByField'];
        $orderDir = $orderByRes['orderDir'];

        $eventsNotificationsRes = $this->eventNotificationsRows($eventId, $phpTzIdentifier, $orderByField, $orderDir, $pageR, $perPage);
        $eventsNotificationsRows = $eventsNotificationsRes['markup'];
        $pageCount = $eventsNotificationsRes['pageCount'];
//                $nextPage = $eventsNotificationsRes['nextPage'];
        //page we can return after validation etc
        $page = $eventsNotificationsRes['page'];

        //except order by object string {} other parts of onclick funcitons
        $onclickS = 'eventNotifications(\'list\',' . $eventId . ',0,';
        $onclickE = ');';

        $columnHeadrs = $this->getFieldsTableColumnHeadrs($fields, $fieldNames, $orderByField, $orderDir, $onclickS, $onclickE, 'evtn_', $page);
        //get event data if event data not passed
        if (empty($event))
            $event = $this->getEvent($eventId);

        $finalMarkup = $this->ui->eventNotificationsList($event, $columnHeadrs, $eventsNotificationsRows, $page, $pageCount, $perPage, $onclickS, $onclickE, 'eventNotifications');

        if ($markup)
            return $finalMarkup;
        else {
            $this->res['adcnt'] = $finalMarkup;
            $this->res['text'] = '';
            $this->res['status'] = true;
            return $this->res;
        }
    }

    /**
     * return event notifications rows
     * 
     * @param int $eventId event id
     */
    private function eventNotificationsRows($eventId, $phpTzIdentifier = 'UTC', $orderBy = 'last_updated', $orderDir = 'desc', $page = 1, $perPage = ROWS_PER_PAGE) {
        //make sure order by has
        if (empty($orderBy))
            $orderBy = 'last_updated';
        if (empty($orderDir))
            $orderDir = 'desc';

        $tbAlias = 'en';
        $table = 'event_notification';
        $whereSql = ' WHERE en.deleted=0 AND en.event_id=:event_id';

        $sqlData = array('event_id' => $eventId);
        //total count sql
        $countSql = 'SELECT count(*) FROM ' . $table . ' ' . $tbAlias . $whereSql;
        //get pagination infor
        $paginInfor = getPageInfor($countSql, $sqlData, $perPage, $page);
        $pageCount = $paginInfor['pageCount'];
        $nextPage = $paginInfor['nextPage'];
        $page = $paginInfor['page'];
        //get limit sql
        $limit = getLimitSql($page, $perPage);

        //get all messages
        $sql = 'SELECT ' . $tbAlias . '.*';
        $sql .= ' FROM ' . $table . ' ' . $tbAlias;
        $sql .= $whereSql;
        $sql .= ' order by ' . $tbAlias . '.' . $orderBy . ' ' . $orderDir . $limit;

        $notifications = $this->db->query($sql, $sqlData);
        $finalMarkup = '';
        if ($this->db->RowCount() > 0) {
            foreach ($notifications as $notification) {
                $edit = $this->ui->eventNotificationsEditBtn($eventId, $notification['id'], $page);
                $delLink = $this->ui->eventNotificationsDeleteBtn($eventId, $notification['id'], $page);

                $lastUpdatedLocal = toLocalDateTime($notification['last_updated'], $phpTzIdentifier, 'M d, Y  H:i:s');
                $finalMarkup .=$this->ui->eventNotificationsRow($notification, $lastUpdatedLocal, $edit, $delLink);
            }
        } else {
            //no event notifications
            $finalMarkup .=$this->ui->noEventNotificationsRow();
        }

//        return $finalMarkup;
        return array('markup' => $finalMarkup, 'pageCount' => $pageCount, 'nextPage' => $nextPage, 'page' => $page);
    }

    /**
     * add/edit/delete/list event notifications
     * 
     * @param string $mode add|edit|delete|list
     */
    public function eventNotifications($mode, $iData = array()) {

        switch ($mode) {
            case 'list':
                //if challege id not passed invalid request
                if (empty($iData['eventId'])) {
                    $error_text = 'Sorry! Invalid request.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }

                //get latest list
                $options = array();
                if (!empty($iData['options']))
                    $options = $iData['options'];

                $finalMarkup = $this->eventNotificationsList($iData['eventId'], $iData['phpTzIdentifier'], $options, true);

                $this->res['admEnCnt'] = $finalMarkup;
                $this->res['text'] = '';
                $this->res['status'] = true;

                return $this->res;

                break;
            case 'add':
                //get event data
                $event = $this->getEvent($iData['eventId']);
                //validate event notification
                $this->res = $this->validateEventNotifications($iData, $event);
                if ($this->res['status'] == false) {
                    //we have error so return
                    return $this->res;
                }

                //set publish                
                $publish = $this->getEventNotificationsPublish($iData);


                $sql = 'INSERT INTO event_notification(event_id,title,publish,text,created,last_updated) 
                        VALUES(:event_id,:title,:publish,:text,NOW(),NOW())';
                $data = array(
                    'event_id' => $event['id'], 'title' => trim($iData['title']),
                    'publish' => $publish, 'text' => $iData['text']
                );
                $this->db->query($sql, $data);
                logAction('add', 'event notifications', 'add event notifications', 'admin.class.php , event notifications ' . trim($iData['title']) . '(' . $this->db->insertId() . ') added by user : ' . $this->userId, true, $this->api, $this->userId);
                //get latest list
                $options = array();
                if (!empty($iData['options']))
                    $options = $iData['options'];

                $finalMarkup = $this->eventNotificationsList($event['id'], $iData['phpTzIdentifier'], $options, true, $event);
                //sendEventNotification email if $publish=1
                if ($publish == '1')
                    $this->sendEventNotificationEmail($event, $data, $mode);

                $this->res['admEnCnt'] = $finalMarkup;
                $this->res['text'] = '';
                $this->res['status'] = true;

                return $this->res;

                break;
            case 'edit':
                if (empty($iData['uid'])) {
                    $error_text = 'Sorry! Invalid request.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }

                //get event data
                $event = $this->getEvent($iData['eventId']);

                //validate event notification
                $this->res = $this->validateEventNotifications($iData, $event);
                if ($this->res['status'] == false) {
                    //we have error so return
                    return $this->res;
                }

                //set publish                
                $publish = $this->getEventNotificationsPublish($iData);

                $sql = 'UPDATE event_notification set 
                        title=:title,publish=:publish,text=:text,last_updated=NOW() 
                        WHERE id=:id AND event_id=:event_id';

                $data = array(
                    'event_id' => $event['id'], 'title' => trim($iData['title']),
                    'publish' => $publish, 'text' => $iData['text'],
                    'id' => $iData['uid']
                );
                $this->db->query($sql, $data);
                logAction('edit', 'event notification', 'edit event notification', 'admin.class.php , event notification id=' . $iData['uid'] . ' by user : ' . $this->userId, true, $this->api, $this->userId);
                //get latest list
                $options = array();
                if (!empty($iData['options']))
                    $options = $iData['options'];

                $finalMarkup = $this->eventNotificationsList($event['id'], $iData['phpTzIdentifier'], $options, true, $event);
                //sendEventNotification email if $publish=1
                if ($publish == '1')
                    $this->sendEventNotificationEmail($event, $data, $mode);

                $this->res['admEnCnt'] = $finalMarkup;
                $this->res['text'] = '';
                $this->res['status'] = true;

                return $this->res;

                break;
            case 'delete':
                if (empty($iData['uid'])) {
                    $error_text = 'Sorry! Invalid request.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }

                //we do not delete at once sicne user may have used this event, 
                //this will be deleted by cron job later so if something went wrong admin can get help from dev team to restore
                $sql = 'update event_notification set deleted=1,last_updated=NOW() WHERE id=:id AND event_id=:event_id';
                $data = array('id' => $iData['uid'], 'event_id' => $iData['eventId']);
                $this->db->query($sql, $data);
                logAction('delete', 'event notification', 'delete event notification', 'admin.class.php , event notification id=' . $iData['uid'] . ' delete by user : ' . $this->userId, true, $this->api, $this->userId);

                //get latest list
                $options = array();
                if (!empty($iData['options']))
                    $options = $iData['options'];

                $finalMarkup = $this->eventNotificationsList($iData['eventId'], $iData['phpTzIdentifier'], $options, true);

                $this->res['admEnCnt'] = $finalMarkup;
                $this->res['text'] = '';
                $this->res['status'] = true;

                return $this->res;

                break;
            case 'editPop':
                //if challege id or notification id not passed invalid request
                if (empty($iData['eventId'])) {
                    $error_text = 'Sorry! Invalid request.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                } else if (empty($iData['uid'])) {
                    $error_text = 'Sorry! Invalid request.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }
                //get event data
                $event = $this->getEvent($iData['eventId']);
                //get event notification 
                $notification = $this->getEventNotification($iData['eventId'], $iData['uid']);
                //get latest list
                $options = array();
                if (!empty($iData['options']))
                    $options = $iData['options'];

                $finalMarkup = $this->ui->eventNotificationsDialogs($event, 'edit', false, $notification, $options);

                $this->res['admENpop'] = $finalMarkup;
                $this->res['text'] = '';
                $this->res['status'] = true;

                return $this->res;

                break;
        }
    }

    /**
     * get event Notifications publish value from input data
     * 
     * @param array $iData
     * @return int 1 or 0
     */
    private function getEventNotificationsPublish($iData) {
        $publish = 0;
        if (!empty($iData['publish']) && $iData['publish'] == 1)
            $publish = 1;

        return $publish;
    }

    /**
     * get event Notifications Comments Enabled value from input data
     * 
     * @param array $iData
     * @return int 1 or 0
     */
    private function getEventNotificationsCommentsEnabled($iData) {
        $enabled = 0;
        if (!empty($iData['comments']) && $iData['comments'] == 1)
            $enabled = 1;

        return $enabled;
    }

    /**
     * validate event notifications data
     * 
     * @param array $iData
     * @param array $event challange data
     * @return array
     */
    private function validateEventNotifications($iData, $event) {

        if (empty($iData['title']) || trim($iData['title']) == '') {
            $error_text = 'Sorry! Notification title cannot be empty.';
            $this->res['status'] = false;
            $this->res['error'] = $error_text;
            return $this->res;
        } else if (empty($iData['text']) || trim($iData['text']) == '') {
            $error_text = 'Sorry! Notification text cannot be empty.';
            $this->res['status'] = false;
            $this->res['error'] = $error_text;
            return $this->res;
        }

        $this->res['status'] = true;
        return $this->res;
    }

    /**
     * return event data
     * 
     * @param int $eventId event id
     * @return array
     */
    private function getEvent($eventId) {
        $tbAlias = 'ev';
        $table = 'event';
        $whereSql = ' WHERE ev.deleted=0 AND ev.id=:id';

        $sqlData = array('id' => $eventId);
        $sql = 'SELECT ' . $tbAlias . '.*';
        $sql .= ' FROM ' . $table . ' ' . $tbAlias;
        $sql .= $whereSql;
        $sql .= ' LIMIT 1';

        $event = $this->db->row($sql, $sqlData);
        if (!empty($event))
            return $event;
        else
            return array();
    }

    /**
     * return event data
     * 
     * @param int $eventId event id
     * @param int $notificationId notification id 
     * @return array
     */
    private function getEventNotification($eventId, $notificationId) {
        $tbAlias = 'chp';
        $table = 'event_notification';
        $whereSql = ' WHERE chp.deleted=0 AND chp.id=:id AND chp.event_id=:event_id';

        $sqlData = array('id' => $notificationId, 'event_id' => $eventId);
        $sql = 'SELECT ' . $tbAlias . '.*';
        $sql .= ' FROM ' . $table . ' ' . $tbAlias;
        $sql .= $whereSql;
        $sql .= ' LIMIT 1';

        $notification = $this->db->row($sql, $sqlData);
        if (!empty($notification))
            return $notification;
        else
            return array();
    }

    /**
     * send event notification users joined for event
     * @param array $event event data
     * @param array $data notification data
     * @param string $mode add|edit
     */
    private function sendEventNotificationEmail($event, $data, $mode) {
        //get execution time unlimited ,don't let the script time out 
        set_time_limit(0);

        $this->setUserClass();
        //get users list joined for this event
        $evtUsers = $this->usersJoinedToEvent($event['id']);
        foreach ($evtUsers as $evtUser) {
            $user = $this->user->getUser($evtUser['user_id'], array('fname', 'lname', 'username', 'email'));
            $contactName = $this->ui->getDispalyName($user['fname'], $user['lname'], $user['username'], $user['email']);

            //for user
            $subject = SITE_NAME . ' Event - ' . $this->ui->et($event['name']);
//            if ($mode == 'edit')
//                $subject.=' :update';
            //get email Content
            $wembody = $this->ui->eventNotificationEmailContent($subject, $event, $data, $mode, $contactName);
            sendEmail($user['email'], $subject, $wembody, $this->api, $this->userId);
        }
    }

    /**
     * return users list joined for event
     * 
     * @param int $eventId
     * @return array 
     */
    private function usersJoinedToEvent($eventId) {
        $tbAlias = 'ue';
        $table = 'user_event';
        $whereSql = ' WHERE ' . $tbAlias . '.event_id=:event_id';

        $sqlData = array('event_id' => $eventId);
        //total count sql
        $sql = 'SELECT user_id FROM ' . $table . ' ' . $tbAlias . $whereSql;
        $users = $this->db->query($sql, $sqlData);
        if (!empty($users))
            return $users;
        else
            return array();
    }

    /**
     * list event users
     * 
     * @param string $mode list
     */
    public function eventUsers($mode, $iData = array()) {

        switch ($mode) {
            case 'list':
                //if challege id not passed invalid request
                if (empty($iData['eventId'])) {
                    $error_text = 'Sorry! Invalid request.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }

                //get latest list
                $options = array();
                if (!empty($iData['options']))
                    $options = $iData['options'];

                $finalMarkup = $this->eventUsersList($iData['eventId'], $iData['phpTzIdentifier'], $options, true);

                $this->res['admEuCnt'] = $finalMarkup;
                $this->res['text'] = '';
                $this->res['status'] = true;

                return $this->res;

                break;
        }
    }

    /**
     * event users list
     * 
     * @param int $eventId event id
     * @param array $options array options
     * @param array $event challange data
     */
    private function eventUsersList($eventId, $phpTzIdentifier = 'UTC', $options = array(), $markup = false, $event = array()) {
        //get per page and page number from options
        $pageSettings = $this->pageSettingsFromOptions($options);

        //get per page 
        $perPage = getPerPage($pageSettings['perPage']);
        //page requested
        $pageR = $pageSettings['page'];

        //fields we show in table
        $fields = array('name' => 'text', 'email' => 'text', 'username' => 'text', 'grade' => 'number', 'school_name' => 'text', 'joined' => 'other');
        $fieldNames = array('name' => 'Name', 'email' => 'Email', 'username' => 'Username', 'grade' => 'Grade', 'school_name' => 'School', 'joined' => 'Joined');
        //get order by 
        $orderByRes = $this->getOrderByFromOptions($options, $fields, 'joined', 'desc');
        //set ordery by from options
        $orderByField = $orderByRes['orderByField'];
        $orderDir = $orderByRes['orderDir'];

        $eventsUsersRes = $this->eventUsersRows($eventId, $phpTzIdentifier, $orderByField, $orderDir, $pageR, $perPage);
        $eventsUsersRows = $eventsUsersRes['markup'];
        $pageCount = $eventsUsersRes['pageCount'];
//                $nextPage = $eventsUsersRes['nextPage'];
        //page we can return after validation etc
        $page = $eventsUsersRes['page'];

        //except order by object string {} other parts of onclick funcitons
        $onclickS = 'eventUsers(\'list\',' . $eventId . ',0,';
        $onclickE = ');';

        $columnHeadrs = $this->getFieldsTableColumnHeadrs($fields, $fieldNames, $orderByField, $orderDir, $onclickS, $onclickE, 'evtu_', $page);
        //get event data if event data not passed
        if (empty($event))
            $event = $this->getEvent($eventId);

        $finalMarkup = $this->ui->eventUsersList($event, $columnHeadrs, $eventsUsersRows, $page, $pageCount, $perPage, $onclickS, $onclickE, 'eventUsers');

        if ($markup)
            return $finalMarkup;
        else {
            $this->res['adcnt'] = $finalMarkup;
            $this->res['text'] = '';
            $this->res['status'] = true;
            return $this->res;
        }
    }

    /**
     * return event users rows
     * 
     * @param int $eventId event id
     */
    private function eventUsersRows($eventId, $phpTzIdentifier = 'UTC', $orderBy = 'joined', $orderDir = 'desc', $page = 1, $perPage = ROWS_PER_PAGE) {
        //make sure order by has
        if (empty($orderBy))
            $orderBy = 'joined';
        if (empty($orderDir))
            $orderDir = 'desc';

        $tbAlias = 'ue';
        $table = 'user_event';
        $lefJoinTbAlias1 = 'u';
        $lefJoinTable1 = 'user';
        $lefJoinTbAlias2 = 's';
        $lefJoinTable2 = 'school';

        $whereSql = ' WHERE ' . $tbAlias . '.event_id=:event_id 
                    AND ' . $lefJoinTbAlias1 . '.status="active" AND ' . $lefJoinTbAlias1 . '.user_type="user"';

        $leftJoin1 = ' LEFT JOIN ' . $lefJoinTable1 . ' ' . $lefJoinTbAlias1;
        $leftJoin1.=' ON ' . $tbAlias . '.user_id=' . $lefJoinTbAlias1 . '.id';

        //fro school
        $leftJoin2 = ' LEFT JOIN ' . $lefJoinTable2 . ' ' . $lefJoinTbAlias2;
        $leftJoin2.=' ON ' . $lefJoinTbAlias1 . '.school_id=' . $lefJoinTbAlias2 . '.id';

        $sqlData = array('event_id' => $eventId);
        //total count sql
        $countSql = 'SELECT count(*) FROM ' . $table . ' ' . $tbAlias . $leftJoin1 . $leftJoin2 . $whereSql;
        //get pagination infor
        $paginInfor = getPageInfor($countSql, $sqlData, $perPage, $page);
        $pageCount = $paginInfor['pageCount'];
        $nextPage = $paginInfor['nextPage'];
        $page = $paginInfor['page'];
        //get limit sql
        $limit = getLimitSql($page, $perPage);

        //get all messages
        $sql = 'SELECT ';
        $sql .= $tbAlias . '.created as joined,';
        $sql .= $lefJoinTbAlias2 . '.name as school_name,';
        $sql .= 'concat(' . $lefJoinTbAlias1 . '.fname," ",' . $lefJoinTbAlias1 . '.lname) as name,';
        $sql .= $lefJoinTbAlias1 . '.*';
        $sql .= ' FROM ' . $table . ' ' . $tbAlias;
        $sql .= $leftJoin1;
        $sql .= $leftJoin2;
        $sql .= $whereSql;
        $sql .= ' order by ' . $orderBy . ' ' . $orderDir . $limit;

        $users = $this->db->query($sql, $sqlData);
        $finalMarkup = '';
        if ($this->db->RowCount() > 0) {
            foreach ($users as $user) {
                $finalMarkup .=$this->ui->eventUsersRow($user, $phpTzIdentifier);
            }
        } else {
            //no event users
            $finalMarkup .=$this->ui->noEventUsersRow();
        }

        return array('markup' => $finalMarkup, 'pageCount' => $pageCount, 'nextPage' => $nextPage, 'page' => $page);
    }

}
?>