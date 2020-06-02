<?php

namespace Query\Application\Service\Client\ProgramParticipation;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Query\Domain\Model\Firm\Program\Mission;

interface MissionRepository
{

    public function aMissionInProgramWhereParticipantParticipate(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $missionId): Mission;
    
    public function aMissionByPositionInProgramWhereParticipantParticipate(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $position): Mission;

    public function allMissionsContainSubmittedWorksheetCount(
            ProgramParticipationCompositionId $programParticipationCompositionId);
}
