<?php

namespace Firm\Domain\Task\InFirm\Program\Mission;

use Firm\Domain\Model\Firm\Program\MissionData;
use Tests\src\Firm\Domain\Task\InFirm\FirmTaskTestBase;

class CreateRootMissionTest extends FirmTaskTestBase
{
    protected $task;
    protected $payload, $missionData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setMissionRelatedDependency();
        $this->setProgramRelatedDependency();
        //
        $this->task = new CreateRootMission($this->missionRepository, $this->programRepository);
        $this->missionData = new MissionData('name', 'description', 1);
        $this->payload = (new CreateRootMissionPayload())
                ->setMissionData($this->missionData)
                ->setProgramId($this->programId);
    }
    
    //
    protected function execute()
    {
        $this->missionRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->missionId);
        $this->task->executeInFirm($this->firm, $this->payload);
    }
    public function test_execute_addMissionCreatedInProgramToRepository()
    {
        $this->program->expects($this->once())
                ->method('createRootMission')
                ->with($this->missionId, $this->missionData)
                ->willReturn($this->mission);
        $this->missionRepository->expects($this->once())
                ->method('add')
                ->with($this->mission);
        $this->execute();
    }
    public function test_execute_assertProgramManageableInFirm()
    {
        $this->program->expects($this->once())
                ->method('assertManageableInFirm')
                ->with($this->firm);
        $this->execute();
    }
    public function test_execute_setMissionDataId()
    {
        $this->execute();
        $this->assertSame($this->missionId, $this->missionData->id);
    }
}
