<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Registrant;

interface RegistrantRepository
{
    public function ofId(string $firmId, string $programId, string $registrantId): Registrant;
    
    public function update(): void;
}
