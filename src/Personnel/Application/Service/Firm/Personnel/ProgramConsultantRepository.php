<?php

namespace Personnel\Application\Service\Firm\Personnel;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Query\Application\Service\Firm\Personnel\PersonnelCompositionId;

interface ProgramConsultantRepository
{

    public function update(): void;

    public function ofId(PersonnelCompositionId $personnelCompositionId, string $programConsultantId): ProgramConsultant;
}
