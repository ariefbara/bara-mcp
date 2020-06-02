<?php

namespace Query\Application\Service\Client\ProgramParticipation;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Query\Domain\Model\Firm\Program\Participant\Worksheet;

interface WorksheetRepository
{

    public function aWorksheetOfParticipant(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $worksheetId): Worksheet;

    public function allWorksheetsOfParticipant(
            ProgramParticipationCompositionId $programParticipationCompositionId, int $page, int $pageSize,
            ?string $missionId, ?string $parentWorksheetId);

    public function allWorksheetOfParticipantCorrespondWithMission(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $missionId);
}
