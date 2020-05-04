<?php

namespace Query\Application\Service\Firm\Program;

use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\Domain\Model\Firm\Program\Registrant;

interface RegistrantRepository
{

    public function ofId(ProgramCompositionId $programCompositionId, string $registrantId): Registrant;

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize);
}
