<?php

namespace Query\Application\Service\Firm\Program;

interface ParticipantSummaryRepository
{

    public function allParticipantsSummaryInProgram(string $programId, int $page, int $pageSize): array;

    public function getTotalActiveParticipantInProgram(string $programId): int;
}
