<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\ {
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\CompletedMissionRepository as InterfaceForTeamMember,
    Domain\Model\Firm\Program\Participant\CompletedMission
};

interface CompletedMissionRepository extends InterfaceForTeamMember
{

    public function missionProgressOfParticipant(string $firmId, string $programId, string $participantId);

    public function lastCompletedMissionProgressOfParticipant(string $firmId, string $programId, string $participantId): ?CompletedMission;
}
