<?php

namespace ActivityCreator\Application\Service\Consultant;

use ActivityCreator\Domain\DependencyModel\Firm\Personnel\Consultant;

interface ConsultantRepository
{

    public function aConsultantBelongsToPersonnel(string $firmId, string $personnelId, string $consultantId): Consultant;
}
