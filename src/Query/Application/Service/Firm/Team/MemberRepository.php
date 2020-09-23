<?php

namespace Query\Application\Service\Firm\Team;

use Query\ {
    Application\Service\Firm\Client\TeamMembershipRepository as InterfaceForClient,
    Domain\Model\Firm\Team\Member
};

interface MemberRepository extends InterfaceForClient
{

    public function ofId(string $firmId, string $teamId, string $memberId): Member;

    public function all(string $firmId, string $teamId, int $page, int $pageSize);
}
