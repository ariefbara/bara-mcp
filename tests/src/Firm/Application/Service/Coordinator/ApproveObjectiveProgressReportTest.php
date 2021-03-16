<?php

use Firm\Application\Service\Coordinator\ApproveObjectiveProgressReport;
use Tests\src\Firm\Application\Service\Coordinator\ObjectiveProgressReportTestBase;

class ApproveObjectiveProgressReportTest extends ObjectiveProgressReportTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ApproveObjectiveProgressReport($this->coordinatorRepository, $this->objectiveProgressReportRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->programId, $this->objectiveProgressReportId);
    }
    public function test_execute_coordinatorApproveObjectiveProgressReport()
    {
        $this->coordinator->expects($this->once())
                ->method('approveObjectiveProgressReport')
                ->with($this->objectiveProgressReport);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->coordinatorRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
