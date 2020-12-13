<?php

namespace Query\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;

use Query\Domain\Model\Firm\Program\Activity\Invitee;
use Query\Domain\SharedModel\Notification;

class MeetingAttendeeNotification
{

    /**
     * 
     * @var Invitee
     */
    protected $meetingAttendee;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Notification
     */
    protected $notification;

    function getMeetingAttendee(): Invitee
    {
        return $this->meetingAttendee;
    }

    function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    public function getMessage(): string
    {
        return $this->notification->getMessage();
    }

}
