<?php

namespace Participant\Application\Service\Participant;

use Participant\ {
    Application\Service\ClientParticipant\MetricAssignmentRepository as InterfaceForClient,
    Application\Service\UserParticipant\MetricAssignmentRepository as InterfaceForUser,
    Domain\Model\Participant\MetricAssignment
};

interface MetricAssignmentRepository extends InterfaceForClient, InterfaceForUser
{
    public function ofId(string $metricAssignmentId): MetricAssignment;
}
