<?php

namespace Query\Application\Service\Coordinator;

use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;

interface ObjectiveProgressReportRepository
{

    public function anObjectiveProgressReportInProgram(string $programId, string $objectiveProgressReportId): ObjectiveProgressReport;

    public function allObjectiveProgressReportsBelongsToObjectiveInProgram(
            string $programId, string $objectiveId, int $page, int $pageSize);
}
