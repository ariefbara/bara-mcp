<?php

namespace Query\Application\Service\TeamMember;

use Query\Domain\SharedModel\ActivityLog;

interface ActivityLogRepository
{
    public function allMemberActivityLogsInProgram(string $memberId, string $teamId, string $participantId, int $page, int $pageSize);
    
    public function allTeamSharedActivityLogsInProgram(string $memberId, string $teamId, string $participantId, int $page, int $pageSize);
}
