<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\Domain\Model\Participant\MetricAssignment;

interface MetricAssignmentRepository
{

    public function aMetricAssignmentBelongsToUser(string $userId, string $metricAssignmentId): MetricAssignment;
}
