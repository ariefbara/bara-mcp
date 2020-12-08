<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;
use Notification\Domain\Model\Firm\Program\Participant;

class ParticipantMeetingAttendee
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

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
