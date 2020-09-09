<?php

namespace Personnel\Application\Service\Firm\Program\Participant;

use Personnel\Domain\Model\Firm\Program\Participant\Worksheet;

interface WorksheetRepository
{

    public function aWorksheetInProgramsWhereConsultantInvolved(
            string $firmId, string $personnelId, string $programConsultationId, string $participantId,
            string $worksheetId): Worksheet;
}
