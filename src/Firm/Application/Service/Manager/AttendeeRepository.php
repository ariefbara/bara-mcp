<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;

interface AttendeeRepository
{

    public function anAttendeeBelongsToManagerCorrespondWithMeeting(
            string $firmId, string $managerId, string $meetingId): Attendee;

    public function update(): void;
    
    public function ofId(string $attendeeId): Attendee;
}
