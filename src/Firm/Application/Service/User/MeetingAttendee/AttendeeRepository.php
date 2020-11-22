<?php

namespace Firm\Application\Service\User\MeetingAttendee;

use Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;

interface AttendeeRepository
{

    public function anAttendeeBelongsToUserParticipantCorrespondWithMeeting(
            string $userId, string $meetingId): Attendee;

    public function update(): void;
    
    public function ofId(string $attendeeId): Attendee;
}
