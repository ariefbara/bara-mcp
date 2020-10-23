<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;

interface MetricAssignmentReportRepository
{

    public function aMetricAssignmentReportBelongsToTeam(string $teamId, string $metricAssignmentReportId): MetricAssignmentReport;

    public function allMetricAssignmentReportsInProgramParticipationBelongsToTeam(
            string $teamId, string $teamProgramParticipationId, int $page, int $pageSize);
}
