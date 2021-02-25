<?php

namespace Firm\Application\Service\Manager;

use Tests\src\Firm\Application\Service\Manager\MissionTestBase;

class PublishMissionTest extends MissionTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PublishMission($this->managerRepository, $this->missionRepository);
    }
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->managerId, $this->missionId);
    }
    public function test_execute_managerPublishMission()
    {
        $this->manager->expects($this->once())
                ->method('publishMission')
                ->with($this->mission);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->managerRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
