<?php

namespace Query\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\CompletedMission;

interface CompletedMissionRepository
{
    public function lastCompletedMissionOfTeamWhereClientIsMember(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId): ?CompletedMission;
    
    public function missionProgressOfTeamWhereClientIsMember(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId);
}
