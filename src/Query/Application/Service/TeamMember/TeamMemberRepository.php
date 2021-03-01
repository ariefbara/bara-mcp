<?php

namespace Query\Application\Service\TeamMember;

use Query\Domain\Model\Firm\Team\Member;

interface TeamMemberRepository
{

    public function aTeamMemberOfClient(string $firmId, string $clientId, string $memberId): Member;

    public function aTeamMemberOfClientCorrespondWithTeam(string $firmId, string $clientId, string $teamId): Member;
}
