<?php

namespace Query\Application\Service\Firm\Client\AsTeamMember;

use Query\Domain\Model\Firm\Team\Member\TeamMemberActivityLog;

interface TeamMemberActivityLogRepository
{

    public function allActivityLogsOfTeamMember(string $memberId, int $page, int $pageSize);

    public function anActivityLogOfTeamMember(string $memberId, string $teamMemberActivityLogId): TeamMemberActivityLog;
}
