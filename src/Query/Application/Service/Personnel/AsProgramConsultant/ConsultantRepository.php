<?php

namespace Query\Application\Service\Personnel\AsProgramConsultant;

use Query\Domain\Model\Firm\Program\Consultant;

interface ConsultantRepository
{
    public function aConsultantCorrepondWithProgram(string $firmId, string $personnelId, string $programId): Consultant;
}
