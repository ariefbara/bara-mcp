<?php

namespace Query\Application\Service\User\ProgramParticipation;

interface ActivityLogRepository
{

    public function allActivityLogsInProgramParticipationOfUser(
            string $userId, string $programParticipationId, int $page, int $pageSize);
}
