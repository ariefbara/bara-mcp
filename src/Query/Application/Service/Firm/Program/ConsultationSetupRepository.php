<?php

namespace Query\Application\Service\Firm\Program;

use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\Domain\Model\Firm\Program\ConsultationSetup;

interface ConsultationSetupRepository
{

    public function ofId(ProgramCompositionId $programCompositionId, string $consultationSetupId): ConsultationSetup;

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize);
}
