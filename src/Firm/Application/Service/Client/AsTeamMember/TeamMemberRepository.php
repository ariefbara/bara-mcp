<?php

namespace Firm\Application\Service\Client\AsTeamMember;

use Firm\Domain\Model\Firm\Team\Member;

interface TeamMemberRepository
{
    public function aTeamMemberCorrespondWithTeam(string $firmId, string $clientId, string $teamId): Member;
    
    public function update(): void;
}
