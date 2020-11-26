<?php

namespace Firm\Application\Service\Coordinator;

use Firm\Domain\Model\Firm\Program\ {
    Coordinator,
    Participant\MetricAssignment\MetricAssignmentReport
};
use Tests\TestBase;

class ApproveMetricAssignmentReportTest extends TestBase
{
    protected $metricAssignmentReportRepository, $metricAssignmentReport;
    protected $coordinatorRepository, $coordinator;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $programId = "programId", 
            $metricAssignmentReportId = "metricAssignmentReportId";
    
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
        
        $this->service = new ApproveMetricAssignmentReport($this->metricAssignmentReportRepository, $this->coordinatorRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->programId, $this->metricAssignmentReportId);
    }
    public function test_execute_coordinatorApproveReport()
    {
        $this->coordinator->expects($this->once())
                ->method("approveMetricAssignmentReport")
                ->with($this->metricAssignmentReport);
        $this->execute();
    }
    public function test_execute_updateMetricAssignmentReportRepository()
    {
        $this->metricAssignmentReportRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
