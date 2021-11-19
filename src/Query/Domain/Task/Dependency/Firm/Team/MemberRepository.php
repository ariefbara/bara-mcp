<?php

namespace Query\Domain\Task\Dependency\Firm\Team;

use Query\Domain\Model\Firm\Team\Member;

interface MemberRepository
{
    public function aMemberOfTeamInFirm(string $firmId, string $id): Member;
}
