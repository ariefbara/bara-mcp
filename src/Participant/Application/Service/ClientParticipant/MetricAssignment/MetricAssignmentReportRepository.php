<?php

namespace Participant\Application\Service\ClientParticipant\MetricAssignment;

use Participant\Domain\Model\Participant\MetricAssignment\MetricAssignmentReport;

interface MetricAssignmentReportRepository
{

    public function nextIdentity(): string;

    public function add(MetricAssignmentReport $metricAssignmentReport): void;

    public function aMetricAssignmentReportBelongsToClient(string $clientId, string $metricAssignmentReportId): MetricAssignmentReport;

    public function update(): void;
}
