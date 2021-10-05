<?php

namespace Query\Application\Service\Manager;

use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\Model\Firm\Manager;
use Tests\src\Query\Application\Service\Manager\ManagerServiceTestBase;

class ExecuteTaskInFirmTest extends ManagerServiceTestBase
{
    protected $managerRepository, $manager, $firmId = 'firm-id', $managerId = 'manager-id';
    protected $service;
    
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method('aManagerInFirm')
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);
        
        $this->service = new ExecuteTaskInFirm($this->managerRepository);
        
        $this->task = $this->buildMockOfInterface(ITaskInFirmExecutableByManager::class);
    }
    
    public function test_execute_managerExecuteTask()
    {
        $this->manager->expects($this->once())
                ->method('executeTaskInFirm')
                ->with($this->task);
        $this->service->execute($this->firmId, $this->managerId, $this->task);
    }
}
