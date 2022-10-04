<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\Dependency\Firm\Program\Participant\MetricAssignment\MetricAssignmentReportRepository;

class ViewUnreviewedMetricReportListInCoordinatedProgram implements PersonnelTask
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
     * @param string $personnelId
     * @param ViewUnreviewedMetricReportListInCoordinatedProgramPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->metricAssignmentReportRepository
                ->unreviewedMetricReportListInProgramsCoordinatedByPersonnel($personnelId,
                $payload->getPaginationFilter());
    }

}
