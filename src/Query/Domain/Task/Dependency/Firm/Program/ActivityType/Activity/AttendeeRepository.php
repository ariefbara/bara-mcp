<?php

namespace Query\Domain\Task\Dependency\Firm\Program\ActivityType\Activity;

use Query\Domain\Model\Firm\Program\Activity\Invitee;

interface AttendeeRepository
{

    /**
     * 
     * @return Invitee[]
     */
    public function allAttendeesInActivityOfProgram(
            string $programId, string $activityId, int $page, int $pageSize, ?bool $cancelledStatus,
            ?bool $attendedStatus);

    public function anAttendeeInProgram(string $programId, string $attendeeId): Invitee;
}
