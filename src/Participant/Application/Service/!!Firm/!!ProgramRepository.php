<?php

namespace Participant\Application\Service\Firm;

use User\Domain\Model\Firm\Program;

interface ProgramRepository
{
    public function ofId(string $firmId, string $programId): Program;
}
