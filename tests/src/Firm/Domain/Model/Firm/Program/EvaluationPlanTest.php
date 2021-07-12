<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\ {
    Firm,
    Firm\FeedbackForm,
    Firm\Program
};
use Tests\TestBase;
use TypeError;

class EvaluationPlanTest extends TestBase
{
    protected $program;
    protected $reportForm;
    protected $mission;
    protected $evaluationPlan;
    protected $id = "newId", $name = "new name", $interval = 120;
    protected $firm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        
        $reportForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        
        $evaluationPlanData = new EvaluationPlanData("name", 99);
        $this->evaluationPlan = new TestableEvaluationPlan($this->program, "id", $evaluationPlanData, $reportForm, null);
        
        $this->reportForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->firm = $this->buildMockOfClass(Firm::class);
    }
    protected function buildEvaluationPlanData()
    {
        return new EvaluationPlanData($this->name, $this->interval);
    }
    
    protected function executeConstruct()
    {
        $this->mission->expects($this->any())
                ->method('belongsToProgram')
                ->with($this->program)
                ->willReturn(true);
        return new TestableEvaluationPlan(
                $this->program, $this->id, $this->buildEvaluationPlanData(), $this->reportForm, $this->mission);
    }
    public function test_construct_setProperties()
    {
        $evaluationPlan = $this->executeConstruct();
        $this->assertEquals($this->program, $evaluationPlan->program);
        $this->assertEquals($this->id, $evaluationPlan->id);
        $this->assertEquals($this->name, $evaluationPlan->name);
        $this->assertEquals($this->interval, $evaluationPlan->interval);
        $this->assertEquals($this->reportForm, $evaluationPlan->reportForm);
        $this->assertEquals($this->mission, $evaluationPlan->mission);
        $this->assertFalse($evaluationPlan->disabled);
    }
    public function test_construct_emptyName_badRequest()
    {
        $this->name = "";
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: evaluation plan name is mandatory";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_nullInterval_throwTypeError()
    {
        $this->interval = null;
        $this->expectException(TypeError::class);
        $this->executeConstruct();
    }
    public function test_construct_nullMission_setNull()
    {
        $evaluationPlan = new TestableEvaluationPlan($this->program, $this->id, $this->buildEvaluationPlanData(), $this->reportForm, null);
        $this->assertNull($evaluationPlan->mission);
    }
    public function test_construct_missionFromDifferentProgram_forbidden()
    {
        $this->mission->expects($this->once())
                ->method('belongsToProgram')
                ->with($this->program)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function () {
            $this->executeConstruct();
        }, 'Forbidden', 'forbidden: mission must be from same program');
    }
    
    public function test_belongsToFirm_returnProgramBelongsToFirmResult()
    {
        $this->program->expects($this->once())
                ->method("belongsToFirm")
                ->with($this->firm);
        $this->evaluationPlan->belongsToFirm($this->firm);
    }
    
    protected function executeUpdate()
    {
        $this->mission->expects($this->any())
                ->method('belongsToProgram')
                ->with($this->program)
                ->willReturn(true);
        $this->evaluationPlan->update($this->buildEvaluationPlanData(), $this->reportForm, $this->mission);
    }
    public function test_update_setProperties()
    {
        $this->evaluationPlan->mission = null;
        $this->executeUpdate();
        $this->assertEquals($this->name, $this->evaluationPlan->name);
        $this->assertEquals($this->interval, $this->evaluationPlan->interval);
        $this->assertEquals($this->reportForm, $this->evaluationPlan->reportForm);
        $this->assertEquals($this->mission, $this->evaluationPlan->mission);
    }
    public function test_update_emptyName_badRequest()
    {
        $this->name = "";
        $operation = function (){
            $this->executeUpdate();
        };
        $errorDetail = "bad request: evaluation plan name is mandatory";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_update_nullInterval_throwTypeError()
    {
        $this->interval = null;
        $this->expectException(TypeError::class);
        $this->executeUpdate();
    }
    public function test_update_nullMission_setMissionNull()
    {
        $this->evaluationPlan->update($this->buildEvaluationPlanData(), $this->reportForm, null);
        $this->assertNull($this->evaluationPlan->mission);
    }
    public function test_update_unusableMission_forbidden()
    {
        $this->mission->expects($this->once())
                ->method('belongsToProgram')
                ->with($this->program)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function () {
            $this->executeUpdate();
        }, 'Forbidden', 'forbidden: mission must be from same program');
    }
    
    public function test_disable_setDisableTrue()
    {
        $this->evaluationPlan->disable();
        $this->assertTrue($this->evaluationPlan->disabled);
    }
    
    public function test_enable_setDisableFalse()
    {
        $this->evaluationPlan->disabled = true;
        $this->evaluationPlan->enable();
        $this->assertFalse($this->evaluationPlan->disabled);
    }
    
    public function test_belongsToProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->evaluationPlan->belongsToProgram($this->evaluationPlan->program));
    }
    public function test_belongsToProgram_differentProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->evaluationPlan->belongsToProgram($program));
    }
}

class TestableEvaluationPlan extends EvaluationPlan
{
    public $program;
    public $id;
    public $name;
    public $interval;
    public $disabled;
    public $reportForm;
    public $mission;
}
