<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Team\Member;

interface TeamMemberRepository
{

    public function aTeamMemberInFirm(string $firmId, string $teamMemberId): Member;

    public function allMembersOfTeam(
            string $firmId, string $teamId, int $page, int $pageSize, ?bool $activeStatus);
}
