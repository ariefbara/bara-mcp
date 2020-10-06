<?php

namespace Query\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

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
     * @param string $firmId
     * @param string $clientId
     * @param string $teamMembershipId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @return ActivityLog[]
     */
    public function showAll(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId, int $page,
            int $pageSize)
    {
        return $this->activityLogRepository->allActivityLogsBelongsToTeamParticipantWhereClientIsMember(
                        $firmId, $clientId, $teamMembershipId, $teamProgramParticipationId, $page, $pageSize);
    }

}
