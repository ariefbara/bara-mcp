<?php

namespace Query\Application\Service\Firm\Program\Participant;

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
     * @param string $programId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @return MetricAssignmentReport[]
     */
    public function showAll(string $programId, string $participantId, int $page, int $pageSize)
    {
        return $this->metricAssignmentReportRepository->allMetricAssignmentsBelongsToParticipantInProgram(
                        $programId, $participantId, $page, $pageSize);
    }

    public function showById(string $programId, string $metricAssignmentReportId): MetricAssignmentReport
    {
        return $this->metricAssignmentReportRepository
                        ->aMetricAssignmentInProgram($programId, $metricAssignmentReportId);
    }

}
