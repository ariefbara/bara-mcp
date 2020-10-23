<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\{
    Application\Service\Firm\Client\TeamMembershipRepository,
    Application\Service\Participant\MetricAssignment\MetricAssignmentReportRepository,
    Domain\Model\Participant\MetricAssignment\MetricAssignmentReportData
};

class UpdateMetricAssignmentReport
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

    public function __construct(MetricAssignmentReportRepository $metricAssignmentReportRepository,
            TeamMembershipRepository $teamMembershipRepository)
    {
        $this->metricAssignmentReportRepository = $metricAssignmentReportRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
    }

    public function execute(
            string $firmId, string $teamId, string $clientId, string $metricAssignmentReportId,
            MetricAssignmentReportData $metricAssignmentReportData): void
    {
        $metricAssignmentReport = $this->metricAssignmentReportRepository->ofId($metricAssignmentReportId);
        $this->teamMembershipRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->updateMetricAssignmentReport($metricAssignmentReport, $metricAssignmentReportData);
        $this->metricAssignmentReportRepository->update();
    }

}
