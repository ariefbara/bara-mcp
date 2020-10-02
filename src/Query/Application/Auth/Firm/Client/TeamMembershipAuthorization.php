<?php

namespace Query\Application\Auth\Firm\Client;

use Resources\Exception\RegularException;

class TeamMembershipAuthorization
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
        if (!$this->teamMembershipRepository->containRecordOfActiveTeamMembership($firmId, $clientId, $teamMembershipId)) {
            $errorDetail = "forbidden: only active team member can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
