<?php

namespace Participant\Application\Service\Participant;

use Participant\ {
    Application\Service\ClientParticipant\MetricAssignmentRepository as InterfaceForClient,
    Domain\Model\Participant\MetricAssignment
};

interface MetricAssignmentRepository extends InterfaceForClient
{
    public function ofId(string $metricAssignmentId): MetricAssignment;
}
