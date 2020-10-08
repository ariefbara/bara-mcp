<?php

namespace Query\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Mission;

class ViewMission
{

    /**
     *
     * @var MissionRepository
     */
    protected $missionRepository;

    public function __construct(MissionRepository $missionRepository)
    {
        $this->missionRepository = $missionRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $teamMembershipId
     * @param string $teamProgramParticipationId
     * @param int $page
     * @param int $pageSize
     * @return Mission[]
     */
    public function showAll(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId)
    {
        return $this->missionRepository->allMissionsInProgramWhereClientIsMemberOfParticipatingTeam(
                        $firmId, $clientId, $teamMembershipId, $teamProgramParticipationId);
    }

    public function showById(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId,
            string $missionId): Mission
    {
        return $this->missionRepository->aMissionInProgramWhereClientIsMemberOfParticipatingTeam(
                        $firmId, $clientId, $teamMembershipId, $teamProgramParticipationId, $missionId);
    }

    public function showByPosition(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId,
            string $missionPosition): Mission
    {
        return $this->missionRepository->aMissionByPositionInProgramWhereClientIsMemberOfParticipatingTeam(
                        $firmId, $clientId, $teamMembershipId, $teamProgramParticipationId, $missionPosition);
    }

}
