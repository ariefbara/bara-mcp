<?php

namespace Query\Application\Auth;

interface MeetingAttendeeRepository
{
    public function containRecordOfActiveMeetingAttendeeCorrespondWithPersonnelWithInitiatorRole(
            string $firmId, string $personnelId, string $meetingId): bool;
    
    public function containRecordOfActiveMeetingAttendeeCorrespondWithManagerWithInitiatorRole(
            string $firmId, string $managerId, string $meetingId): bool;
}
