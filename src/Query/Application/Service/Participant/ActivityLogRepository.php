<?php

namespace Query\Application\Service\Participant;

use Query\Domain\SharedModel\ActivityLog;

interface ActivityLogRepository
{
    public function allParticipantActivityLogs(string $participantId, int $page, int $pageSize);
    
    public function allSharedActivityLog(string $participantId, int $page, int $pageSize);
}
