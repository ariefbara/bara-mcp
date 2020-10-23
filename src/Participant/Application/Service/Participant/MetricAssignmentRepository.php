<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\Participant\MetricAssignment;

interface MetricAssignmentRepository
{
    public function ofId(string $metricAssignmentId): MetricAssignment;
}
