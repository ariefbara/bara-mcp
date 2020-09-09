<?php

namespace Personnel\Application\Service\Firm\Personnel;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

interface ProgramConsultantRepository
{

    public function update(): void;

    public function ofId(string $firmId, string $personnelId, string $programConsultationId): ProgramConsultant;
}
