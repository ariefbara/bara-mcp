<?php

namespace Query\Application\Service\User\ProgramParticipation;

use Query\{
    Domain\Model\Firm\Program\Participant\Worksheet,
    Infrastructure\QueryFilter\WorksheetFilter
};

interface WorksheetRepository
{

    public function aWorksheetBelongsToUser(string $userId, string $worksheetId): Worksheet;

    public function allWorksheetsInProgramParticipationBelongsToUser(
            string $userId, string $userProgramParticipationId, int $page, int $pageSize,
            ?WorksheetFilter $worksheetFilter);
}
