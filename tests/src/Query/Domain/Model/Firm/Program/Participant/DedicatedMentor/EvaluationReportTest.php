<?php

namespace Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;

use Query\Domain\Model\Firm\Program\EvaluationPlan;
use Query\Domain\Model\Firm\Program\EvaluationPlan\EvaluationPlanReportSummary;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Query\Domain\Model\Shared\FormRecord;
use Tests\TestBase;

class EvaluationReportTest extends TestBase
{
    protected $evaluationReport;
    protected $dedicatedMentor;
    protected $evaluationPlan;
    protected $formRecord;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationReport = new TestableEvaluationReport();
        
        $this->dedicatedMentor = $this->buildMockOfClass(DedicatedMentor::class);
        $this->evaluationReport->dedicatedMentor = $this->dedicatedMentor;
        
        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->evaluationReport->evaluationPlan = $this->evaluationPlan;
        
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->evaluationReport->formRecord = $this->formRecord;
    }
    
    public function test_evaluationPlanEquals_sameEvaluationPlan_returnTrue()
    {
        $this->assertTrue($this->evaluationReport->evaluationPlanEquals($this->evaluationPlan));
    }
    public function test_evaluationPlanEquals_differentEvaluationPlan_returnTrue()
    {
        $this->evaluationReport->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->assertFalse($this->evaluationReport->evaluationPlanEquals($this->evaluationPlan));
    }
    
    public function test_createEvaluationPlanReportSummary_returnEvaluationPlanReportSummary()
    {
        $evaluationPlanReportSummary = new EvaluationPlanReportSummary($this->evaluationPlan, $this->evaluationReport);
        $this->assertEquals($evaluationPlanReportSummary, $this->evaluationReport->createEvaluationPlanReportSummary());
    }
    
    public function test_toArrayOfSummaryTableEntry_returnSummaryTableEntryArray()
    {
        $this->dedicatedMentor->expects($this->once())
                ->method('getParticipantName')
                ->willReturn($participantName = 'participant name');
        
        $this->dedicatedMentor->expects($this->once())
                ->method('getMentorName')
                ->willReturn($mentorName = 'mentor name');
        
        $this->evaluationPlan->expects($this->once())
                ->method('generateSummaryTableEntryFromRecord')
                ->with($this->formRecord)
                ->willReturn($formattedReporfFormValues = ['array represent report form values']);
        $summaryTableEntry = [
            $participantName,
            $mentorName,
            $formattedReporfFormValues[0],
        ];
        $this->assertEquals($summaryTableEntry, $this->evaluationReport->toArrayOfSummaryTableEntry());
    }
}

class TestableEvaluationReport extends EvaluationReport
{
    public $dedicatedMentor;
    public $evaluationPlan;
    public $id = 'evaluation-report-id';
    public $modifiedTime;
    public $cancelled = false;
    public $formRecord;
    
    function __construct()
    {
        parent::__construct();
    }
}
