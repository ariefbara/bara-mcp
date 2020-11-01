<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use DateTimeImmutable;
use Participant\{
    Application\Service\Firm\Client\TeamMembership\TeamProgramParticipationRepository,
    Application\Service\Firm\Client\TeamMembershipRepository,
    Application\Service\Participant\MetricAssignment\MetricAssignmentReportRepository,
    Domain\Service\MetricAssignmentReportDataProvider
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
     * @var TeamProgramParticipationRepository
     */
    protected $teamPrograParticipationRepository;

    function __construct(MetricAssignmentReportRepository $metricAssignmentReportRepository,
            TeamMembershipRepository $teamMembershipRepository,
            TeamProgramParticipationRepository $teamPrograParticipationRepository)
    {
        $this->metricAssignmentReportRepository = $metricAssignmentReportRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->teamPrograParticipationRepository = $teamPrograParticipationRepository;
    }

    public function execute(
            string $firmId, string $teamId, string $clientId, string $teamProgramParticipationId,
            DateTimeImmutable $observationTime, MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): string
    {
        $teamProgramParticipation = $this->teamPrograParticipationRepository->ofId($teamProgramParticipationId);
        $id = $this->metricAssignmentReportRepository->nextIdentity();
        $metricAssignmentReport = $this->teamMembershipRepository
                ->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->submitMetricAssignmentReport(
                        $teamProgramParticipation, $id, $observationTime, $metricAssignmentReportDataProvider);
        $this->metricAssignmentReportRepository->add($metricAssignmentReport);
        return $id;
    }

}
