<?php

namespace Personnel\Application\Service\Firm\Program\Participant;

use Personnel\{
    Application\Service\Firm\Personnel\PersonnelCompositionId,
    Domain\Model\Firm\Program\Participant\Worksheet
};

interface WorksheetRepository
{

    public function aWorksheetInProgramsWhereConsultantInvolved(
            PersonnelCompositionId $personnelCompositionId, string $programConsultantId, string $participantId,
            string $worksheetId): Worksheet;
}
