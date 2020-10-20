<?php

namespace Query\Application\Service\Firm\Program;

interface ConsultantSummaryRepository
{

    public function allConsultantSummaryInProgram(string $programId, int $page, int $pageSize): array;

    public function getTotalActiveConsultantInProgram(string $programId): int;
}
