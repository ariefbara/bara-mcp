<?php

namespace Query\Application\Auth;

interface MeetingAttendeeRepository
{
    public function containRecordOfActiveMeetingAttendeeCorrespondWithPersonnelWithInitiatorRole(
            string $firmId, string $personnelId, string $meetingId): bool;
    
    public function containRecordOfActiveMeetingAttendeeCorrespondWithManagerWithInitiatorRole(
            string $firmId, string $managerId, string $meetingId): bool;
    
    public function containRecordOfActiveMeetingAttendeeCorrespondWithClientAsProgramParticipantHavingInitiatorRole(
            string $firmId, string $clientId, string $meetingId): bool;
    
    public function containRecordOfActiveMeetingAttendeeCorrespondWithUserAsProgramParticipantHavingInitiatorRole(
            string $userId, string $meetingId): bool;
    
    public function containRecordOfActiveMeetingAttendeeCorrespondWithTeamAsProgramParticipantHavingInitiatorRole(
            string $firmId, string $teamId, string $meetingId): bool;
    
    
}
