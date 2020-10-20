<?php

namespace Query\Application\Service\Firm\Client\TeamMembership;

use Query\{
    Application\Service\Firm\Client\TeamMembershipRepository,
    Domain\Model\Firm\Team\TeamProgramRegistration,
    Domain\Service\Firm\Team\TeamProgramRegistrationFinder
};

class ViewTeamProgramRegistration
{

    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;

    /**
     *
     * @var TeamProgramRegistrationFinder
     */
    protected $teamProgramRegistrationFinder;

    public function __construct(TeamMembershipRepository $teamMembershipRepository,
            TeamProgramRegistrationFinder $teamProgramRegistrationFinder)
    {
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->teamProgramRegistrationFinder = $teamProgramRegistrationFinder;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $teamMembershipId
     * @param int $page
     * @param int $pageSize
     * @param bool|null $concludedStatus
     * @return TeamProgramRegistration[]
     */
    public function showAll(
            string $firmId, string $clientId, string $teamMembershipId, int $page, int $pageSize, ?bool $concludedStatus)
    {
        return $this->teamMembershipRepository
                        ->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId)
                        ->viewAllTeamProgramRegistration(
                                $this->teamProgramRegistrationFinder, $page, $pageSize, $concludedStatus);
    }

    public function showById(string $firmId, string $clientId, string $teamMembershipId,
            string $teamProgramRegistrationId): TeamProgramRegistration
    {
        return $this->teamMembershipRepository
                        ->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId)
                        ->viewTeamProgramRegistration($this->teamProgramRegistrationFinder, $teamProgramRegistrationId);
    }

}
