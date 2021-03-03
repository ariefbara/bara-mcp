<?php

namespace Query\Domain\Service;

use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;

interface ObjectiveProgressReportRepository
{

    public function anObjectiveProgressReportBelongsToParticipant(
            string $participantId, string $objectiveProgressReportId): ObjectiveProgressReport;

    public function allObjectiveProgressReportsInObjectiveBelongsToParticipant(
            string $participantId, string $objectiveId, int $page, int $pageSize);
}
