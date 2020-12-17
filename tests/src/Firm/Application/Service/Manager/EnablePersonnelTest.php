<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Personnel;
use Tests\TestBase;
use function any;

class EnablePersonnelTest extends TestBase
{
    protected $personnelRepository;
    protected $managerRepository, $manager;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $personnelId = "personnelId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->personnelRepository = $this->buildMockOfInterface(PersonnelRepository::class);
        
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);
        
        $this->service = new EnablePersonnel($this->personnelRepository, $this->managerRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->personnelId);
    }
    public function test_execute_managerEnablePersonnel()
    {
        $this->personnelRepository->expects($this->once())
                ->method("aPersonnelOfId")
                ->with($this->personnelId);
        $this->manager->expects($this->once())
                ->method("enablePersonnel");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->personnelRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
