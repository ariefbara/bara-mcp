<?php

namespace Query\Application\Service\Firm\Client;

use Query\Domain\Model\Firm\Team\Member;

class ViewTeamMembership
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

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param int $page
     * @param int $pageSize
     * @return Member[]
     */
    public function showAll(string $firmId, string $clientId, int $page, int $pageSize)
    {
        return $this->teamMembershipRepository->allTeamMembershipsOfClient($firmId, $clientId, $page, $pageSize);
    }

    public function showById(string $firmId, string $clientId, string $teamMembershipId): Member
    {
        return $this->teamMembershipRepository->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId);
    }

}
