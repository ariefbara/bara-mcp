<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\WorksheetForm;
use Tests\src\Firm\Application\Service\Manager\MissionTestBase;

class CreateBranchMissionTest extends MissionTestBase
{
    protected $service;
    protected $worksheetFormRepository, $worksheetForm, $worksheetFormId = 'worksheetFormId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetFormRepository = $this->buildMockOfClass(WorksheetFormRepository::class);
        $this->worksheetForm = $this->buildMockOfClass(WorksheetForm::class);
        $this->worksheetFormRepository->expects($this->any())
                ->method('aWorksheetFormOfId')
                ->with($this->worksheetFormId)
                ->willReturn($this->worksheetForm);
        
        $this->service = new CreateBranchMission($this->managerRepository, $this->missionRepository, $this->worksheetFormRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->managerId, $this->missionId, $this->worksheetFormId, $this->missionData);
    }
    public function test_execute_addBranchMissionCreatedByManagerToRepository()
    {
        $branchMission = $this->buildMockOfClass(Mission::class);
        $this->manager->expects($this->once())
                ->method('createBranchMission')
                ->with($this->nextMissionId, $this->mission, $this->worksheetForm, $this->missionData)
                ->willReturn($branchMission);
        
        $this->missionRepository->expects($this->once())
                ->method('add')
                ->with($this->identicalTo($branchMission));
        $this->execute();
    }
    public function test_execute_returnNextMissionId()
    {
        $this->assertEquals($this->nextMissionId, $this->execute());
    }
}
