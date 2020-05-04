<?php

namespace Query\Application\Service\Firm\Program;

use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\Domain\Model\Firm\Program\RegistrationPhase;

interface RegistrationPhaseRepository
{

    public function ofId(ProgramCompositionId $programCompositionId, string $registrationPhaseId): RegistrationPhase;

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize);
}
