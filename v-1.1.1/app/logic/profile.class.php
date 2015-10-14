<?php

require_once "base/profile.base.class.php";

class profile extends profileBase {

    protected $event;

    public function __construct($userId = 0, $api = false) {
        parent::__construct($userId, $api);
    }

    /**
     * set event class to $this->event
     */
    private function setEventClass() {
        require_once "logic/event.class.php";
        $this->event = new event($this->userId, $this->api);
    }

    /**
     * return school section content for logged in user school
     */
    public function schoolSection($markup = false, $reload = false) {
        //get school data
        $school = $this->getSchool();
        //get scholl events bord
        $schoolEventsBord = $this->schoolEventsBord();

        //get school section ui
        $final_markup = $this->ui->schoolSection($school, $schoolEventsBord, $reload);

        if ($markup)
            return $final_markup;
        else {
            $this->res['cnt'] = $final_markup;
            $this->res['text'] = '';
            $this->res['status'] = true;

            return $this->res;
        }
    }

    /**
     * return school data for logged in user
     */
    protected function getSchool() {
        $user = $this->user->getUser($this->userId, array('school_id'));

        $leftJoinLogoPic = ' LEFT JOIN pictures pic ON sc.logo_id=pic.id';
        $logoPicField = ',IFNULL(pic.stored_fname,"") as logo_stored_fname';

        $sql = 'SELECT sc.*' . $logoPicField;
        $sql .= ' FROM school sc ';
        $sql .= $leftJoinLogoPic;
        $sql .= ' WHERE sc.disabled=0 AND sc.deleted=0 AND sc.id=:school_id';
        $sql .= ' ORDER BY sc.name ASC ';
        $school = $this->db->row($sql, array('school_id' => $user['school_id']));

        if (!empty($school)) {
            return $school;
        } else {
            return array();
        }
    }

    /**
     * school Events Bord as three columns ('internships' | 'job shadows' |'volunteering')
     * 
     * @return string
     */
    private function schoolEventsBord() {
        $phpTzIdentifier = getPhpTzIdentifierFromSession();
        $seiData = array();
        $seiData['phpTzIdentifier'] = $phpTzIdentifier;
        //set to get all joined and not join
        $seiData['options']['filter']['joined'] = false;

        //get internship events
        $seiData['options']['filter']['type'] = 'internships';
        $internshipRes = $this->schoolEvents('list', $seiData);
        $internships = $internshipRes['schEvtCnt'];
        //get job shadows events
        $seiData['options']['filter']['type'] = 'job shadows';
        $jobShadowsRes = $this->schoolEvents('list', $seiData);
        $jobShadows = $jobShadowsRes['schEvtCnt'];
        //get volunteering events
        $seiData['options']['filter']['type'] = 'volunteering';
        $volunteeringRes = $this->schoolEvents('list', $seiData);
        $volunteering = $volunteeringRes['schEvtCnt'];

        //get place holder
        $viewDialog = $this->eventNotificationsDialogs(array(), true);

        $schoolEventsBrod = $this->ui->schoolEventsBord($internships, $jobShadows, $volunteering,$viewDialog);

        return $schoolEventsBrod;
    }

    /**
     * at school page list events  , join users to event, leave users from events
     * 
     * @param string $mode list|join|leave
     */
    public function schoolEvents($mode, $iData = array()) {
        $this->setEventClass();
        return $this->event->schoolEvents($mode, $iData);
    }

    /**
     * event Notifications Dialogs (to get place holder) 
     * 
     * @param array $event event data
     * @param string $mode add | edit
     * @param bool $placeHolderOnly if true return place holder only
     * @param string $notification notification data
     * @param array $options options array
     * @return string
     */
    private function eventNotificationsDialogs($event, $placeHolderOnly = false, $notification = '', $options = array(), $phpTzIdentifier = 'UTC') {
        $this->setEventClass();
        return $this->event->eventNotificationsDialogs($event, $placeHolderOnly, $notification, $options, $phpTzIdentifier);
    }

}
?>