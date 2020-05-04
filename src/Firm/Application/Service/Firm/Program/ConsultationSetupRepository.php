<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\ConsultationSetup;

interface ConsultationSetupRepository
{

    public function nextIdentity(): string;

    public function add(ConsultationSetup $consultationSetup): void;

    public function update(): void;

    public function ofId(ProgramCompositionId $programCompositionId, string $consultationSetupId): ConsultationSetup;
}
