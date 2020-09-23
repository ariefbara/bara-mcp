<?php

namespace Client\Application\Service\Client;

class QuitTeamMembership
{
    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;
    
    public function __construct(TeamMembershipRepository $teamMembershipRepository)
    {
        $this->teamMembershipRepository = $teamMembershipRepository;
    }
    
    public function execute(string $firmId, string $clientId, string $teamMembershipId): void
    {
        $this->teamMembershipRepository->ofId($firmId, $clientId, $teamMembershipId)->quit();
        $this->teamMembershipRepository->update();
    }

}
