<?php

namespace Query\Application\Service\User\ProgramParticipation;

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
     * @param string $userId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @return MetricAssignmentReport[]
     */
    public function showAll(string $userId, string $programParticipationId, int $page, int $pageSize)
    {
        return $this->metricAssignmentReportRepository->allMetricAssignmentReportsInProgramParticipationBelongsToUser(
                        $userId, $programParticipationId, $page, $pageSize);
    }

    public function showById(string $userId, string $metricAssignmentReportId): MetricAssignmentReport
    {
        return $this->metricAssignmentReportRepository
                        ->aMetricAssignmentReportBelongsToUser($userId, $metricAssignmentReportId);
    }

}
