<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

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
     * @param string $clientId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @return MetricAssignmentReport[]
     */
    public function showAll(string $clientId, string $programParticipationId, int $page, int $pageSize)
    {
        return $this->metricAssignmentReportRepository->allMetricAssignmentReportsInProgramParticipationBelongsToClient(
                        $clientId, $programParticipationId, $page, $pageSize);
    }

    public function showById(string $clientId, string $metricAssignmentReportId): MetricAssignmentReport
    {
        return $this->metricAssignmentReportRepository
                        ->aMetricAssignmentReportBelongsToClient($clientId, $metricAssignmentReportId);
    }

}
