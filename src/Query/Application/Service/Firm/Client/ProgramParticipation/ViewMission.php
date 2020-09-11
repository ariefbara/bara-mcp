<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

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
     * @param string $firmId
     * @param string $clientId
     * @param string $programParticipationId
     * @return Mission[]
     */
    public function showAll(string $firmId, string $clientId, string $programParticipationId)
    {
        return $this->missionRepository
                        ->allMissionsInProgramWhereClientParticipate($firmId, $clientId, $programParticipationId);
    }

    public function showById(string $firmId, string $clientId, string $programParticipationId, string $missionId): Mission
    {
        return $this->missionRepository->aMissionInProgramWhereClientParticipate(
                        $firmId, $clientId, $programParticipationId, $missionId);
    }

    public function showByPosition(
            string $firmId, string $clientId, string $programParticipationId, string $missionPosition): Mission
    {
        return $this->missionRepository->aMissionByPositionInProgramWhereClientParticipate(
                        $firmId, $clientId, $programParticipationId, $missionPosition);
    }

}
