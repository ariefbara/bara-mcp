<?php

namespace User\Domain\DependencyModel\Firm\Program;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;
use User\Domain\DependencyModel\Firm\ {
    FeedbackForm,
    Program
};

class EvaluationPlanTest extends TestBase
{
    protected $evaluationPlan;
    protected $program;
    protected $reportForm;
    protected $formRecordId = "formRecordId", $formRecordData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->reportForm = $this->buildMockOfClass(FeedbackForm::class);
        
        $this->evaluationPlan = new TestableEvaluationPlan();
        $this->evaluationPlan->program = $this->program;
        $this->evaluationPlan->reportForm = $this->reportForm;
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    public function test_isAnEnabledEvaluationPlanInProgram_enabledPlanIsSameProgram_returnTrue()
    {
        $this->assertTrue($this->evaluationPlan->isAnEnabledEvaluationPlanInProgram($this->program));
    }
    public function test_isAnEnabledEvaluationPlanInProgram_disabledPlan_returnFalse()
    {
        $this->evaluationPlan->disabled = true;
        $this->assertFalse($this->evaluationPlan->isAnEnabledEvaluationPlanInProgram($this->program));
    }
    public function test_isAnEnabledEvaluationPlanInProgram_differentProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->evaluationPlan->isAnEnabledEvaluationPlanInProgram($program));
    }
    
    public function test_createFormRecord_returnFormRecordCreatedInReportForm()
    {
        $this->reportForm->expects($this->once())
                ->method("createFormRecord")
                ->with($this->formRecordId, $this->formRecordData);
        $this->evaluationPlan->createFormRecord($this->formRecordId, $this->formRecordData);
    }
}

class TestableEvaluationPlan extends EvaluationPlan
{
    public $program;
    public $id;
    public $interval;
    public $disabled = false;
    public $reportForm;
    
    function __construct()
    {
        parent::__construct();
    }
}
