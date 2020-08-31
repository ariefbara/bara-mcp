<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\DependencyEntity\Firm\Program\Consultant;

interface ConsultantRepository
{

    public function ofId(string $firmId, string $programId, string $personnelId): Consultant;
}
