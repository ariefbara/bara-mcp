<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Team\Member;

interface MemberRepository
{

    public function ofId(string $memberId): Member;
}
