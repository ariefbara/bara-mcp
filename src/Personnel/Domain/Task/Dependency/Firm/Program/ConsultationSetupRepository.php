<?php

namespace Personnel\Domain\Task\Dependency\Firm\Program;

use Personnel\Domain\Model\Firm\Program\ConsultationSetup;

interface ConsultationSetupRepository
{

    public function ofId(string $id): ConsultationSetup;
}
