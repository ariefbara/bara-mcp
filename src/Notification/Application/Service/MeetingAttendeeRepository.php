<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;

interface MeetingAttendeeRepository
{

    public function ofId(string $meetingAttendeeId): MeetingAttendee;

    public function update(): void;
}
