<?php

namespace Firm\Application\Service\Firm;

use Firm\ {
    Application\Service\Manager\ProgramRepository as InterfaceForManager,
    Domain\Model\Firm\Program
};

interface ProgramRepository extends InterfaceForManager
{

    public function nextIdentity(): string;

    public function add(Program $program): void;

    public function update(): void;

    public function ofId(string $firmId, string $programId): Program;
}
