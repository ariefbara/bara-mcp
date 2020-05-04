<?php

namespace Query\Application\Service\Firm\Program;

use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\Domain\Model\Firm\Program\Consultant;

interface ConsultantRepository
{

    public function ofId(ProgramCompositionId $programCompositionId, string $consultantId): Consultant;

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize);
}
