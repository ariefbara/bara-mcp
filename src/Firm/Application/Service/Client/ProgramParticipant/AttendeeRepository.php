<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

use Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;

interface AttendeeRepository
{

    public function anAttendeeBelongsToClientParticipantCorrespondWithMeeting(
            string $firmId, string $clientId, string $meetingId): Attendee;

    public function update(): void;
    
    public function ofId(string $attendeeId): Attendee;
}
