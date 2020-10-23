<?php

namespace Participant\Application\Service\Participant\MetricAssignment;

use Participant\Domain\Model\Participant\MetricAssignment\MetricAssignmentReport;

interface MetricAssignmentReportRepository
{

    public function nextIdentity(): string;

    public function add(MetricAssignmentReport $metricAssignmentReport): void;

    public function ofId(string $metricAssignmentReportId): MetricAssignmentReport;
    
    public function update(): void;
}
