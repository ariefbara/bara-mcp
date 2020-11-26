<?php

namespace Firm\Domain\Service;

use Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;

interface MeetingAttendeeRepository
{
    public function anAttendeeBelongsToTeamCorrespondWithMeeting(string $teamId, string $meetingId): Attendee;
}
