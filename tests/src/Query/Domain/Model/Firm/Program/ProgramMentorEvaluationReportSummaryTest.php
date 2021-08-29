<?php

namespace Query\Domain\Model\Firm\Program;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Program\EvaluationPlan\EvaluationPlanReportSummary;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Tests\TestBase;

class ProgramMentorEvaluationReportSummaryTest extends TestBase
{
    protected $summary;
    protected $evaluationPlanReportSummary;
    protected $evaluationPlanReportSummaryTwo;
    protected $evaluationReport;
    protected $spreadsheet;
    protected $worksheet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->summary = new TestableProgramMentorEvaluationReportSummary();
        $this->evaluationPlanReportSummary = $this->buildMockOfClass(EvaluationPlanReportSummary::class);
        $this->evaluationPlanReportSummaryTwo = $this->buildMockOfClass(EvaluationPlanReportSummary::class);
        $this->summary->evaluationPlanReportSummaries[] = $this->evaluationPlanReportSummary;
        
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->spreadsheet = $this->buildMockOfClass(Spreadsheet::class);
    }
    
    protected function includeEvaluationReport()
    {
        $this->summary->includeEvaluationReport($this->evaluationReport);
    }
    public function test_includeEvaluationReport_addEvaluationPlanReportSummaryCreatedInReportToCollection()
    {
        $this->evaluationReport->expects($this->once())
                ->method('createEvaluationPlanReportSummary')
                ->willReturn($this->evaluationPlanReportSummaryTwo);
        $this->includeEvaluationReport();
        $this->assertEquals(
                [$this->evaluationPlanReportSummary, $this->evaluationPlanReportSummaryTwo], 
                $this->summary->evaluationPlanReportSummaries);
    }
    public function test_includeEvaluationReport_containEvaluationPlanSummaryCorresponginToReport_includeReportToCorrepondingEvaluationPlanReportSummary()
    {
        $this->evaluationPlanReportSummary->expects($this->once())
                ->method('canInclude')
                ->with($this->evaluationReport)
                ->willReturn(true);
        $this->evaluationPlanReportSummary->expects($this->once())
                ->method('includeEvaluationReport')
                ->with($this->evaluationReport);
        $this->includeEvaluationReport();
    }
    public function test_includeEvaluationReport_containEvaluationPlanSummaryCorresponginToReport_preventAddNewSummary()
    {
        $this->evaluationPlanReportSummary->expects($this->once())
                ->method('canInclude')
                ->with($this->evaluationReport)
                ->willReturn(true);
        $this->includeEvaluationReport();
        $this->assertEquals(1, count($this->summary->evaluationPlanReportSummaries));
    }
    
    protected function saveToSpreadsheet()
    {
        $this->summary->evaluationPlanReportSummaries[] = $this->evaluationPlanReportSummaryTwo;
        $this->summary->saveToSpreadsheet($this->spreadsheet);
    }
    public function test_saveToSpreadsheet_removeDefaultActiveWorksheet()
    {
        $this->spreadsheet->expects($this->once())
                ->method('getActiveSheetIndex')
                ->willReturn(1);
        $this->spreadsheet->expects($this->once())
                ->method('removeSheetByIndex')
                ->with(1);
        $this->saveToSpreadsheet();
    }
    public function test_saveToSpreadsheet_saveAllEvaluationPlanReportSummaryToSpreadsheet()
    {
        $this->evaluationPlanReportSummary->expects($this->once())
                ->method('saveToSpreadsheet')
                ->with($this->spreadsheet);
        $this->evaluationPlanReportSummaryTwo->expects($this->once())
                ->method('saveToSpreadsheet')
                ->with($this->spreadsheet);
        $this->saveToSpreadsheet();
    }
    
    protected function saveToClientSummaryTableSpreadsheet()
    {
        $this->summary->evaluationPlanReportSummaries[] = $this->evaluationPlanReportSummaryTwo;
        $this->summary->saveToClientSummaryTableSpreadsheet($this->spreadsheet);
    }
    public function test_saveToClientSummaryTableSpreadsheet_removeDefaultActiveWorksheet()
    {
        $this->spreadsheet->expects($this->once())
                ->method('getActiveSheetIndex')
                ->willReturn(1);
        $this->spreadsheet->expects($this->once())
                ->method('removeSheetByIndex')
                ->with(1);
        $this->saveToClientSummaryTableSpreadsheet();
    }
    public function test_saveToClientSummaryTableSpreadsheet_saveAllEvaluationPlanReportSummaryToClientSummaryTableSpreadsheet()
    {
        $this->evaluationPlanReportSummary->expects($this->once())
                ->method('saveToClientSummaryTableSpreadsheet')
                ->with($this->spreadsheet);
        $this->evaluationPlanReportSummaryTwo->expects($this->once())
                ->method('saveToClientSummaryTableSpreadsheet')
                ->with($this->spreadsheet);
        $this->saveToClientSummaryTableSpreadsheet();
    }
}

class TestableProgramMentorEvaluationReportSummary extends ProgramMentorEvaluationReportSummary
{
    public $evaluationPlanReportSummaries;
}
