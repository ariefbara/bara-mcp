<?php

namespace Query\Application\Service\Firm\Client;

use Query\ {
    Application\Service\Firm\Client\AsTeamMember\TeamMemberRepository,
    Domain\Model\Firm\Team\Member
};

interface TeamMembershipRepository extends TeamMemberRepository
{

    public function aTeamMembershipOfClient(string $firmId, string $clientId, string $teamMembershipId): Member;

    public function allTeamMembershipsOfClient(string $firmId, string $clientId, int $page, int $pageSize);
}
