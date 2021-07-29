<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

use Firm\Domain\Model\Firm\Program\ActivityType\MeetingType\Meeting\Attendee;

interface AttendeeRepository
{

    public function anAttendeeBelongsToClientParticipantCorrespondWithMeeting(
            string $firmId, string $clientId, string $meetingId): Attendee;

    public function update(): void;
    
    public function ofId(string $attendeeId): Attendee;
}
