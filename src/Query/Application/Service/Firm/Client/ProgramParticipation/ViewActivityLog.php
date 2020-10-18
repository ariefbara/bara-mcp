<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\Domain\SharedModel\ActivityLog;

class ViewActivityLog
{

    /**
     *
     * @var ActivityLogRepository
     */
    protected $activityLogRepository;

    public function __construct(ActivityLogRepository $activityLogRepository)
    {
        $this->activityLogRepository = $activityLogRepository;
    }

    /**
     * 
     * @param string $clientId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @return ActivityLog[]
     */
    public function showAll(string $clientId, string $programParticipationId, int $page, int $pageSize)
    {
        return $this->activityLogRepository->allActivityLogsInProgramParticipationOfClient(
                        $clientId, $programParticipationId, $page, $pageSize);
    }

}
