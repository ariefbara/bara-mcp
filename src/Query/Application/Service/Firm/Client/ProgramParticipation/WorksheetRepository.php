<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;

interface WorksheetRepository
{

    public function aWorksheetBelongsToClient(string $firmId, string $clientId, string $programParticipationId,
            string $worksheetId): Worksheet;

    public function allWorksheetsBelongsToClient(string $firmId, string $clientId, string $programParticipationId,
            int $page, int $pageSize, ?string $missionId, ?string $parentWorksheetId);
}
