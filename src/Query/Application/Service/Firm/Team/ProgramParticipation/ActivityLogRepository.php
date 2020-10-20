<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation;

interface ActivityLogRepository
{
    public function allActivityLogInProgramParticipationOfTeam(
            string $teamId, string $teamProgramParticipationId, int $page, int $pageSize);
}
