<?php

namespace Query\Application\Service\Firm\Client\TeamMembership;

use Query\ {
    Application\Service\Firm\Client\TeamMembershipRepository,
    Domain\Model\Firm\Program,
    Domain\Service\Firm\ProgramFinder
};

class ViewAvailableProgram
{

    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;

    /**
     *
     * @var ProgramFinder
     */
    protected $programFinder;
    
    public function __construct(TeamMembershipRepository $teamMembershipRepository, ProgramFinder $programFinder)
    {
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->programFinder = $programFinder;
    }
    
    /**
     * 
     * @param int $page
     * @param int $pageSize
     * @return Program[]
     */
    public function showAll(string $firmId, string $clientId, string $teamMembershipId, int $page, int $pageSize)
    {
        return $this->teamMembershipRepository->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId)
                ->viewAllAvailablePrograms($this->programFinder, $page, $pageSize);
    }
    
    public function showById(string $firmId, string $clientId, string $teamMembershipId, string $programId): Program
    {
        return $this->teamMembershipRepository->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId)
                ->viewProgram($this->programFinder, $programId);
    }


}
