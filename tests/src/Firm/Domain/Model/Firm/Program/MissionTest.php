<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm\{
    Program,
    WorksheetForm
};
use Tests\TestBase;

class MissionTest extends TestBase
{

    protected $program;
    protected $worksheetForm;
    protected $mission;
    protected $id = 'mission-id', $name = 'new mission name', $description = 'new mission description', $position = 'mission positioin';

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->worksheetForm = $this->buildMockOfClass(WorksheetForm::class);

        $this->mission = TestableMission::createRoot(
                        $this->program, 'id', 'name', 'description', $this->worksheetForm, 'position');
        $this->mission->branches = new ArrayCollection();

        $this->participant = $this->buildMockOfClass(Participant::class);
    }

    protected function executeCreateRoot()
    {
        return TestableMission::createRoot($this->program, $this->id, $this->name, $this->description,
                        $this->worksheetForm, $this->position);
    }

    public function test_construct_setProperties()
    {
        $mission = $this->executeCreateRoot();
        $this->assertEquals($this->program, $mission->program);
        $this->assertNull($mission->parent);
        $this->assertEquals($this->id, $mission->id);
        $this->assertEquals($this->name, $mission->name);
        $this->assertEquals($this->description, $mission->description);
        $this->assertEquals($this->position, $mission->position);
        $this->assertEquals($this->worksheetForm, $mission->worksheetForm);
        $this->assertFalse($mission->published);
    }

    public function test_createRoot_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function () {
            $this->executeCreateRoot();
        };
        $errorDetail = "bad request: mission name is required";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    protected function executeCreateBranch()
    {
        return $this->mission->createBranch($this->id, $this->name, $this->description, $this->worksheetForm,
                        $this->position);
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

    public function test_createBranch_alreadyContainBranch_createNormally()
    {
        $nextMission = $this->buildMockOfClass(Mission::class);
        $this->mission->branches->add($nextMission);

        $this->executeCreateBranch();
        $this->markAsSuccess();
    }

    protected function executeUpdate()
    {
        $this->mission->update($this->name, $this->description, $this->position);
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

    public function test_update_alreadyPublished_processNormally()
    {
        $this->mission->published = true;
        $this->executeUpdate();
        $this->markAsSuccess();
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

}

class TestableMission extends Mission
{

    public $program, $parent, $id, $name, $description, $published, $position;
    public $worksheetForm, $branches;

}
