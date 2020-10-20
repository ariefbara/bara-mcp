<?php

namespace Query\Application\Service\Firm\Client\TeamMembership;

use Query\{
    Application\Service\Firm\Client\TeamMembershipRepository,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Service\Firm\Team\TeamProgramParticipationFinder
};

class ViewTeamProgramParticipation
{

    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;

    /**
     *
     * @var TeamProgramParticipationFinder
     */
    protected $teamProgramParticipationFinder;

    public function __construct(TeamMembershipRepository $teamMembershipRepository,
            TeamProgramParticipationFinder $teamProgramParticipationFinder)
    {
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->teamProgramParticipationFinder = $teamProgramParticipationFinder;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $teamMembershipId
     * @param int $page
     * @param int $pageSize
     * @return TeamProgramParticipation[]
     */
    public function showAll(string $firmId, string $clientId, string $teamMembershipId, int $page, int $pageSize)
    {
        return $this->teamMembershipRepository->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId)
                        ->viewAllProgramParticipation($this->teamProgramParticipationFinder, $page, $pageSize);
    }

    public function showById(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId): TeamProgramParticipation
    {
        return $this->teamMembershipRepository->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId)
                        ->viewTeamProgramParticipation(
                                $this->teamProgramParticipationFinder, $teamProgramParticipationId);
    }

}
