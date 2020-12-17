<?php

namespace Query\Domain\Model\Firm\Program\MeetingType\Meeting;

use Query\Domain\Model\Firm\Program\Activity;
use Query\Domain\SharedModel\Notification;

class MeetingNotification
{

    /**
     * 
     * @var Activity
     */
    protected $meeting;

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

    function getMeeting(): Activity
    {
        return $this->meeting;
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
