<?php

namespace Firm\Application\Service\Coordinator;

class ApproveMetricAssignmentReport
{

    /**
     *
     * @var MetricAssignmentReportRepository
     */
    protected $metricAssignmentReportRepository;

    /**
     *
     * @var CoordinatorRepository
     */
    protected $coordiantorRepository;
    
    function __construct(MetricAssignmentReportRepository $metricAssignmentReportRepository,
            CoordinatorRepository $coordiantorRepository)
    {
        $this->metricAssignmentReportRepository = $metricAssignmentReportRepository;
        $this->coordiantorRepository = $coordiantorRepository;
    }
    
    public function execute(string $firmId, string $personnelId, string $programId, string $metricAssignmentReportId): void
    {
        $metricAssignmentReport = $this->metricAssignmentReportRepository->ofId($metricAssignmentReportId);
        $this->coordiantorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                ->approveMetricAssignmentReport($metricAssignmentReport);
        $this->metricAssignmentReportRepository->update();
    }


}
