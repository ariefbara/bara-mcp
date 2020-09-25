<?php

namespace Query\Application\Service\Firm\Client\TeamMembership;

use Query\Application\Service\Firm\Client\TeamMembershipRepository;
use Resources\Exception\RegularException;

class ActiveTeamMembershipAuthorization
{
    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMemberRepository;
    
    public function __construct(TeamMembershipRepository $teamMemberRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
    }
    
    public function execute(string $firmId, string $clientId, string $teamMembershipId): void
    {
        if (!$this->teamMemberRepository->isActiveTeamMembership($firmId, $clientId, $teamMembershipId)) {
            $errorDetail = "forbidden: only active team member can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
