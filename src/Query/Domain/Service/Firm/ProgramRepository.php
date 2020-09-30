<?php

namespace Query\Domain\Service\Firm;

use Query\Domain\Model\Firm\Program;

interface ProgramRepository
{
    public function ofId(string $firmId, string $programId): Program;
    
    public function all(string $firmId, int $page, int $pageSize, ?string $participantType);
}
