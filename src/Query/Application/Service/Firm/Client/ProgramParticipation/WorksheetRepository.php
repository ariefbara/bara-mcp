<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\{
    Domain\Model\Firm\Program\Participant\Worksheet,
    Infrastructure\QueryFilter\WorksheetFilter
};

interface WorksheetRepository
{

    public function aWorksheetBelongsToClient(string $clientId, string $worksheetId): Worksheet;

    public function allWorksheetsInProgramParticipationBelongsToClient(
            string $clientId, string $clientProgramParticipationId, int $page, int $pageSize,
            ?WorksheetFilter $worksheetFilter);
}
