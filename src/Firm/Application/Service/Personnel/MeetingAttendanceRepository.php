<?php

namespace Firm\Application\Service\Personnel;

use Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;

interface MeetingAttendanceRepository
{

    public function aMeetingAttendanceBelongsToPersonnelCorrespondWithMeeting(
            string $firmId, string $personnelId, string $meetingId): Attendee;

    public function update(): void;
}
