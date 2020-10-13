<?php

namespace Query\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\CompletedMission;

class ViewCompletedMission
{

    /**
     *
     * @var CompletedMissionRepository
     */
    protected $completedMissionRepository;

    public function __construct(CompletedMissionRepository $completedMissionRepository)
    {
        $this->completedMissionRepository = $completedMissionRepository;
    }

    public function showProgress(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId): array
    {
        return $this->completedMissionRepository->missionProgressOfTeamWhereClientIsMember(
                        $firmId, $clientId, $teamMembershipId, $teamProgramParticipationId);
    }

    public function showLast(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId): ?CompletedMission
    {
        return $this->completedMissionRepository->lastCompletedMissionOfTeamWhereClientIsMember(
                        $firmId, $clientId, $teamMembershipId, $teamProgramParticipationId);
    }

}
