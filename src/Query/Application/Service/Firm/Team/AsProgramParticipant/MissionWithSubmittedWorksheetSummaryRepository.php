<?php

namespace Query\Application\Service\Firm\Team\AsProgramParticipant;

interface MissionWithSubmittedWorksheetSummaryRepository
{

    public function allMissionInProgramIncludeSubmittedWorksheetFromTeam(
            string $programId, string $teamId, int $page, int $pageSize): array;

    public function getTotalMissionInProgram(string $programId): int;
}
