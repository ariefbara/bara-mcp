<?php

namespace Personnel\Application\Service\Firm\Personnel;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

interface ProgramConsultantRepository
{
    public function update(): void;

    public function ofId(PersonnelCompositionId $personnelCompositionId, string $programConsultantId): ProgramConsultant;

    public function all(PersonnelCompositionId $personnelCompositionId, int $page, int $pageSize);
}
