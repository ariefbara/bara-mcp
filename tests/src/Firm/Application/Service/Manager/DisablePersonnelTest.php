<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\ {
    Manager,
    Personnel
};
use Tests\TestBase;

class DisablePersonnelTest extends TestBase
{
    protected $personnelRepository, $personnel;
    protected $managerRepository, $manager;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $personnelId = "personnelId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->personnelRepository = $this->buildMockOfInterface(PersonnelRepository::class);
        $this->personnelRepository->expects($this->any())
                ->method("aPersonnelOfId")
                ->with($this->personnelId)
                ->willReturn($this->personnel);
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);
        
        $this->service = new DisablePersonnel($this->personnelRepository, $this->managerRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->personnelId);
    }
    public function test_execute_disablePersonnelByManager()
    {
        $this->manager->expects($this->once())
                ->method("disablePersonnel")
                ->with($this->personnel);
        $this->execute();
    }
    public function test_execute_updatePersonnelRepository()
    {
        $this->personnelRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}