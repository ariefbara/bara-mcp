<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Registrant;

interface RegistrantRepository
{

    public function update(): void;

    public function ofId(ProgramCompositionId $programCompositionId, string $registrantId): Registrant;
}
