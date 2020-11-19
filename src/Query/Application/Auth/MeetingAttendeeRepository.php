<?php

namespace Query\Application\Auth;

interface MeetingAttendeeRepository
{
    public function containRecordOfActiveMeetingAttendeeCorrespondWithPersonnelWithInitiatorRole(
            string $firmId, string $personnelId, string $meetingId): bool;
}
