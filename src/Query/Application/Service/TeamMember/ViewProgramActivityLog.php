<?php

namespace Query\Application\Service\TeamMember;

use Query\Domain\SharedModel\ActivityLog;

class ViewProgramActivityLog
{

    /**
     * 
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * 
     * @var ActivityLogRepository
     */
    protected $activityLogRepository;
    
    public function __construct(TeamMemberRepository $teamMemberRepository, ActivityLogRepository $activityLogRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->activityLogRepository = $activityLogRepository;
    }
    
    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $teamId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @return ActivityLog[]
     */
    public function showSelfActivityLogs(string $firmId, string $clientId, string $teamId, string $participantId, int $page, int $pageSize)
    {
        return $this->teamMemberRepository->aTeamMemberOfClientCorrespondWithTeam($firmId, $clientId, $teamId)
                ->viewAllSelfActivityLogsInProgram($this->activityLogRepository, $participantId, $page, $pageSize);
    }
    
    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $teamId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @return ActivityLog[]
     */
    public function showSharedSelfActivityLogs(string $firmId, string $clientId, string $teamId, string $participantId, int $page, int $pageSize)
    {
        return $this->teamMemberRepository->aTeamMemberOfClientCorrespondWithTeam($firmId, $clientId, $teamId)
                ->viewAllSharedActivityLogsInProgram($this->activityLogRepository, $participantId, $page, $pageSize);
    }


}
