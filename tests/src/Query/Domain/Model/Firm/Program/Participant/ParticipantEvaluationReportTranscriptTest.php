<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Query\Domain\Model\Firm\Program\EvaluationPlan\EvaluationPlanReportSummary;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Tests\TestBase;

class ParticipantEvaluationReportTranscriptTest extends TestBase
{
    protected $transcript;
    protected $evaluationReport;
    protected $evaluationPlanReportSummary, $evaluationPlanOneName = 'evaluation plan one', 
            $transcriptOneTable = ['string represent table one'], $evaluationPlanOneId = 'evaluation-plan-one-id';
    protected $evaluationPlanReportSummaryTwo, $evaluationPlanTwoName = 'evaluation plan two', 
            $transcriptTwoTable = ['string represent table two'], $evaluationPlanTwoId = 'evaluation-plan-two-id';
    protected $spreadsheet, $worksheet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transcript = new TestableParticipantEvaluationReportTranscript();
        
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        
        $this->evaluationPlanReportSummary = $this->buildMockOfClass(EvaluationPlanReportSummary::class);
        $this->evaluationPlanReportSummaryTwo = $this->buildMockOfClass(EvaluationPlanReportSummary::class);
        
        $this->evaluationPlanReportSummary->expects($this->any())->method('getEvaluationPlanName')->willReturn($this->evaluationPlanOneName);
        $this->evaluationPlanReportSummaryTwo->expects($this->any())->method('getEvaluationPlanName')->willReturn($this->evaluationPlanTwoName);
        
        $this->evaluationPlanReportSummary->expects($this->any())->method('toTrascriptTableArray')->willReturn($this->transcriptOneTable);
        $this->evaluationPlanReportSummaryTwo->expects($this->any())->method('toTrascriptTableArray')->willReturn($this->transcriptTwoTable);
        
        $this->evaluationPlanReportSummary->expects($this->any())->method('getEvaluationPlanId')->willReturn($this->evaluationPlanOneId);
        $this->evaluationPlanReportSummaryTwo->expects($this->any())->method('getEvaluationPlanId')->willReturn($this->evaluationPlanTwoId);
        
        $this->spreadsheet = $this->buildMockOfClass(Spreadsheet::class);
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
    }
    
    public function test_construct_setEvaluatinPlanReportSummariesArray()
    {
        $transcript = new TestableParticipantEvaluationReportTranscript();
        $this->assertEquals([], $transcript->evaluationPlanReportSummaries);
    }
    
    protected function includeEvaluationReport()
    {
        $this->transcript->evaluationPlanReportSummaries[] = $this->evaluationPlanReportSummary;
        $this->transcript->includeEvaluationReport($this->evaluationReport);
    }
    public function test_includeEvaluationReport_addEvaluationPlanReportSummaryCreatedInReportToCollection()
    {
        $this->evaluationReport->expects($this->once())
                ->method('createEvaluationPlanReportSummary')
                ->willReturn($this->evaluationPlanReportSummaryTwo);
        $this->includeEvaluationReport();
        $this->assertEquals(
                [$this->evaluationPlanReportSummary, $this->evaluationPlanReportSummaryTwo], 
                $this->transcript->evaluationPlanReportSummaries);
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
        $this->assertEquals(1, count($this->transcript->evaluationPlanReportSummaries));
    }
    
    protected function saveToSpreadsheet()
    {
        $this->transcript->evaluationPlanReportSummaries[] = $this->evaluationPlanReportSummary;
        $this->transcript->evaluationPlanReportSummaries[] = $this->evaluationPlanReportSummaryTwo;
        return $this->transcript->saveToSpreadsheet($this->spreadsheet);
    }
    public function test_saveToSpreadsheet_saveTranscriptArrayToWorksheet()
    {
        $this->spreadsheet->expects($this->once())
                ->method('getActiveSheet')
                ->willReturn($this->worksheet);
        
        $transcriptTable = [
            [$this->evaluationPlanOneName],
            $this->transcriptOneTable,
            [],
            [$this->evaluationPlanTwoName],
            $this->transcriptTwoTable,
        ];
        $this->worksheet->expects($this->once())
                ->method('fromArray')
                ->with($transcriptTable);
        $this->saveToSpreadsheet();
    }
    
    protected function toArray()
    {
        $this->transcript->evaluationPlanReportSummaries[] = $this->evaluationPlanReportSummary;
        $this->transcript->evaluationPlanReportSummaries[] = $this->evaluationPlanReportSummaryTwo;
        return $this->transcript->toArray();
    }
    public function test_toArray_returnTranscriptArray()
    {
        $result = [
            [
                'evaluationPlan' => [
                    'id' => $this->evaluationPlanOneId,
                    'name' => $this->evaluationPlanOneName,
                ],
                'transcriptTable' => $this->transcriptOneTable
            ],
            [
                'evaluationPlan' => [
                    'id' => $this->evaluationPlanTwoId,
                    'name' => $this->evaluationPlanTwoName,
                ],
                'transcriptTable' => $this->transcriptTwoTable
            ],
        ];
        $this->assertEquals($result, $this->toArray());
    }
    
}

class TestableParticipantEvaluationReportTranscript extends ParticipantEvaluationReportTranscript
{
    public $evaluationPlanReportSummaries;
}
