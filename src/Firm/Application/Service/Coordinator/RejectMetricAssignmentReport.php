<?php

namespace Firm\Application\Service\Coordinator;

class RejectMetricAssignmentReport
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
    protected $coordinatorRepository;

    function __construct(MetricAssignmentReportRepository $metricAssignmentReportRepository,
            CoordinatorRepository $coordinatorRepository)
    {
        $this->metricAssignmentReportRepository = $metricAssignmentReportRepository;
        $this->coordinatorRepository = $coordinatorRepository;
    }

    public function execute(
            string $firmId, string $personnelId, string $programId, string $metricAssignmentReportId, ?string $note): void
    {
        $metricAssignmentReport = $this->metricAssignmentReportRepository->ofId($metricAssignmentReportId);
        $this->coordinatorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                ->rejectMetricAssignmentReport($metricAssignmentReport, $note);
        $this->metricAssignmentReportRepository->update();
    }

}
