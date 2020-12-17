<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Manager;
use Tests\TestBase;

class ChangeMissionWorksheetFormTest extends TestBase
{
    protected $missionRepository;
    protected $managerRepository, $manager;
    protected $worksheetFormRepository;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $missionId = "missionId", $worksheetFormId = "worksheetFormId";
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);
        
        $this->worksheetFormRepository = $this->buildMockOfInterface(WorksheetFormRepository::class);
        
        $this->service = new ChangeMissionWorksheetForm(
                $this->missionRepository, $this->managerRepository, $this->worksheetFormRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->missionId, $this->worksheetFormId);
    }
    public function test_execute_managerChangeMissionWorksheetForm()
    {
        $this->missionRepository->expects($this->once())->method("aMissionOfId")->with($this->missionId);
        $this->worksheetFormRepository->expects($this->once())->method("aWorksheetFormOfId")->with($this->worksheetFormId);
        
        $this->manager->expects($this->once())
                ->method("changeMissionsWorksheetForm");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->missionRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
