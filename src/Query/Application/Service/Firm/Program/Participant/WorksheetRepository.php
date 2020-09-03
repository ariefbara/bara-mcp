<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\ {
    Application\Service\Firm\Client\ProgramParticipation\WorksheetRepository as InterfaceForClient,
    Application\Service\User\ProgramParticipation\WorksheetRepository as InterfaceForUser,
    Domain\Model\Firm\Program\Participant\Worksheet
};

interface WorksheetRepository extends InterfaceForClient, InterfaceForUser
{

    public function ofId(string $firmId, string $programId, string $participantId, string $worksheetId): Worksheet;

    public function all(string $firmId, string $programId, string $participantId, int $page, int $pageSize): Worksheet;
}
