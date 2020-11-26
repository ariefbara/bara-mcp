<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\ {
    Application\Service\Personnel\ProgramConsultant\ProgramConsultantRepository as InterfaceForPersonnel,
    Domain\Model\Firm\Program\Consultant
};

interface ConsultantRepository extends InterfaceForPersonnel
{

    public function update(): void;

    public function ofId(ProgramCompositionId $programCompositionId, string $consultantId): Consultant;
    
    public function aConsultantOfId(string $consultantId): Consultant;
}
