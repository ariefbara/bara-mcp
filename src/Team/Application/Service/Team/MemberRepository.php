<?php

namespace Team\Application\Service\Team;

use Team\Domain\Model\Team\Member;

interface MemberRepository
{

    public function aMemberCorrespondWithClient(string $firmId, string $teamId, string $clientId): Member;

    public function ofId(string $firmId, string $teamId, string $memberId): Member;

    public function update(): void;
}
