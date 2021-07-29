<?php

namespace Firm\Domain\Service;

use Firm\Domain\Model\Firm\Program\ActivityType\MeetingType\Meeting\Attendee;

interface MeetingAttendeeRepository
{
    public function anAttendeeBelongsToTeamCorrespondWithMeeting(string $teamId, string $meetingId): Attendee;
}
