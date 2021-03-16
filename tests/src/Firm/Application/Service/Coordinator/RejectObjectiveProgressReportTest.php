<?php

namespace Firm\Application\Service\Coordinator;

use Tests\src\Firm\Application\Service\Coordinator\ObjectiveProgressReportTestBase;

class RejectObjectiveProgressReportTest extends ObjectiveProgressReportTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RejectObjectiveProgressReport($this->coordinatorRepository, $this->objectiveProgressReportRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->programId, $this->objectiveProgressReportId);
    }
    public function test_execute_coordinatorRejectObjectiveProgressReport()
    {
        $this->coordinator->expects($this->once())
                ->method('rejectObjectiveProgressReport')
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
