<?php

namespace Firm\Application\Service\Personnel;

use Firm\Domain\Model\Firm\Program\ActivityType\MeetingType\Meeting\Attendee;

interface AttendeeRepository
{

    public function anAttendeeBelongsToPersonnelCorrespondWithMeeting(
            string $firmId, string $personnelId, string $meetingId): Attendee;

    public function update(): void;
    
    public function ofId(string $attendeeId): Attendee;
}
