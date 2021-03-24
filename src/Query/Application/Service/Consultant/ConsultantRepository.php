<?php

namespace Query\Application\Service\Consultant;

use Query\Domain\Model\Firm\Program\Consultant;

interface ConsultantRepository
{

    public function aConsultantBelongsToPersonnel(string $firmId, string $personnelId, string $consultantId): Consultant;
}
