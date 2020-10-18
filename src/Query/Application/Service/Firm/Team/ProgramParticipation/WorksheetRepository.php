<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation;

use Query\{
    Domain\Model\Firm\Program\Participant\Worksheet,
    Infrastructure\QueryFilter\WorksheetFilter
};

interface WorksheetRepository
{

    public function aWorksheetBelongsToTeam(string $teamId, string $worksheetId): Worksheet;

    public function allWorksheetsInProgramParticipationBelongsToTeam(
            string $teamId, string $teamProgramParticipationId, int $page, int $pageSize,
            ?WorksheetFilter $worksheetFilter);
}
