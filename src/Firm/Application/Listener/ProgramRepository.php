<?php

namespace Firm\Application\Listener;

use Firm\Domain\Model\Firm\Program;

interface ProgramRepository
{

    public function aProgramOfId(string $programId): Program;

    public function update(): void;
}
