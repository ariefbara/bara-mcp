<?php

namespace Participant\Application\Service\UserParticipant\MetricAssignment;

use Participant\Domain\Model\Participant\MetricAssignment\MetricAssignmentReport;

interface MetricAssignmentReportRepository
{

    public function nextIdentity(): string;

    public function add(MetricAssignmentReport $metricAssignmentReport): void;
    
    public function aMetricAssignmentReportBelongsToUser(string $userId, string $metricAssignmentReportId): MetricAssignmentReport;
    
    public function update(): void;
}
