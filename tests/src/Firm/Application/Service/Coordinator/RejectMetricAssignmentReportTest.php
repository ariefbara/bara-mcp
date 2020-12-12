<?php

namespace Firm\Application\Service\Coordinator;

use Firm\Domain\Model\Firm\Program\Coordinator;
use Firm\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;
use Tests\TestBase;

class RejectMetricAssignmentReportTest extends TestBase
{
    protected $metricAssignmentReportRepository, $metricAssignmentReport;
    protected $coordinatorRepository, $coordinator;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $programId = "programId", 
            $metricAssignmentReportId = "metricAssignmentReportId";
    protected $note = "new note";

    protected function setUp(): void
    {
        parent::setUp();
        $this->metricAssignmentReport = $this->buildMockOfClass(MetricAssignmentReport::class);
        $this->metricAssignmentReportRepository = $this->buildMockOfInterface(MetricAssignmentReportRepository::class);
        $this->metricAssignmentReportRepository->expects($this->any())
                ->method("ofId")
                ->with($this->metricAssignmentReportId)
                ->willReturn($this->metricAssignmentReport);
        
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinatorRepository->expects($this->any())
                ->method("aCoordinatorCorrespondWithProgram")
                ->with($this->firmId, $this->personnelId, $this->programId)
                ->willReturn($this->coordinator);
        
        $this->service = new RejectMetricAssignmentReport($this->metricAssignmentReportRepository, $this->coordinatorRepository);
    }
    
    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->personnelId, $this->programId, $this->metricAssignmentReportId, $this->note);
    }
    public function test_execute_coordinatorRejectMetricAssignmentReport()
    {
        $this->coordinator->expects($this->once())
                ->method("rejectMetricAssignmentReport")
                ->with($this->metricAssignmentReport, $this->note);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->metricAssignmentReportRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
