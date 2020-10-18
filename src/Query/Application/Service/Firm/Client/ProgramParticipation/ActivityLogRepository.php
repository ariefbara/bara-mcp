<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\Domain\SharedModel\ActivityLog;

interface ActivityLogRepository
{

    public function allActivityLogsInProgramParticipationOfClient(
            string $clientId, string $programParticipationId, int $page, int $pageSize);
}
