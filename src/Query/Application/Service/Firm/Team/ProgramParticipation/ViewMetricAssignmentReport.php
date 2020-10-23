<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;

class ViewMetricAssignmentReport
{

    /**
     *
     * @var MetricAssignmentReportRepository
     */
    protected $metricAssignmentReportRepository;

    public function __construct(MetricAssignmentReportRepository $metricAssignmentReportRepository)
    {
        $this->metricAssignmentReportRepository = $metricAssignmentReportRepository;
    }

    /**
     * 
     * @param string $teamId
     * @param string $teamProgramParticipationId
     * @param int $page
     * @param int $pageSize
     * @return MetricAssignmentReport[]
     */
    public function showAll(string $teamId, string $teamProgramParticipationId, int $page, int $pageSize)
    {
        return $this->metricAssignmentReportRepository->allMetricAssignmentReportsInProgramParticipationBelongsToTeam(
                        $teamId, $teamProgramParticipationId, $page, $pageSize);
    }

    public function showById(string $teamId, string $metricAssignmentReportId): MetricAssignmentReport
    {
        return $this->metricAssignmentReportRepository
                        ->aMetricAssignmentReportBelongsToTeam($teamId, $metricAssignmentReportId);
    }

}
