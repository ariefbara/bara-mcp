<?php

namespace Query\Application\Service\Client\AsProgramParticipant;

use Query\Application\Service\Participant\ActivityLogRepository;
use Query\Domain\SharedModel\ActivityLog;

class ViewActivityLog
{

    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    /**
     * 
     * @var ActivityLogRepository
     */
    protected $activityLogRepository;

    public function __construct(ClientParticipantRepository $clientParticipantRepository,
            ActivityLogRepository $activityLogRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->activityLogRepository = $activityLogRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @return ActivityLog[]
     */
    public function showSelfActivityLogs(string $firmId, string $clientId, string $participantId, int $page,
            int $pageSize)
    {
        return $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $participantId)
                        ->viewSelfActivityLogs($this->activityLogRepository, $page, $pageSize);
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @return ActivityLog[]
     */
    public function showSharedActivityLogs(string $firmId, string $clientId, string $participantId, int $page,
            int $pageSize)
    {
        return $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $participantId)
                        ->viewSharedActivityLogs($this->activityLogRepository, $page, $pageSize);
    }

}
