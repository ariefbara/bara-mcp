<?php

namespace Firm\Application\Service\Coordinator;

use Tests\src\Firm\Application\Service\Coordinator\OKRPeriodTestBase;

class RejectOKRPeriodTest extends OKRPeriodTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RejectOKRPeriod($this->coordinatorRepository, $this->okrPeriodRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->personnelId, $this->programId, $this->okrPeriodId);
    }
    public function test_execute_coordinatorRejectOKRPeriod()
    {
        $this->coordinator->expects($this->once())
                ->method('rejectOKRPeriod')
                ->with($this->okrPeriod);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->coordinatorRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
    
}
