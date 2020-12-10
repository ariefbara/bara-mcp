<?php

namespace Notification\Domain\Model\Firm\Program\Coordinator;

use Notification\Domain\Model\Firm\Program\Coordinator;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;

class CoordinatorMeetingAttendee
{

    /**
     * 
     * @var Coordinator
     */
    protected $coordinator;

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
