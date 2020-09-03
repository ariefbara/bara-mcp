<?php

namespace Query\Application\Service\Firm\Program;

use Query\ {
    Application\Service\Firm\Personnel\ProgramConsultationRepository as InterfaceForPersonnel,
    Domain\Model\Firm\Program\Consultant
};

interface ConsultantRepository extends InterfaceForPersonnel
{

    public function ofId(string $firmId, string $programId, string $consultantId): Consultant;

    public function all(string $firmId, string $programId, int $page, int $pageSize);
}
