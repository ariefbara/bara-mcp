<?php

namespace Query\Application\Service\Firm\Client;

use Query\Domain\Model\Firm\Team\Member;

interface TeamMembershipRepository
{

    public function aTeamMembershipOfClient(string $firmId, string $clientId, string $teamMembershipId): Member;

    public function allTeamMembershipsOfClient(string $firmId, string $clientId, int $page, int $pageSize);
    
    public function isActiveTeamMembership(string $firmId, string $clientId, string $teamMembershipId): bool;
}
