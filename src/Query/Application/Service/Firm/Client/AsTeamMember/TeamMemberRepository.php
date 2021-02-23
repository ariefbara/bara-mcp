<?php

namespace Query\Application\Service\Firm\Client\AsTeamMember;

use Query\Domain\Model\Firm\Team\Member;

interface TeamMemberRepository
{

    public function aTeamMembershipCorrespondWithTeam(string $clientId, string $teamId): Member;

    public function aTeamMembershipOfClient(string $firmId, string $clientId, string $teamMembershipId): Member;
}
