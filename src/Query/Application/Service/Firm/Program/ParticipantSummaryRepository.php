<?php

namespace Query\Application\Service\Firm\Program;

interface ParticipantSummaryRepository
{

    public function allParticipantsSummaryInProgram(string $programId, int $page, int $pageSize): array;

    public function getTotalActiveParticipantInProgram(string $programId): int;

    public function allParticipantAchievmentSummaryInProgram(
            string $firmId, string $programId, int $page, int $pageSize, string $orderType = "DESC"): array;

    public function allParticipantEvaluationSummaryInProgram(
            string $firmId, string $programId, int $page, int $pageSize): array;
}
