<?php

/**
 * all functions related to user event
 */
class event {

    private $res = array('status' => true, 'text' => '', 'error' => '');
    private $userId;
    private $user;
    private $ui;

    /**
     * set true if calls is from api
     * @var bool 
     */
    private $api = false;

    public function __construct($userId = 0, $api = false) {
        $this->db = new DB(DBCONN, DBUSER, DBPASS);
        $this->userId = $userId;
        $this->api = $api;

        require_once "ui/ui.event.class.php";
        $this->ui = new uiEvent();
    }

    /**
     * set user class to $this->user
     */
    private function setUserClass() {
        require_once "logic/user.class.php";
        $this->user = new user($this->userId, $this->api);
    }

    /**
     * at school page list events  , join users to event, leave users from events
     * 
     * @param string $mode list|join|leave|notificationPop|notification
     */
    public function schoolEvents($mode, $iData = array()) {

        switch ($mode) {
            case 'list':
                //get latest list
                $options = array();
                if (!empty($iData['options']))
                    $options = $iData['options'];

                $finalMarkup = $this->schoolEventsList($iData['phpTzIdentifier'], $options, true);

                $this->res['schEvtCnt'] = $finalMarkup;
                $this->res['text'] = '';
                $this->res['status'] = true;

                return $this->res;

                break;
            case 'join':
                if (empty($iData['eventId'])) {
                    $error_text = 'Sorry! Invalid request.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }
                //get event data
                $event = $this->getEvent($iData['eventId']);
                if (empty($event)) {
                    $error_text = 'Sorry! Invalid request.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }


                //make sure apply dedline is not past date
                $todayLocal = getLocalToday($iData['phpTzIdentifier']);
                if (strtotime($event['apply_deadline']) < strtotime($todayLocal)) {
                    $error_text = 'Sorry! Apply deadline has been exceeded.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }
                //validate user has already joined
                if ($this->hasUserJoinedEvent($event['id'])) {
                    $error_text = 'Sorry! You have already joined  for this event.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }

                $sql = 'INSERT INTO user_event(event_id,user_id,created) 
                        VALUES(:event_id,:user_id,NOW())';
                $data = array('event_id' => $event['id'], 'user_id' => $this->userId);
                $this->db->query($sql, $data);
                logAction('add', 'user events', 'add  user events', 'admin.class.php , add user events eventId:' . $event['id'] . '(' . $this->db->insertId() . ') added by user : ' . $this->userId, true, $this->api, $this->userId);
                //get latest list
                $options = array();
                if (!empty($iData['options']))
                    $options = $iData['options'];

                $finalMarkup = $this->schoolEventsList($iData['phpTzIdentifier'], $options, true);
                //send event joined email to admin and user
                $this->sendEventEmail($event, $mode,$iData['phpTzIdentifier']);

                $this->res['schEvtCnt'] = $finalMarkup;
                $this->res['text'] = '';
                $this->res['status'] = true;

                return $this->res;

                break;
            case 'leave':
                if (empty($iData['uid'])) {
                    $error_text = 'Sorry! Invalid request.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }
                if (empty($iData['eventId'])) {
                    $error_text = 'Sorry! Invalid request.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }
                //get event data
                $event = $this->getEvent($iData['eventId']);
                if (empty($event)) {
                    $error_text = 'Sorry! Invalid request.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }
                //validate user has already joined
                if (!$this->hasUserJoinedEvent($event['id'])) {
                    $error_text = 'Sorry! You have haven\'t joined  for this event .';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }

                //no need to have event_id in sql but had for more security 
                $sql = 'delete from  user_event WHERE id=:id AND event_id=:event_id AND user_id=:user_id';
                $data = array('id' => $iData['uid'], 'event_id' => $event['id'], 'user_id' => $this->userId);
                $this->db->query($sql, $data);
                logAction('delete', 'user events', 'delete user events', 'admin.class.php , delete user events id=' . $iData['uid'] . ' delete by user : ' . $this->userId, true, $this->api, $this->userId);

                //get latest list
                $options = array();
                if (!empty($iData['options']))
                    $options = $iData['options'];

                $finalMarkup = $this->schoolEventsList($iData['phpTzIdentifier'], $options, true);

                //send event leave email to admin and user
                $this->sendEventEmail($event, $mode,$iData['phpTzIdentifier']);

                $this->res['schEvtCnt'] = $finalMarkup;
                $this->res['text'] = '';
                $this->res['status'] = true;

                return $this->res;

                break;
            case 'notificationPop':
                //get nofificaiton content                
                $notificationRes=$this->schoolEvents('notification', $iData);
                //if retuned error
                if($notificationRes['status']==false){
                    $this->res=$notificationRes;
                    return $this->res;
                }
                //get event data
                $event = $this->getEvent($iData['eventId']);
                
                //get latest option
                $options = array();
                if (!empty($iData['options']))
                    $options = $iData['options'];

                $finalMarkup = $this->eventNotificationsDialogs($event, false, $notificationRes['schEvtNotCnt'], $options,$iData['phpTzIdentifier']);

                $this->res['schEvtNotCntpop'] = $finalMarkup;
                $this->res['text'] = '';
                $this->res['status'] = true;

                return $this->res;
                break;
            case 'notification':
                //if challege id not passed invalid request
                if (empty($iData['eventId'])) {
                    $error_text = 'Sorry! Invalid request.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }
                
                //get event data
                $event = $this->getEvent($iData['eventId']);
                if (empty($event)) {
                    $error_text = 'Sorry! Invalid request.';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }
                
                //validate user has already joined
                if (!$this->hasUserJoinedEvent($iData['eventId'])) {
                    $error_text = 'Sorry! You have haven\'t joined  for this event .';
                    $this->res['status'] = false;
                    $this->res['error'] = $error_text;
                    return $this->res;
                }

                //get latest list
                $options = array();
                if (!empty($iData['options']))
                    $options = $iData['options'];

                $finalMarkup = $this->eventNotificationsList($iData['eventId'], $iData['phpTzIdentifier'], $options, true,$event);

                $this->res['schEvtNotCnt'] = $finalMarkup;
                $this->res['text'] = '';
                $this->res['status'] = true;

                return $this->res;

                break;
        }
    }

    /**
     * event evens list
     * 
     * @param array $options array options
     * @param array $event challange data
     */
    private function schoolEventsList($phpTzIdentifier = 'UTC', $options = array(), $markup = false) {
        //get per page and page number from options
        $pageSettings = $this->pageSettingsFromOptions($options);

        //get per page 
        $perPage = getPerPage($pageSettings['perPage']);
        //page requested
        $pageR = $pageSettings['page'];

        //fields we show in table
//        $fields = array('title' => 'text', 'publish' => 'other', 'last_updated' => 'other');
//        $fieldNames = array('title' => 'Title', 'publish' => 'Publish', 'last_updated' => 'Last Updated');
//        //get order by 
//        $orderByRes = $this->getOrderByFromOptions($options, $fields, 'last_updated', 'desc');
        //set ordery by from options
        $orderByField = 'last_updated';
        $orderDir = 'desc';

        $eventsRes = $this->schoolEventsRows($phpTzIdentifier, $orderByField, $orderDir, $pageR, $perPage, $options);
        $eventsRows = $eventsRes['markup'];
        $pageCount = $eventsRes['pageCount'];
//                $nextPage = $eventsEventsRes['nextPage'];
        //page we can return after validation etc
        $page = $eventsRes['page'];

        //except order by object string {} other parts of onclick funcitons
        $onclickS = 'schoolEvents(\'list\',0,0,';
        $onclickE = ');';

//        $columnHeadrs = $this->getFieldsTableColumnHeadrs($fields, $fieldNames, $orderByField, $orderDir, $onclickS, $onclickE, 'evtn_', $page);
        //get event data if event data not passed
//        if (empty($event))
//            $event = $this->getEvent($eventId);

        $finalMarkup = $this->ui->schoolEventsList($eventsRows, $page, $pageCount, $perPage, $onclickS, $onclickE, 'schoolEvents', $options);

        if ($markup)
            return $finalMarkup;
        else {
            $this->res['schEvtCnt'] = $finalMarkup;
            $this->res['text'] = '';
            $this->res['status'] = true;
            return $this->res;
        }
    }

    /**
     * return event events rows, only pulished events are retuned
     * 
     * @param int $eventId event id
     */
    private function schoolEventsRows($phpTzIdentifier = 'UTC', $orderBy = 'last_updated', $orderDir = 'desc', $page = 1, $perPage = ROWS_PER_PAGE, $options = array()) {
        //make sure order by has
        if (empty($orderBy))
            $orderBy = 'last_updated';
        if (empty($orderDir))
            $orderDir = 'desc';

        $filter = $this->getFiltersFromOptions($options);

        $tbAlias = 'en';
        $table = 'event';
        $lefJoinTbAlias = 'ue';
        $lefJoinTable = 'user_event';
        $sqlData = array();
        //get where condition
        $whereSqlRes = $this->schoolEventsWhere($tbAlias, $lefJoinTbAlias, $filter);
        $whereSql = $whereSqlRes['where'];
        //merge data array with wehre data
        $sqlData = array_merge($sqlData, $whereSqlRes['data']);

        $leftJoin = ' LEFT JOIN ' . $lefJoinTable . ' ' . $lefJoinTbAlias;
        $leftJoin.=' ON ' . $tbAlias . '.id=' . $lefJoinTbAlias . '.event_id';
        $leftJoin.=' AND ' . $lefJoinTbAlias . '.user_id=:' . $lefJoinTbAlias . '_user_id_lj';
        $sqlData[$lefJoinTbAlias . '_user_id_lj'] = $this->userId;
        //field from left joined table user_event.id
        $ue_id = ',IFNULL(' . $lefJoinTbAlias . '.id,0) as ue_id';

        //total count sql
        $countSql = 'SELECT count(*) FROM ' . $table . ' ' . $tbAlias . $leftJoin . $whereSql;

        //get pagination infor
        $paginInfor = getPageInfor($countSql, $sqlData, $perPage, $page);
        $pageCount = $paginInfor['pageCount'];
        $nextPage = $paginInfor['nextPage'];
        $page = $paginInfor['page'];
        //get limit sql
        $limit = getLimitSql($page, $perPage);

        //get all messages
        $sql = 'SELECT ' . $tbAlias . '.*' . $ue_id;
        $sql .= ' FROM ' . $table . ' ' . $tbAlias;
        $sql .= $leftJoin;
        $sql .= $whereSql;
        $sql .= ' order by ' . $tbAlias . '.' . $orderBy . ' ' . $orderDir . ',' . $tbAlias . '.id desc' . $limit;

        $events = $this->db->query($sql, $sqlData);
        $finalMarkup = '';
        if ($this->db->RowCount() > 0) {
            $optionsStr = paginationOptionJsonString($options, $page);
            foreach ($events as $event) {
                $leave = $join = $notifBtn = '';
                if (!empty($event['ue_id'])) {
                    $leave = $this->ui->schoolEventLeaveBtn($event['id'], $event['ue_id'], $optionsStr);
                    $notifBtn = $this->ui->schoolEventNotificationBtn($event['id'], $event['ue_id'], $optionsStr);
                } else {
                    $join = $this->ui->schoolEventJoinBtn($event['id'], $optionsStr);
                }
                $lastUpdatedLocal = toLocalDateTime($event['last_updated'], $phpTzIdentifier, 'M d, Y  H:i:s');
                $finalMarkup .=$this->ui->schoolEventsRow($event, $lastUpdatedLocal, $join, $leave, $notifBtn);
            }
        } else {
            //no event events
            $finalMarkup .=$this->ui->noSchoolEventsRow();
        }

//        return $finalMarkup;
        return array('markup' => $finalMarkup, 'pageCount' => $pageCount, 'nextPage' => $nextPage, 'page' => $page);
    }

    /**
     * schoolEventsWhere conidtion 
     * 
     * @param type $tbAlias
     * @param type $lefJoinTbAlias 
     * @param array $filter
     * @return string
     */
    private function schoolEventsWhere($tbAlias, $lefJoinTbAlias, $filter = array()) {
        $sqlData = array();
        $operator = ' AND';
        $whereSql = ' WHERE ' . $tbAlias . '.deleted=0 AND ' . $tbAlias . '.publish=1';
        $i = 0;
        //prefix for data varas for schoolEventsWhere to make unique value with count $i
        $dvu = '_sew';

        if (!empty($filter)) {
            if (!empty($filter['type'])) {
                $whereSql .= $operator;
                $whereSql.=' ' . $tbAlias . '.type=:' . 'type_' . $tbAlias . $dvu . $i;
                $sqlData['type_' . $tbAlias . $dvu . $i] = $filter['type'];
                $i++;
            }

            if (isset($filter['joined']) && $filter['joined'] != '') {
                if ($filter['joined'] == true) {
                    //joined only
                    $whereSql .= $operator;
                    $whereSql.=' ' . 'IFNULL(' . $lefJoinTbAlias . '.id,0)>0';
                } else if ($filter['joined'] == false) {
                    //not joined only
                    $whereSql .= $operator;
                    $whereSql.=' ' . 'IFNULL(' . $lefJoinTbAlias . '.id,0)=0';
                }
                $i++;
            }
        }

        return array('where' => $whereSql, 'data' => $sqlData);
    }

    /**
     * return event data, only active challeges are retuned
     * 
     * @param int $eventId event id
     * @return array
     */
    public function getEvent($eventId) {
        $tbAlias = 'ev';
        $table = 'event';
        $whereSql = ' WHERE ' . $tbAlias . '.deleted=0 AND ' . $tbAlias . '.publish=1 AND ' . $tbAlias . '.id=:id';

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
     * return events as pages, only active events are retuned
     * with this we can get few records instead of getting all
     * 
     * @param string $orderBy
     * @param string $orderDir
     * @param type $page
     * @param type $perPage
     * @return type
     */
    /*
      public function eventPage($orderBy = 'last_updated', $orderDir = 'desc', $page = 1, $perPage = ROWS_PER_PAGE) {
      //make sure order by has
      if (empty($orderBy))
      $orderBy = 'last_updated';
      if (empty($orderDir))
      $orderDir = 'desc';

      $tbAlias = 'ev';
      $table = 'event';
      $whereSql = ' WHERE '.$tbAlias.'.deleted=0  AND '.$tbAlias.'.enabled=1';

      $sqlData = array();
      //total count sql
      $countSql = 'SELECT count(*) FROM ' . $table . ' ' . $tbAlias . $whereSql;
      //get pagination infor
      $paginInfor = getPageInfor($countSql, $sqlData, $perPage, $page);
      $pageCount = $paginInfor['pageCount'];
      $nextPage = $paginInfor['nextPage'];
      $page = $paginInfor['page'];
      $totalCount = $paginInfor['totalCount'];
      //get limit sql
      $limit = getLimitSql($page, $perPage);

      //get all messages
      $sql = 'SELECT ' . $tbAlias . '.*';
      $sql .= ' FROM ' . $table . ' ' . $tbAlias;
      $sql .= $whereSql;
      $sql .= ' order by ' . $tbAlias . '.' . $orderBy . ' ' . $orderDir . $limit;

      $events = array();
      $rows = $this->db->query($sql, $sqlData);

      if ($this->db->RowCount() > 0) {
      $events = $rows;
      }

      return array('events' => $events, 'pageCount' => $pageCount, 'nextPage' => $nextPage, 'page' => $page, 'totalCount' => $totalCount);
      }
     */

    /**
     * return true if there are events admin has activated
     * 
     * @return bool true if there are events admin has activated else false
     */
    public function hasEvents() {
        $tbAlias = 'ev';
        $table = 'event';
        $whereSql = ' WHERE ' . $tbAlias . '.deleted=0  AND ' . $tbAlias . '.enabled=1';

        $sqlData = array();
        //total count sql
        $countSql = 'SELECT count(*) FROM ' . $table . ' ' . $tbAlias . $whereSql;
        $countRs = $this->db->column($countSql, $sqlData);
        $count = intval($countRs[0]);
        if ($count > 0)
            return true;
        else
            return false;
    }

    /**
     * return filters from $options array
     * 
     * @param array $options options
     */
    private function getFiltersFromOptions($options) {
        //if $type set return only event belogs to type ('internships' | 'job shadows' |'volunteering')
        $type = '';
        // if $joined true return only events user has joined, if false return only event user not joined, else return all
        $joined = '';

        if (isset($options['filter'])) {
            //we have option data for filter to set
            //if type set 
            if (isset($options['filter']['type']) && $options['filter']['type'] != '')
                $type = $options['filter']['type'];
            //if joined set to true we set it true else keep default false
            if (isset($options['filter']['joined']) && $options['filter']['joined'] == 'true')
                $joined = true;
            else if (isset($options['filter']['joined']) && $options['filter']['joined'] == 'false')
                $joined = false;
        }

        return array(
            'type' => $type,
            'joined' => $joined
        );
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
     * check if logged in user has joined given event from $eventId
     * 
     * @param int $eventId
     * @return bool true if user has joined and false if not
     */
    private function hasUserJoinedEvent($eventId) {
        $tbAlias = 'ue';
        $table = 'user_event';
        $whereSql = ' WHERE ' . $tbAlias . '.event_id=:event_id  AND ' . $tbAlias . '.user_id=:user_id';

        $sqlData = array('event_id' => $eventId, 'user_id' => $this->userId);
        //total count sql
        $countSql = 'SELECT count(*) FROM ' . $table . ' ' . $tbAlias . $whereSql;
        $countRs = $this->db->column($countSql, $sqlData);
        $count = intval($countRs[0]);
        if ($count > 0)
            return true;
        else
            return false;
    }

    /**
     * send event joined and leave email to user adn admin
     * @param array $event event data
     * @param string $mode join|leave
     */
    private function sendEventEmail($event, $mode,$phpTzIdentifier='UTC') {
        $this->setUserClass();
        $user = $this->user->getUser($this->userId, array('fname', 'lname', 'username', 'email'));
        $contactName = $this->ui->getDispalyName($user['fname'], $user['lname'], $user['username'], $user['email']);

        //for user
        $subject = SITE_NAME . ' Event - ' . $this->ui->et($event['name']);
        //get email Content
        $wembody = $this->ui->eventEmailUserContent($subject, $event, $mode, $contactName,$phpTzIdentifier);
        sendEmail($user['email'], $subject, $wembody, $this->api, $this->userId);

        /*
         * for admin
         */
        $admSbject = SITE_NAME . ' Event - ' . $this->ui->et($event['name']);
        //get email Content
        $admWembody = $this->ui->eventEmailAdminContent($subject, $event, $user, $mode, $contactName,$phpTzIdentifier);
        sendEmail(SUPPORT_EMAIL, $admSbject, $admWembody, $this->api, $this->userId);
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

        //set ordery by from options
        $orderByField = 'last_updated';
        $orderDir = 'desc';

        $eventsNotificationsRes = $this->eventNotificationsRows($eventId, $phpTzIdentifier, $orderByField, $orderDir, $pageR, $perPage);
        $eventsNotificationsRows = $eventsNotificationsRes['markup'];
        $pageCount = $eventsNotificationsRes['pageCount'];
//                $nextPage = $eventsNotificationsRes['nextPage'];
        //page we can return after validation etc
        $page = $eventsNotificationsRes['page'];

        //except order by object string {} other parts of onclick funcitons
        $onclickS = 'schoolEvents(\'notification\',' . $eventId . ',0,';
        $onclickE = ');';

        $finalMarkup = $this->ui->eventNotificationsList($eventsNotificationsRows, $page, $pageCount, $perPage, $onclickS, $onclickE, 'eventNotifications',$options);

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
//                $edit = $this->ui->eventNotificationsEditBtn($eventId, $notification['id'], $page);
//                $delLink = $this->ui->eventNotificationsDeleteBtn($eventId, $notification['id'], $page);

                $lastUpdatedLocal = toLocalDateTime($notification['last_updated'], $phpTzIdentifier, 'M d, Y  H:i:s');
                $finalMarkup .=$this->ui->eventNotificationsRow($notification, $lastUpdatedLocal);
            }
        } else {
            //no event notifications
            $finalMarkup .=$this->ui->noEventNotificationsRow();
        }

//        return $finalMarkup;
        return array('markup' => $finalMarkup, 'pageCount' => $pageCount, 'nextPage' => $nextPage, 'page' => $page);
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
        return $this->ui->eventNotificationsDialogs($event, $placeHolderOnly, $notification, $options, $phpTzIdentifier);
    }


}

?>