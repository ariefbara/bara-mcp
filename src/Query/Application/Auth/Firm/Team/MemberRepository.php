<?php

namespace Query\Application\Auth\Firm\Team;

interface MemberRepository
{

    public function containRecordOfActiveTeamMemberCorrespondWithClient(string $firmId, string $teamId, string $clientId): bool;

    public function containRecordOfActiveTeamMemberWithAdminPriviledgeCorrespondWithClient(
            string $firmId, string $teamId, string $clientId): bool;
}
