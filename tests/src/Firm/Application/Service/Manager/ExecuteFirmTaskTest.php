<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\FirmTaskExecutableByManager;
use Tests\src\Firm\Application\Service\Manager\ManagerTestBase;

class ExecuteFirmTaskTest extends ManagerTestBase
{
    protected $service;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExecuteFirmTask($this->managerRepository);
        $this->task = $this->buildMockOfInterface(FirmTaskExecutableByManager::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->task);
    }
    public function test_execute_managerExecuteFirmTask()
    {
        $this->manager->expects($this->once())
                ->method('executeFirmTask')
                ->with($this->task);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->managerRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
