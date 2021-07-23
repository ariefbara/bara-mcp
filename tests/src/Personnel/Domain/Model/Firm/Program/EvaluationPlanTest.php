<?php

namespace Personnel\Domain\Model\Firm\Program;

use Personnel\Domain\Model\Firm\FeedbackForm;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class EvaluationPlanTest extends TestBase
{
    protected $evaluationPlan;
    protected $reportForm, $formRecordId = 'form-record-id', $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationPlan = new TestableEvaluationPlan();
        
        $this->reportForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->evaluationPlan->reportForm = $this->reportForm;
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    public function test_assertUsableInProgram_activePlanInSameProgram_void()
    {
        $this->evaluationPlan->assertUsableInProgram($this->evaluationPlan->programId);
        $this->markAsSuccess();
    }
    public function test_assertUsableInProgram_inactivePlan_forbidden()
    {
        $this->evaluationPlan->disabled = true;
        $this->assertRegularExceptionThrowed(function (){
            $this->evaluationPlan->assertUsableInProgram($this->evaluationPlan->programId);
        }, 'Forbidden', 'forbidden: unusable evaluation plan');
    }
    public function test_assertUsableInProgram_differentProgram_forbidden()
    {
        $this->assertRegularExceptionThrowed(function (){
            $this->evaluationPlan->assertUsableInProgram('different-program-id');
        }, 'Forbidden', 'forbidden: unusable evaluation plan');
    }
    
    public function test_createFormRecord_returnReportFormCreateFormRecordResult()
    {
        $this->reportForm->expects($this->once())
                ->method('createFormRecord')
                ->with($this->formRecordId, $this->formRecordData);
        $this->evaluationPlan->createFormRecord($this->formRecordId, $this->formRecordData);
    }
}

class TestableEvaluationPlan extends EvaluationPlan
{
    public $programId = 'program-id';
    public $id = 'evaluation-plan-id';
    public $disabled = false;
    public $reportForm;
    
    function __construct()
    {
        parent::__construct();
    }
}
