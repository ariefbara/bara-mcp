<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Consultant;

interface ConsultantRepository
{

    public function update(): void;

    public function ofId(ProgramCompositionId $programCompositionId, string $consultantId): Consultant;
    
    public function aConsultantOfId(string $consultantId): Consultant;
}
