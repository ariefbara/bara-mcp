<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    DependencyModel\Firm\Program\Mission,
    DependencyModel\Firm\Team,
    Model\Participant,
    Model\Participant\Worksheet\Comment,
    Model\Participant\Worksheet\WorksheetActivityLog
};
use SharedContext\Domain\Model\SharedEntity\ {
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class WorksheetTest extends TestBase
{
    
    protected $rootMission, $mission; 
    protected $participant, $worksheet;
    protected $id = 'worksheet-id', $name = 'new name';
    protected $formRecord;
    protected $formRecordData;
    
    protected $commentId = 'commentId', $commentMessage = 'commentMessage';
    protected $teamMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        
        $this->rootMission = $this->buildMockOfClass(Mission::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        
        $this->worksheet = new TestableWorksheet($this->participant, 'id', "worksheet name", $this->rootMission, $this->formRecordData);
        $this->worksheet->formRecord = $this->formRecord;
        $this->worksheet->worksheetActivityLogs->clear();
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
    }
    
    public function test_belongsToTeam_returnParticipantsIsATeamProgramParticipationOfTeamResult()
    {
        $this->participant->expects($this->once())
                ->method("belongsToTeam")
                ->with($team = $this->buildMockOfClass(Team::class))
                ->willReturn(true);
        $this->assertTrue($this->worksheet->belongsToTeam($team));
    }
    
    protected function executeCreateRootWorksheet()
    {
        $this->rootMission->expects($this->once())
                ->method('isRootMission')
                ->willReturn(true);
        return TestableWorksheet::createRootWorksheet($this->participant, $this->id, $this->name, $this->rootMission, $this->formRecordData, null);
    }
    public function test_createRootWorksheet_setProperties()
    {
        $formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->rootMission->expects($this->once())
                ->method("createWorksheetFormRecord")
                ->with($this->id, $this->formRecordData)
                ->willReturn($formRecord);
        
        $worksheet = $this->executeCreateRootWorksheet();
        $this->assertEquals($this->participant, $worksheet->participant);
        $this->assertEquals($this->id, $worksheet->id);
        $this->assertEquals($this->name, $worksheet->name);
        $this->assertEquals($this->rootMission, $worksheet->mission);
        $this->assertFalse($worksheet->removed);
        $this->assertNull($worksheet->parent);
        $this->assertEquals($formRecord, $worksheet->formRecord);
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
    public function test_createRootWorksheet_addActivity()
    {
        $worksheet = $this->executeCreateRootWorksheet();
        $this->assertInstanceOf(WorksheetActivityLog::class, $worksheet->worksheetActivityLogs->first());
    }
    
    protected function executeCreateBranchWorksheet()
    {
        $this->rootMission->expects($this->once())
                ->method('hasBranch')
                ->willReturn(true);
        return $this->worksheet->createBranchWorksheet($this->id, $this->name, $this->mission, $this->formRecordData);
    }
    public function test_createBranchWorksheet_setProperties()
    {
        $formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->mission->expects($this->once())
                ->method("createWorksheetFormRecord")
                ->with($this->id, $this->formRecordData)
                ->willReturn($formRecord);
        
        $worksheet = $this->executeCreateBranchWorksheet();
        $this->assertEquals($this->participant, $worksheet->participant);
        $this->assertEquals($this->id, $worksheet->id);
        $this->assertEquals($this->name, $worksheet->name);
        $this->assertEquals($this->mission, $worksheet->mission);
        $this->assertEquals($formRecord, $worksheet->formRecord);
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
    public function test_createBranch_logActivity()
    {
        $branch = $this->executeCreateBranchWorksheet();
        $this->assertInstanceOf(WorksheetActivityLog::class, $branch->worksheetActivityLogs->first());
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
    public function test_update_logActivity()
    {
        $this->executeUpdate();
        $this->assertInstanceOf(WorksheetActivityLog::class, $this->worksheet->worksheetActivityLogs->first());
    }
    
    public function test_remove_setRemovedFlagTrue()
    {
        $this->worksheet->remove();
        $this->assertTrue($this->worksheet->removed);
    }
    public function test_remove_logActivity()
    {
        $this->worksheet->remove();
        $this->assertInstanceOf(WorksheetActivityLog::class, $this->worksheet->worksheetActivityLogs->first());
    }
    
    public function test_createComment_returnComment()
    {
        $this->assertInstanceOf(Comment::class, $this->worksheet->createComment($this->commentId, $this->commentMessage, $this->teamMember));
    }
    
}

class TestableWorksheet extends Worksheet
{
    public $mission, $id, $name, $parent, $participant, $formRecord, $removed;
    public $branches;
    public $worksheetActivityLogs;
    
    function __construct(Participant $participant, string $id, string $name, Mission $mission,
            FormRecordData $formRecordData, ?TeamMembership $teamMember = null)
    {
        parent::__construct($participant, $id, $name, $mission, $formRecordData, $teamMember);
    }
}
