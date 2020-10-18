<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\ {
    Application\Service\Firm\Client\ProgramParticipation\WorksheetRepository as InterfaceForClient,
    Application\Service\Firm\Team\ProgramParticipation\WorksheetRepository as InterfaceForTeam,
    Application\Service\User\ProgramParticipation\WorksheetRepository as InterfaceForUser,
    Domain\Model\Firm\Program\Participant\Worksheet,
    Infrastructure\QueryFilter\WorksheetFilter
};

interface WorksheetRepository extends InterfaceForClient, InterfaceForUser, InterfaceForTeam
{

    public function aWorksheetInProgram(string $programId, string $worksheetId): Worksheet;

    public function allWorksheetBelongsToParticipantInProgram(
            string $programId, string $participantId, int $page, int $pageSize, ?WorksheetFilter $worksheetFilter);
}
