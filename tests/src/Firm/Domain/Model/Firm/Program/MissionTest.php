<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Firm\Domain\Model\Firm\Program\Mission\LearningMaterialData;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Firm\Domain\Model\Firm\WorksheetForm;
use Tests\TestBase;

class MissionTest extends TestBase
{

    protected $program;
    protected $mission;
    protected $id = 'mission-id', $name = 'new mission name', $description = 'new mission description', $position = 'mission positioin';
    protected $firm;
    protected $worksheetForm;
    protected $missionCommentId = 'mission-comment-id', $missionCommentData, $userId = 'user-id', $userName = 'user name';
    protected $learningMaterialId = 'learning-material-id', $learningMaterialData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);

        $missionData = new MissionData('name', 'description', 'position');
        $this->mission = new TestableMission($this->program, 'id', $missionData);
        $this->mission->branches = new ArrayCollection();

        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->firm = $this->buildMockOfClass(Firm::class);
        
        $this->worksheetForm = $this->buildMockOfClass(WorksheetForm::class);
        $this->missionCommentData = new MissionCommentData('message');
        $this->learningMaterialData = new LearningMaterialData('name', 'content');
    }
    protected function getMissionData()
    {
        return new MissionData($this->name, $this->description, $this->position);
    }
    
    public function test_belongsToProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->mission->belongsToProgram($this->program));
    }
    public function test_belongsToProgram_differentProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->mission->belongsToProgram($program));
    }

    //
    protected function executeConstruct()
    {
        return new TestableMission($this->program, $this->id, $this->getMissionData());
    }
    public function test_construct_setProperties()
    {
        $mission = $this->executeConstruct();
        $this->assertEquals($this->program, $mission->program);
        $this->assertNull($mission->parent);
        $this->assertEquals($this->id, $mission->id);
        $this->assertEquals($this->name, $mission->name);
        $this->assertEquals($this->description, $mission->description);
        $this->assertEquals($this->position, $mission->position);
        $this->assertNull($mission->worksheetForm);
        $this->assertFalse($mission->published);
    }
    public function test_construct_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = "bad request: mission name is required";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    //
    protected function executeCreateBranch()
    {
        return $this->mission->createBranch($this->id, $this->getMissionData());
    }
    public function test_createBranch_createBranchMission()
    {
        $branchMission = $this->executeCreateBranch();
        $this->assertEquals($this->program, $branchMission->program);
        $this->assertEquals($this->mission, $branchMission->parent);
        $this->assertEquals($this->id, $branchMission->id);
        $this->assertEquals($this->name, $branchMission->name);
        $this->assertEquals($this->description, $branchMission->description);
        $this->assertEquals($this->position, $branchMission->position);
    }

    //
    protected function executeUpdate()
    {
        $this->mission->update($this->getMissionData());
    }
    public function test_update_changeProperties()
    {
        $this->executeUpdate();
        $this->assertEquals($this->name, $this->mission->name);
        $this->assertEquals($this->description, $this->mission->description);
        $this->assertEquals($this->position, $this->mission->position);
    }
    public function test_update_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function () {
            $this->executeUpdate();
        };
        $errorDetail = "bad request: mission name is required";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    //
    protected function executePublish()
    {
        $this->mission->publish();
    }
    public function test_publish_setPublishedFlagTrue()
    {
        $this->executePublish();
        $this->assertTrue($this->mission->published);
    }
    public function test_publish_alreadyPublished_throwError()
    {
        $this->mission->published = true;
        $operation = function () {
            $this->executePublish();
        };
        $errorDetail = "forbidden: request only valid for non published mission";
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    //
    protected function assignWorksheetForm()
    {
        $this->mission->assignWorksheetForm($this->worksheetForm);
    }
    public function test_assignWorksheetForm_setWorksheetForm()
    {
        $this->assignWorksheetForm();
        $this->assertSame($this->worksheetForm, $this->mission->worksheetForm);
    }
    
    //
    protected function assertManageableInFirm()
    {
        $this->mission->assertManageableInFirm($this->firm);
    }
    public function test_assertManageableInFirm_assertProgramManageableInFirm()
    {
        $this->program->expects($this->once())
                ->method('assertManageableInFirm')
                ->with($this->firm);
        $this->assertManageableInFirm();
    }
    
    //
    public function test_belongsToFirm_returnProgramsBelongsToFirmResult()
    {
        $this->program->expects($this->once())
                ->method("belongsToFirm")
                ->with($this->firm);
        $this->mission->belongsToFirm($this->firm);
    }

    public function test_isManageableByFirm_returnProgramsIsManageableByFirmResult()
    {
        $firm = $this->buildMockOfClass(Firm::class);
        $this->program->expects($this->once())
                ->method('isManageableByFirm')
                ->with($firm);
        $this->mission->isManageableByFirm($firm);
    }
    
    protected function executeReceiveComment()
    {
        return $this->mission->receiveComment(
                $this->missionCommentId, $this->missionCommentData, $this->userId, $this->userName);
    }
    public function test_receiveComment_returnMissionComment()
    {
        $missionComment = new MissionComment($this->mission, $this->missionCommentId, $this->missionCommentData, $this->userId, $this->userName);
        $this->assertEquals($missionComment, $this->executeReceiveComment());
    }
    
    protected function addLearningMaterial()
    {
        return $this->mission->addLearningMaterial($this->learningMaterialId, $this->learningMaterialData);
    }
    public function test_addLearningMaterial_returnLearningMaterial()
    {
        $this->assertInstanceOf(LearningMaterial::class, $this->addLearningMaterial());
    }
    
    protected function assertAccessibleInFirm()
    {
        $this->mission->assertAccessibleInFirm($this->firm);
    }
    public function test_assertAccessibleInFirm_assertProgramAccessibleInFirm()
    {
        $this->program->expects($this->once())
                ->method('assertAccessibleInFirm')
                ->with($this->firm);
        $this->assertAccessibleInFirm();
    }
}

class TestableMission extends Mission
{

    public $program, $parent, $id, $name, $description, $published, $position;
    public $worksheetForm, $branches;

}
