<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\WorksheetForm;
use Tests\TestBase;

class MissionTest extends TestBase
{

    protected $program;
    protected $worksheetForm;
    protected $mission;
    protected $id = 'mission-id', $name = 'new mission name', $description = 'new mission description', $position = 'mission positioin';
    protected $firm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->worksheetForm = $this->buildMockOfClass(WorksheetForm::class);

        $missionData = new MissionData('name', 'description', 'position');
        $this->mission = new TestableMission($this->program, 'id', $this->worksheetForm, $missionData);
        $this->mission->branches = new ArrayCollection();

        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->firm = $this->buildMockOfClass(Firm::class);
    }
    protected function getMissionData()
    {
        return new MissionData($this->name, $this->description, $this->position);
    }

    protected function executeConstruct()
    {
        return new TestableMission($this->program, $this->id, $this->worksheetForm, $this->getMissionData());
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
        $this->assertEquals($this->worksheetForm, $mission->worksheetForm);
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

    protected function executeCreateBranch()
    {
        return $this->mission->createBranch($this->id, $this->worksheetForm, $this->getMissionData());
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
    
    protected function executeChangeWorksheetForm()
    {
        $this->mission->worksheetForm = null;
        $this->mission->changeWorksheetForm($this->worksheetForm);
    }
    public function test_changeWorksheetForm_setWorksheetForm()
    {
        $this->executeChangeWorksheetForm();
        $this->assertEquals($this->worksheetForm, $this->mission->worksheetForm);
    }
    public function test_changeWorksheetForm_publishedMission_forbidden()
    {
        $this->mission->published = true;
        $operation = function (){
            $this->executeChangeWorksheetForm();
        };
        $errorDetail = "forbidden: can only change worksheet form of unpublished mission";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
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
}

class TestableMission extends Mission
{

    public $program, $parent, $id, $name, $description, $published, $position;
    public $worksheetForm, $branches;

}
