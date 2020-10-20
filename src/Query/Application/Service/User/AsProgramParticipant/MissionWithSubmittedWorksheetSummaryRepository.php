<?php

namespace Query\Application\Service\User\AsProgramParticipant;

interface MissionWithSubmittedWorksheetSummaryRepository
{

    public function allMissionInProgramIncludeSubmittedWorksheetFromUser(
            string $programId, string $userId, int $page, int $pageSize): array;

    public function getTotalMissionInProgram(string $programId): int;
}
