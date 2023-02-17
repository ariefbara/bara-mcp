<?php

namespace Query\Domain\Task\Dependency\Firm\Program;

interface ConsultantRepository
{

    public function consultantSummaryListInProgram(string $programId): array;
}
