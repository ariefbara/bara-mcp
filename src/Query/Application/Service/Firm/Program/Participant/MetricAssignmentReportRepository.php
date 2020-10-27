<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\ {
    Application\Service\Firm\Client\ProgramParticipation\MetricAssignmentReportRepository as InterfaceForClient,
    Application\Service\Firm\Team\ProgramParticipation\MetricAssignmentReportRepository as InterfaceForTeam,
    Application\Service\User\ProgramParticipation\MetricAssignmentReportRepository as InterfaceForUser,
    Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport
};

interface MetricAssignmentReportRepository extends InterfaceForTeam, InterfaceForClient, InterfaceForUser
{

    public function aMetricAssignmentInProgram(string $programId, string $metricAssignmentReportId): MetricAssignmentReport;

    public function allMetricAssignmentsBelongsToParticipantInProgram(
            string $programId, string $participantId, int $page, int $pageSize);
}
