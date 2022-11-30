<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant\MetricAssignment;

use Query\Domain\Task\Dependency\PaginationFilter;

interface MetricAssignmentReportRepository
{

    public function unreviewedMetricReportListInProgramsCoordinatedByPersonnel(
            string $personnelId, PaginationFilter $paginationFilter);

    public function listInProgramsCoordinatedByPersonnel(
            string $personnelId, MetricAssignmentReportListFilterForCoordinator $filter);

    public function listInProgramsConsultedByPersonnel(
            string $personnelId, MetricAssignmentReportListFilterForConsultant $filter);
}
