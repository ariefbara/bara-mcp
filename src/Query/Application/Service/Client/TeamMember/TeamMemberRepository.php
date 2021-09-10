<?php

namespace Query\Application\Service\Client\TeamMember;

use Query\Domain\Model\Firm\Team\Member;

interface TeamMemberRepository
{

    public function aTeamMemberOfClientCorrespondWithTeam(string $firmId, string $clientId, string $teamId): Member;
}
