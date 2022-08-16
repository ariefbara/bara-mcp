<?php

namespace Team\Application\Service\TeamMember;

use Team\Domain\Model\Team\Member;

interface TeamMemberRepository
{

    public function aMemberOfTeam(string $firmId, string $clientId, string $memberid): Member;
    
    public function aMemberCorrespondWithClient(string $firmId, string $teamId, string $clientId): Member;

    public function update(): void;
}
