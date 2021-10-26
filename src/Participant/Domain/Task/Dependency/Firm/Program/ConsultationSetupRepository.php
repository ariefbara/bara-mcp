<?php

namespace Participant\Domain\Task\Dependency\Firm\Program;

use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;

interface ConsultationSetupRepository
{

    public function ofId(string $consultationSetupId): ConsultationSetup;
}
