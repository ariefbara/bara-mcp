<?php

namespace Query\Domain\Model\Firm\Program\EvaluationPlan;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Query\Domain\Model\Firm\Program\EvaluationPlan;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Tests\TestBase;

class EvaluationPlanReportSummaryTest extends TestBase
{
    protected $evaluationPlan;
    protected $evaluationReport;
    protected $summary;
    protected $spreadsheet, $worksheet;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->summary = new TestableEvaluationPlanReportSummary($this->evaluationPlan, $this->evaluationReport);
        
        $this->spreadsheet = $this->buildMockOfClass(Spreadsheet::class);
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
    }
    
    public function test_construct_setProperties()
    {
        $summary = new TestableEvaluationPlanReportSummary($this->evaluationPlan, $this->evaluationReport);
        $this->assertEquals($this->evaluationPlan, $summary->evaluationPlan);
        $this->assertEquals([$this->evaluationReport], $summary->mentorEvaluationReports);
    }
    
    public function test_includeEvaluationReport_addEvaluationReportToList()
    {
        $this->summary->mentorEvaluationReports = [];
        $this->summary->includeEvaluationReport($this->evaluationReport);
        $this->assertEquals([$this->evaluationReport], $this->summary->mentorEvaluationReports);
    }
    
    public function test_canInclude_returnEvaluationReportsEvaluationPlanEqualsResult()
    {
        $this->evaluationReport->expects($this->once())
                ->method('evaluationPlanEquals')
                ->with($this->evaluationPlan);
        $this->summary->canInclude($this->evaluationReport);
    }
    
    protected function saveToSpreadsheet()
    {
        $this->spreadsheet->expects($this->once())
                ->method('createSheet')
                ->willReturn($this->worksheet);
        $this->summary->saveToSpreadsheet($this->spreadsheet);
    }
    public function test_saveToSpreadsheet_addSummaryArrayToNewWorksheet()
    {
        $this->evaluationPlan->expects($this->once())
                ->method('toArrayOfSummaryTableHeader')
                ->willReturn($tableHeaderArray = ['string represent table headers']);
        $this->evaluationReport->expects($this->once())
                ->method('toArrayOfSummaryTableEntry')
                ->willReturn($tableEntryArray = ['string represent table entry']);
        $summaryArray = [
            $tableHeaderArray,
            $tableEntryArray,
        ];
        
        $this->worksheet->expects($this->once())
                ->method('fromArray')
                ->with($summaryArray);
        $this->saveToSpreadsheet();
    }
    public function test_saveToSpreadsheet_setEvaluationPlanNameAsWorksheetTitle()
    {
        $this->evaluationPlan->expects($this->once())
                ->method('getName')
                ->willReturn($evaluationPlanName = 'evaluation plan name');
        
        $this->worksheet->expects($this->once())
                ->method('setTitle')
                ->with($evaluationPlanName);
        $this->saveToSpreadsheet();
    }
    
    protected function toTrascriptTableArray()
    {
        return $this->summary->toTrascriptTableArray();
    }
    public function test_toTrascriptTableArray_returnTranscriptTable()
    {
        $this->evaluationPlan->expects($this->once())
                ->method('toArrayOfHorizontalTranscriptTableHeader')
                ->willReturn(['Mentor', 'field-one']);
        $this->evaluationReport->expects($this->once())
                ->method('toArrayOfHorizontalTranscriptTableEntry')
                ->willReturn(['mentor name', 'field-value']);
        
        $this->assertEquals([
            ['Mentor', 'mentor name'],
            ['field-one', 'field-value'],
        ], $this->summary->toTrascriptTableArray());
    }
    
    protected function saveToClientSummaryTableSpreadsheet()
    {
        $this->spreadsheet->expects($this->once())
                ->method('createSheet')
                ->willReturn($this->worksheet);
        $this->summary->saveToClientSummaryTableSpreadsheet($this->spreadsheet);
    }
    public function test_saveToClientSummaryTableSpreadsheet_addSummaryArrayToNewWorksheet()
    {
        $this->evaluationPlan->expects($this->once())
                ->method('toArrayOfSummaryTableHeader')
                ->willReturn($tableHeaderArray = ['string represent table headers']);
        $summaryArray = [
            $tableHeaderArray,
        ];
        
        $this->worksheet->expects($this->once())
                ->method('fromArray')
                ->with($summaryArray);
        $this->saveToClientSummaryTableSpreadsheet();
    }
    public function test_saveToClientSummaryTableSpreadsheet_setEvaluationPlanNameAsWorksheetTitle()
    {
        $this->evaluationPlan->expects($this->once())
                ->method('getName')
                ->willReturn($evaluationPlanName = 'evaluation plan name');
        
        $this->worksheet->expects($this->once())
                ->method('setTitle')
                ->with($evaluationPlanName);
        $this->saveToClientSummaryTableSpreadsheet();
    }
}

class TestableEvaluationPlanReportSummary extends EvaluationPlanReportSummary
{
    public $evaluationPlan;
    public $mentorEvaluationReports;
}
