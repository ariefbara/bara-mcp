<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\CompletedMission;

class ViewCompletedMission
{
    /**
     *
     * @var CompletedMissionRepository
     */
    protected $completedMissionRepository;
    
    public function __construct(CompletedMissionRepository $completedMissionRepository)
    {
        $this->completedMissionRepository = $completedMissionRepository;
    }
    
    public function showProgress(string $firmId, string $programId, string $participantId)
    {
        return $this->completedMissionRepository->missionProgressOfParticipant($firmId, $programId, $participantId);
    }
    
    public function showLast(string $firmId, string $programId, string $participantId): ?CompletedMission
    {
        return $this->completedMissionRepository->lastCompletedMissionProgressOfParticipant($firmId, $programId, $participantId);
    }

}
