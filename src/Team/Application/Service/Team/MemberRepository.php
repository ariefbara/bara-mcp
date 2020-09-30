<?php

namespace Team\Application\Service\Team;

use Team\ {
    Application\Service\Firm\Client\TeamMembershipRepository,
    Domain\Model\Team\Member
};

interface MemberRepository extends TeamMembershipRepository
{

    public function aMemberCorrespondWithClient(string $firmId, string $teamId, string $clientId): Member;

    public function ofId(string $firmId, string $teamId, string $memberId): Member;

    public function update(): void;
}
