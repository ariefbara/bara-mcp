<?php

namespace Query\Application\Service\Firm\Client\TeamMembership;

use Query\ {
    Application\Service\Firm\Client\TeamMembershipRepository,
    Domain\Model\Firm\Team\TeamFileInfo,
    Domain\Service\Firm\Team\TeamFileInfoFinder
};

class ViewTeamFileInfo
{

    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;

    /**
     *
     * @var TeamFileInfoFinder
     */
    protected $teamFileInfoFinder;

    public function __construct(TeamMembershipRepository $teamMembershipRepository,
            TeamFileInfoFinder $teamFileInfoFinder)
    {
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->teamFileInfoFinder = $teamFileInfoFinder;
    }
    
    public function showById(string $firmId, string $clientId, string $teamMembershipId, string $teamFileInfoId): TeamFileInfo
    {
        return $this->teamMembershipRepository->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId)
                ->viewTeamFileInfo($this->teamFileInfoFinder, $teamFileInfoId);
    }

}
