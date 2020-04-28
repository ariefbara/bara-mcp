<?php

namespace Client\Domain\Model\Client\ProgramParticipation;

use Client\Domain\Model\ {
    Client\ProgramParticipation,
    Firm\Program\Mission
};
use Shared\Domain\Model\ {
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class WorksheetTest extends TestBase
{
    
    protected $rootMission, $mission; 
    protected $programParticipation, $worksheet;
    protected $id = 'worksheet-id', $name = 'new name';
    protected $formRecord;
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        
        $this->rootMission = $this->buildMockOfClass(Mission::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        
        $this->programParticipation = $this->buildMockOfClass(ProgramParticipation::class);
        $this->worksheet = new TestableWorksheet($this->programParticipation, 'id', "worksheet name", $this->rootMission, $this->formRecord);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function executeCreateRootWorksheet()
    {
        $this->rootMission->expects($this->once())
                ->method('isRootMission')
                ->willReturn(true);
        return TestableWorksheet::createRootWorksheet($this->programParticipation, $this->id, $this->name, $this->rootMission, $this->formRecord);
    }
    public function test_createRootWorksheet_setProperties()
    {
        $worksheet = $this->executeCreateRootWorksheet();
        $this->assertEquals($this->programParticipation, $worksheet->programParticipation);
        $this->assertEquals($this->id, $worksheet->id);
        $this->assertEquals($this->name, $worksheet->name);
        $this->assertEquals($this->rootMission, $worksheet->mission);
        $this->assertEquals($this->formRecord, $worksheet->formRecord);
        $this->assertFalse($worksheet->removed);
        $this->assertNull($worksheet->parent);
    }
    public function test_createWorksheet_emptyName_throwEx()
    {
        $this->name = "";
        $operation = function (){
            $this->executeCreateRootWorksheet();
        };
        $errorDetail = "bad request: worksheet name is mandatory";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_createRootWorksheet_missionIsNotRootMission_throwEx()
    {
        $this->rootMission->expects($this->once())
                ->method('isRootMission')
                ->willReturn(false);
        $operation = function (){
            $this->executeCreateRootWorksheet();
        };
        $errorDetail = 'forbidden: root worksheet can only refer to root mission';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    protected function executeCreateBranchWorksheet()
    {
        $this->rootMission->expects($this->once())
                ->method('hasBranch')
                ->willReturn(true);
        return $this->worksheet->createBranchWorksheet($this->id, $this->name, $this->mission, $this->formRecord);
    }
    public function test_createBranchWorksheet_setProperties()
    {
        $worksheet = $this->executeCreateBranchWorksheet();
        $this->assertEquals($this->programParticipation, $worksheet->programParticipation);
        $this->assertEquals($this->id, $worksheet->id);
        $this->assertEquals($this->name, $worksheet->name);
        $this->assertEquals($this->mission, $worksheet->mission);
        $this->assertEquals($this->formRecord, $worksheet->formRecord);
    }
    public function test_createBranchWorksheet_setWorksheetAsBranchWorksheetParent()
    {
        $worksheet = $this->executeCreateBranchWorksheet();
        $this->assertEquals($this->worksheet, $worksheet->parent);
    }
    public function test_createBranch_missionIsNotBranchOfParentWorksheetsMission_throwEx()
    {
        $this->rootMission->expects($this->once())
                ->method('hasBranch')
                ->with($this->mission)
                ->willReturn(false);
        $operation = function (){
            $this->executeCreateBranchWorksheet();
        };
        $errorDetail = "forbidden: parent worksheet mission doesn't contain this mission";
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    protected function executeUpdate()
    {
        $this->worksheet->update($this->name, $this->formRecordData);
    }
    
    public function test_update_changeName()
    {
        $this->executeUpdate();
        $this->assertEquals($this->name, $this->worksheet->name);
    }
    
    public function test_update_updateFormRecord()
    {
        $this->formRecord->expects($this->once())
                ->method('update')
                ->with($this->formRecordData);
        $this->executeUpdate();
    }
    
    public function test_remove_setRemovedFlagTrue()
    {
        $this->worksheet->remove();
        $this->assertTrue($this->worksheet->removed);
    }
}

class TestableWorksheet extends Worksheet
{
    public $mission, $id, $name, $parent, $programParticipation, $formRecord, $removed;
    public $branches;
    
    public function __construct(ProgramParticipation $programParticipation, string $id, string $name,
            Mission $mission, FormRecord $formRecord)
    {
        parent::__construct($programParticipation, $id, $name, $mission, $formRecord);
    }
}
