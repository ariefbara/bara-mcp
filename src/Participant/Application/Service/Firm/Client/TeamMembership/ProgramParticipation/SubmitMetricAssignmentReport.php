<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use DateTimeImmutable;
use Participant\{
    Application\Service\Firm\Client\TeamMembershipRepository,
    Application\Service\Participant\MetricAssignment\MetricAssignmentReportRepository,
    Application\Service\Participant\MetricAssignmentRepository,
    Domain\Model\Participant\MetricAssignment\MetricAssignmentReportData
};

class SubmitMetricAssignmentReport
{

    /**
     *
     * @var MetricAssignmentReportRepository
     */
    protected $metricAssignmentReportRepository;

    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;

    /**
     *
     * @var MetricAssignmentRepository
     */
    protected $metricAssignmentRepository;

    public function __construct(MetricAssignmentReportRepository $metricAssignmentReportRepository,
            TeamMembershipRepository $teamMembershipRepository, MetricAssignmentRepository $metricAssignmentRepository)
    {
        $this->metricAssignmentReportRepository = $metricAssignmentReportRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->metricAssignmentRepository = $metricAssignmentRepository;
    }

    public function execute(
            string $firmId, string $teamId, string $clientId, string $metricAssignmentId,
            DateTimeImmutable $observeTime, MetricAssignmentReportData $metricAssignmentReportData): string
    {
        $metricAssignment = $this->metricAssignmentRepository->ofId($metricAssignmentId);
        $id = $this->metricAssignmentReportRepository->nextIdentity();
        $metricAssignmentReport = $this->teamMembershipRepository
                ->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->submitReportInMetricAssignment($metricAssignment, $id, $observeTime, $metricAssignmentReportData);
        $this->metricAssignmentReportRepository->add($metricAssignmentReport);
        return $id;
    }

}
