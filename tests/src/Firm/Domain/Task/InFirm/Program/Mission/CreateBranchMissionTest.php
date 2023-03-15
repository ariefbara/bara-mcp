<?php

namespace Firm\Domain\Task\InFirm\Program\Mission;

use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\MissionData;
use Tests\src\Firm\Domain\Task\InFirm\FirmTaskTestBase;

class CreateBranchMissionTest extends FirmTaskTestBase
{
    protected $task;
    //
    protected $payload, $missionData;
    protected $branchMissionId = 'branchMissionId', $branchMission;


    protected function setUp(): void
    {
        parent::setUp();
        $this->setMissionRelatedDependency();
        //
        $this->task = new CreateBranchMission($this->missionRepository);
        
        $this->missionData = new MissionData('name', 'description', 1);
        $this->payload = (new CreateBranchMissionPayload())
                ->setParentMissionId($this->missionId)
                ->setMissionData($this->missionData);
        
        $this->branchMission = $this->buildMockOfClass(Mission::class);
    }
    
    //
    protected function execute()
    {
        $this->missionRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->branchMissionId);
        $this->task->executeInFirm($this->firm, $this->payload);
    }
    public function test_execute_addBranchMissionCreatedInParentToRepository()
    {
        $this->mission->expects($this->once())
                ->method('createBranch')
                ->with($this->branchMissionId, $this->missionData)
                ->willReturn($this->branchMission);
        $this->missionRepository->expects($this->once())
                ->method('add')
                ->with($this->branchMission);
        $this->execute();
    }
    public function test_execute_assertParentMissionManageableInFirm()
    {
        $this->mission->expects($this->once())
                ->method('assertManageableInFirm')
                ->with($this->firm);
        $this->execute();
    }
    public function test_execute_setMissionDataId()
    {
        $this->execute();
        $this->assertSame($this->branchMissionId, $this->missionData->id);
    }
}
