<?php

namespace Notification\Domain\Model\Firm\Manager;

use Notification\Domain\Model\Firm\Manager;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;

class ManagerMeetingAttendee
{

    /**
     * 
     * @var Manager
     */
    protected $manager;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var MeetingAttendee
     */
    protected $meetingAttendee;

    protected function __construct()
    {
        
    }

}
