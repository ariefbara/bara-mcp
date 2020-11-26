<?php

namespace Firm\Application\Service\Coordinator;

use Firm\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;

interface MetricAssignmentReportRepository
{

    public function ofId(string $metricAssignmentReportId): MetricAssignmentReport;

    public function update(): void;
}
