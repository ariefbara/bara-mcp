<?php

namespace Query\Application\Service\User\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Mission;

class ViewMission
{

    /**
     *
     * @var MissionRepository
     */
    protected $missionRepository;

    public function __construct(MissionRepository $missionRepository)
    {
        $this->missionRepository = $missionRepository;
    }

    /**
     * 
     * @param string $userId
     * @param string $programParticipationId
     * @return Mission[]
     */
    public function showAll(string $userId, string $programParticipationId)
    {
        return $this->missionRepository->allMissionsInProgramWhereUserParticipate($userId, $programParticipationId);
    }

    public function showById(string $userId, string $programParticipationId, string $missionId): Mission
    {
        return $this->missionRepository->aMissionInProgramWhereUserParticipate($userId, $programParticipationId,
                        $missionId);
    }
    
    public function showByPosition(string $userId, string $programParticipationId, string $missionPosition): Mission
    {
        return $this->missionRepository->aMissionByPositionInProgramWhereUserParticipate(
                $userId, $programParticipationId, $missionPosition);
    }

}
