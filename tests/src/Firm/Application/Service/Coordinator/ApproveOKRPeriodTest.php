<?php

namespace Firm\Application\Service\Coordinator;

use Tests\src\Firm\Application\Service\Coordinator\OKRPeriodTestBase;

class ApproveOKRPeriodTest extends OKRPeriodTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ApproveOKRPeriod($this->coordinatorRepository, $this->okrPeriodRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->personnelId, $this->programId, $this->okrPeriodId);
    }
    public function test_execute_coordinatorApproveOKRPeriod()
    {
        $this->coordinator->expects($this->once())
                ->method('approveOKRPeriod')
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
