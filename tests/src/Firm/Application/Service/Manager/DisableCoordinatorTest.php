<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\ {
    Manager,
    Program\Coordinator
};
use Tests\TestBase;

class DisableCoordinatorTest extends TestBase
{
    protected $coordinatorRepository, $coordinator;
    protected $managerRepository, $manager;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $coordinatorId = "coordinatorId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository = $this->buildMockOfClass(CoordinatorRepository::class);
        $this->coordinatorRepository->expects($this->any())
                ->method("aCoordinatorOfId")
                ->with($this->coordinatorId)
                ->willReturn($this->coordinator);
        
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfClass(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);
        
        $this->service = new DisableCoordinator($this->coordinatorRepository, $this->managerRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->coordinatorId);
    }
    public function test_execute_disableCoordinatorInManager()
    {
        $this->manager->expects($this->once())
                ->method("disableCoordinator")
                ->with($this->coordinator);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->coordinatorRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
