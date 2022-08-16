<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\TaskInFirmExecutableByManager;
use Tests\TestBase;

class ExecuteTaskInFirmTest extends TestBase
{
    protected $managerRepository;
    protected $manager;
    protected $service;
    
    protected $firmId = 'firmId', $managerId = 'managerId';
    protected $firmTask, $payload = 'string represent task payload';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->manager = $this->buildMockOfClass(Manager::class);
        
        $this->service = new ExecuteTaskInFirm($this->managerRepository);
        $this->firmTask = $this->buildMockOfInterface(TaskInFirmExecutableByManager::class);
    }
    
    protected function execute()
    {
        $this->managerRepository->expects($this->any())
                ->method('aManagerInFirm')
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);
        $this->service->execute($this->firmId, $this->managerId, $this->firmTask, $this->payload);
    }
    public function test_execute_managerExecuteTaskInFirm()
    {
        $this->manager->expects($this->once())
                ->method('executeTaskInFirm')
                ->with($this->firmTask, $this->payload);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->managerRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
