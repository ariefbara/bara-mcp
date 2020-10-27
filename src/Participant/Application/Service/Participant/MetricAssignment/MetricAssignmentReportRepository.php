<?php

namespace Participant\Application\Service\Participant\MetricAssignment;

use Participant\ {
    Application\Service\ClientParticipant\MetricAssignment\MetricAssignmentReportRepository as InterfaceForClient,
    Domain\Model\Participant\MetricAssignment\MetricAssignmentReport
};

interface MetricAssignmentReportRepository extends InterfaceForClient
{

    public function nextIdentity(): string;

    public function add(MetricAssignmentReport $metricAssignmentReport): void;

    public function ofId(string $metricAssignmentReportId): MetricAssignmentReport;
    
    public function update(): void;
}
