<?php

namespace Team\Application\Service\TeamMember;

use Team\Domain\Model\Team\Member;

interface TeamMemberRepository
{

    public function aMemberCorrespondWithClient(string $firmId, string $teamId, string $clientId): Member;

    public function update(): void;
}
