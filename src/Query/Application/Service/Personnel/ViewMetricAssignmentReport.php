<?php

namespace Query\Application\Service\Personnel;

use Query\Application\Service\Firm\Program\Participant\MetricAssignment\MetricAssignmentReportRepository;

class ViewMetricAssignmentReport
{

    /**
     * 
     * @var PersonnelRepository
     */
    protected $personnelRepository;

    /**
     * 
     * @var MetricAssignmentReportRepository
     */
    protected $metricAssignmentReportRepository;

    public function __construct(PersonnelRepository $personnelRepository,
            MetricAssignmentReportRepository $metricAssignmentReportRepository)
    {
        $this->personnelRepository = $personnelRepository;
        $this->metricAssignmentReportRepository = $metricAssignmentReportRepository;
    }

    public function showAll(string $firmId, string $personnelId, int $page, int $pageSize, ?bool $approvedStatus)
    {
        return $this->personnelRepository
                        ->aPersonnelInFirm($firmId, $personnelId)
                        ->viewAllAccesibleMetricAssignmentReports(
                                $this->metricAssignmentReportRepository, $page, $pageSize, $approvedStatus);
    }

}
