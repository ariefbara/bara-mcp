<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Shared\FormRecord;
use Tests\TestBase;

class EvaluationPlanTest extends TestBase
{
    protected $evaluationPlan;
    protected $reportForm;
    protected $formRecord;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationPlan = new TestableEvaluationPlan();
        
        $this->reportForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->evaluationPlan->reportForm = $this->reportForm;
        
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
    }
    
    public function test_toArrayOfSummaryTableHeader_returnSummaryTableHeader()
    {
        $this->reportForm->expects($this->once())
                ->method('toArrayOfSummaryTableHeader')
                ->willReturn($dynamicTableHeader = ['string represet dynamic table header']);
        $summaryTableHeader = [
            'Participant',
            'Mentor',
            $dynamicTableHeader[0],
        ];
        $this->assertEquals($summaryTableHeader, $this->evaluationPlan->toArrayOfSummaryTableHeader());
    }
    
    public function test_generateSummaryTableEntryFromRecord_returnReportFormGenerateTableEntryResult()
    {
        $this->reportForm->expects($this->once())
                ->method('generateSummaryTableEntryFromRecord')
                ->with($this->formRecord);
        $this->evaluationPlan->generateSummaryTableEntryFromRecord($this->formRecord);
    }
}

class TestableEvaluationPlan extends EvaluationPlan
{
    public $program;
    public $id = 'evaluation-plan-id';
    public $name = 'evaluation plan name';
    public $interval;
    public $disabled = false;
    public $reportForm;
    public $mission;
    
    function __construct()
    {
        parent::__construct();
    }
}
