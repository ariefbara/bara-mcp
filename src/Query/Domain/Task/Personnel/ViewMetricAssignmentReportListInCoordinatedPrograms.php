<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\MetricAssignment\MetricAssignmentReportRepository;

class ViewMetricAssignmentReportListInCoordinatedPrograms implements PersonnelTask
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
     * @param CommonViewListPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->metricAssignmentReportRepository
                ->listInProgramsCoordinatedByPersonnel($personnelId, $payload->getFilter());
    }

}
