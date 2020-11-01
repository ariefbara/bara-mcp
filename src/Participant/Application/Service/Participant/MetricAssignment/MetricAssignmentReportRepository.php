<?php

namespace Participant\Application\Service\Participant\MetricAssignment;

use Participant\ {
    Application\Service\ClientParticipant\MetricAssignmentReportRepository as InterfaceForClient,
    Application\Service\UserParticipant\MetricAssignment\MetricAssignmentReportRepository as InterfaceForUser,
    Domain\Model\Participant\MetricAssignment\MetricAssignmentReport
};

interface MetricAssignmentReportRepository extends InterfaceForClient, InterfaceForUser
{

    public function nextIdentity(): string;

    public function add(MetricAssignmentReport $metricAssignmentReport): void;

    public function ofId(string $metricAssignmentReportId): MetricAssignmentReport;
    
    public function update(): void;
}
