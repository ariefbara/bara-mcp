<?php

namespace Query\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Mission;

interface MissionRepository
{

    public function aMissionInProgramWhereClientIsMemberOfParticipatingTeam(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId,
            string $missionId): Mission;

    public function aMissionByPositionInProgramWhereClientIsMemberOfParticipatingTeam(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId,
            string $missionPosition): Mission;

    public function allMissionsInProgramWhereClientIsMemberOfParticipatingTeam(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId);
}
