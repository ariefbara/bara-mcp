<?php

namespace Query\Application\Service\Firm\Program\ClientParticipant;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;

interface WorksheetRepository
{

    public function aWorksheetBelongsToClientParticipant(
            string $firmId, string $clientId, string $programId, string $worksheetId): Worksheet;

    public function allWorksheetsBelongsToClientParticipant(
            string $firmId, string $clientId, string $programId, int $page, int $pageSize, ?string $missionId,
            ?string $parentWorksheetId);
}
