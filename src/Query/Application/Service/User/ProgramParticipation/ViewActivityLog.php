<?php

namespace Query\Application\Service\User\ProgramParticipation;

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
     * @param string $userId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @return ActivityLog[]
     */
    public function showAll(string $userId, string $programParticipationId, int $page, int $pageSize)
    {
        return $this->activityLogRepository->allActivityLogsInProgramParticipationOfUser($userId,
                        $programParticipationId, $page, $pageSize);
    }

}
