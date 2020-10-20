<?php

namespace Query\Application\Service\Firm\Client\AsProgramParticipant;

interface MissionWithSubmittedWorksheetSummaryRepository
{
    public function allMissionInProgramIncludeSubmittedWorksheetFromClient(
            string $programId, string $clientId, int $page, int $pageSize): array;

    public function getTotalMissionInProgram(string $programId): int;
}
