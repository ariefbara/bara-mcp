<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;

interface MetricAssignmentReportRepository
{

    public function aMetricAssignmentReportBelongsToClient(string $clientId, string $metricAssignmentReportId): MetricAssignmentReport;

    public function allMetricAssignmentReportsInProgramParticipationBelongsToClient(
            string $clientId, string $programParticipationId, int $page, int $pageSize);
}
