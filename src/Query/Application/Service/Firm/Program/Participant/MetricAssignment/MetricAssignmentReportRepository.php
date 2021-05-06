<?php

namespace Query\Application\Service\Firm\Program\Participant\MetricAssignment;

interface MetricAssignmentReportRepository
{
    public function allMetricAssignmentReportsAccessibleByPersonnel(
            string $personnelId, int $page, int $pageSize, ?bool $approvedStatus);
}
