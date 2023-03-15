<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\ManagerTaskInFirm;
use Tests\src\Firm\Application\Service\Manager\ManagerTestBase;

class ExecuteTaskInFirmTest extends ManagerTestBase
{
    protected $service;
    //
    protected $task, $payload = 'task payload';

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExecuteTaskInFirm($this->managerRepository);
        //
        $this->task = $this->buildMockOfInterface(ManagerTaskInFirm::class);
    }
    
    //
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->task, $this->payload);
    }
    public function test_execute_managerExecuteTaskInFirm()
    {
        $this->manager->expects($this->once())
                ->method('executeTaskInFirm')
                ->with($this->task, $this->payload);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->managerRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
