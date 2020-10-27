<?php

namespace Query\Application\Service\User\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;

interface MetricAssignmentReportRepository
{

    public function aMetricAssignmentReportBelongsToUser(string $userId, string $metricAssignmentReportId): MetricAssignmentReport;

    public function allMetricAssignmentReportsInProgramParticipationBelongsToUser(
            string $userId, string $programParticipationId, int $page, int $pageSize);
}
