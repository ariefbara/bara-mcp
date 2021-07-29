<?php

namespace Firm\Domain\Task\MeetingInitiator;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\Attendee;

interface AttendeeRepository
{
    public function ofId(string $meetingAttendeeId): Attendee;
    
}
