<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\Program;

interface ProgramRepository
{

    public function nextIdentity(): string;

    public function add(Program $program): void;

    public function update(): void;

    public function ofId(string $firmId, string $programId): Program;
}
