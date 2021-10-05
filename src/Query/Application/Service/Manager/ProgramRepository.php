<?php

namespace Query\Application\Service\Manager;

use Query\Domain\Model\Firm\Program;

interface ProgramRepository
{

    public function aProgramOfId(string $id): Program;
}
