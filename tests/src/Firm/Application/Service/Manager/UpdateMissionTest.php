<?php

namespace Firm\Application\Service\Manager;

use Tests\src\Firm\Application\Service\Manager\MissionTestBase;

class UpdateMissionTest extends MissionTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UpdateMission($this->managerRepository, $this->missionRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->missionId, $this->missionData);
    }
    public function test_execute_updateMissionByManager()
    {
        $this->manager->expects($this->once())
                ->method('updateMission')
                ->with($this->mission, $this->missionData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->managerRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
