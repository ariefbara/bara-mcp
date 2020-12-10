<?php

namespace Notification\Domain\Model\Firm\Program\Consultant;

use Notification\Domain\Model\Firm\Program\Consultant;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;

class ConsultantMeetingAttendee
{
    /**
     * 
     * @var Consultant
     */
    protected $consultant;

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
