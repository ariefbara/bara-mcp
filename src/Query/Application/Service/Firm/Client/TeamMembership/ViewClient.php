<?php

namespace Query\Application\Service\Firm\Client\TeamMembership;

use Query\ {
    Application\Service\Firm\Client\TeamMembershipRepository,
    Domain\Model\Firm\Client,
    Domain\Service\Firm\ClientFinder
};

class ViewClient
{

    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;

    /**
     *
     * @var ClientFinder
     */
    protected $clientFinder;

    public function __construct(TeamMembershipRepository $teamMembershipRepository, ClientFinder $clientFinder)
    {
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->clientFinder = $clientFinder;
    }
    
    public function showByEmail(string $firmId, string $clientId, string $teamMembershipId, string $clientEmail): Client
    {
        return $this->teamMembershipRepository->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId)
                ->viewClientByEmail($this->clientFinder, $clientEmail);
    }

}
