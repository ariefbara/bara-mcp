<?php

namespace Query\Application\Service\User\AsProgramParticipant;

use Query\Application\Service\Participant\ActivityLogRepository;
use Query\Domain\SharedModel\ActivityLog;

class ViewActivityLog
{

    /**
     * 
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    /**
     * 
     * @var ActivityLogRepository
     */
    protected $activityLogRepository;

    public function __construct(UserParticipantRepository $userParticipantRepository,
            ActivityLogRepository $activityLogRepository)
    {
        $this->userParticipantRepository = $userParticipantRepository;
        $this->activityLogRepository = $activityLogRepository;
    }

    /**
     * 
     * @param string $userId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @return ActivityLog[]
     */
    public function showSelfActivityLog(string $userId, string $participantId, int $page, int $pageSize)
    {
        return $this->userParticipantRepository->aUserParticipant($userId, $participantId)
                        ->viewSelfActivityLogs($this->activityLogRepository, $page, $pageSize);
    }

    /**
     * 
     * @param string $userId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @return ActivityLog[]
     */
    public function showSharedActivityLog(string $userId, string $participantId, int $page, int $pageSize)
    {
        return $this->userParticipantRepository->aUserParticipant($userId, $participantId)
                        ->viewSharedActivityLogs($this->activityLogRepository, $page, $pageSize);
    }

}
