<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\RegistrationPhase;

interface RegistrationPhaseRepository
{

    public function nextIdentity(): string;

    public function add(RegistrationPhase $registrationPhase): void;

    public function update(): void;

    public function ofId(ProgramCompositionId $programCompositionId, string $registrationPhaseId): RegistrationPhase;
}
