<?php

namespace Firm\Application\Service\Personnel\ProgramConsultant;

use Firm\Domain\Model\Firm\Program\Consultant;

interface ProgramConsultantRepository
{

    public function aConsultantCorrespondWithProgram(string $firmId, string $personnelId, string $programId): Consultant;
}
