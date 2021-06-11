<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\MutationTaskExecutableByManager;
use Tests\src\Firm\Application\Service\Manager\ManagerTestBase;

class HandleMutationTaskTest extends ManagerTestBase
{
    protected $service;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task = $this->buildMockOfInterface(MutationTaskExecutableByManager::class);
        $this->service = new HandleMutationTask($this->managerRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->task);
    }
    public function test_execute_managerHandleMutationTask()
    {
        $this->manager->expects($this->once())
                ->method('handleMutationTask')
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
