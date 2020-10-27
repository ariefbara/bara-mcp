<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\Domain\Model\Participant\MetricAssignment;

interface MetricAssignmentRepository
{
    public function aMetricAssignmentBelongsToClient(string $clientId, string $metricAssignmentId): MetricAssignment;
}
