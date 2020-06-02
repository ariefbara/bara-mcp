<?php

namespace Query\Application\Service\Client\ProgramParticipation;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Query\Domain\Model\Firm\Program\Mission;

class MissionView
{

    /**
     *
     * @var MissionRepository
     */
    protected $missionRepository;

    function __construct(MissionRepository $missionRepository)
    {
        $this->missionRepository = $missionRepository;
    }

    /**
     * 
     * @param ProgramParticipationCompositionId $programParticipationCompositionId
     * @return Mission[]
     */
    public function showAll(ProgramParticipationCompositionId $programParticipationCompositionId)
    {
        return $this->missionRepository->allMissionsContainSubmittedWorksheetCount($programParticipationCompositionId);
    }

    public function showById(ProgramParticipationCompositionId $programParticipationCompositionId, string $missionId): Mission
    {
        return $this->missionRepository->aMissionInProgramWhereParticipantParticipate(
                        $programParticipationCompositionId, $missionId);
    }

    public function showByPosition(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $position): Mission
    {
        return $this->missionRepository->aMissionByPositionInProgramWhereParticipantParticipate(
                        $programParticipationCompositionId, $position);
    }

}
