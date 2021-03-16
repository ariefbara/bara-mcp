<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\MissionData;
use Tests\src\Firm\Domain\Model\Firm\ManagerTestBase;

class ManagerTest_ManageMission extends ManagerTestBase
{
    protected $program;
    protected $worksheetForm;
    protected $missionId = 'missionId', $mission, $missionData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->worksheetForm = $this->buildMockOfClass(WorksheetForm::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionData = $this->buildMockOfClass(MissionData::class);
        $this->missionData->expects($this->any())->method('getName')->willReturn('new mission name');
    }
    
    protected function executeCreateRootMission()
    {
        $this->setAssetManageableByFirm($this->program);
        $this->setAssetManageableByFirm($this->worksheetForm);
        return $this->manager->createRootMission($this->missionId, $this->program, $this->worksheetForm, $this->missionData);
    }
    public function test_createRootMission_returnMissionCreatedInProgram()
    {
        $this->program->expects($this->once())
                ->method('createRootMission')
                ->with($this->missionId, $this->worksheetForm, $this->missionData);
        $this->executeCreateRootMission();
    }
    public function test_createRootMission_inactiveManager_forbidden()
    {
        $this->manager->removed = true;
        $this->assertInactiveManager(function (){
            $this->executeCreateRootMission();
        });
    }
    public function test_createRootMission_programUnmanageable_forbidden()
    {
        $this->setAssetUnmanageableByFirm($this->program);
        $this->assertAssetNotManageableByFirm(function (){
            $this->executeCreateRootMission();
        });
    }
    public function test_createRootMission_worksheetFormUnamageable_forbidden()
    {
        $this->setAssetUnmanageableByFirm($this->worksheetForm);
        $this->assertAssetNotManageableByFirm(function (){
            $this->executeCreateRootMission();
        });
    }
    
    protected function executeCreateBranchMission()
    {
        $this->setAssetManageableByFirm($this->mission);
        $this->setAssetManageableByFirm($this->worksheetForm);
        return $this->manager->createBranchMission($this->missionId, $this->mission, $this->worksheetForm, $this->missionData);
    }
    public function test_createBranchMission_returnBranchMissionCreatedInMission()
    {
        $branch = $this->buildMockOfClass(Mission::class);
        $this->mission->expects($this->once())
                ->method('createBranch')
                ->with($this->missionId, $this->worksheetForm, $this->missionData)
                ->willReturn($branch);
        $this->assertSame($branch, $this->executeCreateBranchMission());
    }
    public function test_createBranchMission_inactiveManger_forbidden()
    {
        $this->manager->removed = true;
        $this->assertInactiveManager(function (){
            $this->executeCreateBranchMission();
        });
    }
    public function test_createBranchMission_programUnmanageable_forbidden()
    {
        $this->setAssetUnmanageableByFirm($this->mission);
        $this->assertAssetNotManageableByFirm(function (){
            $this->executeCreateBranchMission();
        });
    }
    public function test_createBranchMission_worksheetFormUnmanageable_forbidden()
    {
        $this->setAssetUnmanageableByFirm($this->worksheetForm);
        $this->assertAssetNotManageableByFirm(function (){
            $this->executeCreateBranchMission();
        });
    }
    
    protected function executeUpdateMission()
    {
        $this->setAssetManageableByFirm($this->mission);
        return $this->manager->updateMission($this->mission, $this->missionData);
    }
    public function test_updateMission_updateMission()
    {
        $this->mission->expects($this->once())
                ->method('update')
                ->with($this->missionData);
        $this->executeUpdateMission();
    }
    public function test_updateMission_inactiveManager_forbidden()
    {
        $this->manager->removed = true;
        $this->assertInactiveManager(function (){
            $this->executeUpdateMission();
        });
    }
    public function test_updateMission_missionUnmanageable_forbidden()
    {
        $this->setAssetUnmanageableByFirm($this->mission);
        $this->assertAssetNotManageableByFirm(function (){
            $this->executeUpdateMission();
        });
    }
    
    protected function executePublishMission()
    {
        $this->setAssetManageableByFirm($this->mission);
        return $this->manager->publishMission($this->mission);
    }
    public function test_publishMission_publishMission()
    {
        $this->mission->expects($this->once())
                ->method('publish');
        $this->executePublishMission();
    }
    public function test_publishMission_inactiveManager_forbidden()
    {
        $this->manager->removed = true;
        $this->assertInactiveManager(function (){
            $this->executePublishMission();
        });
    }
    public function test_publishMission_missionUnamanageable_forbidden()
    {
        $this->setAssetUnmanageableByFirm($this->mission);
        $this->assertAssetNotManageableByFirm(function (){
            $this->executePublishMission();
        });
    }
    
    protected function executeChangeMissionsWorksheetForm()
    {
        $this->setAssetManageableByFirm($this->mission);
        $this->setAssetManageableByFirm($this->worksheetForm);
        $this->manager->changeMissionsWorksheetForm($this->mission, $this->worksheetForm);
    }
    public function test_changeMissionsWorkshetForm_changeMissionsWorksheetForm()
    {
        $this->mission->expects($this->once())
                ->method("changeWorksheetForm")
                ->with($this->worksheetForm);
        $this->executeChangeMissionsWorksheetForm();
    }
    public function test_changeMissionWorksheetForm_inactiveManager_forbidden()
    {
        $this->manager->removed = true;
        $this->assertInactiveManager(function (){
            $this->executeChangeMissionsWorksheetForm();
        });
    }
    public function test_changeMissionWorksheetForm_unamanagedMission_forbidden()
    {
        $this->setAssetUnmanageableByFirm($this->mission);
        $this->assertAssetNotManageableByFirm(function (){
            $this->executeChangeMissionsWorksheetForm();
        });
    }
    public function test_changeMissionWorksheetForm_unamanagedWorksheetForm_forbidden()
    {
        $this->setAssetUnmanageableByFirm($this->worksheetForm);
        $this->assertAssetNotManageableByFirm(function (){
            $this->executeChangeMissionsWorksheetForm();
        });
    }
}
