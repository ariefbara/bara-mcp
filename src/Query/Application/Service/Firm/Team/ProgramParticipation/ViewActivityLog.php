<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation;

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
     * @param string $teamId
     * @param string $teamProgramParticipationId
     * @param int $page
     * @param int $pageSize
     * @return ActivityLog[]
     */
    public function showAll(string $teamId, string $teamProgramParticipationId, int $page, int $pageSize)
    {
        return $this->activityLogRepository->allActivityLogInProgramParticipationOfTeam($teamId, $teamProgramParticipationId, $page, $pageSize);
    }

}
