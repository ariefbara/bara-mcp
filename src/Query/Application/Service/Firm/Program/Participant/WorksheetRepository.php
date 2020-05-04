<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;

interface WorksheetRepository
{

    public function ofId(ParticipantCompositionId $participantCompositionId, string $worksheetId): Worksheet;

    public function all(ParticipantCompositionId $participantCompositionId, int $page, int $pageSize);
}
