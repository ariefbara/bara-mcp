<?php

namespace Query\Domain\Task\InProgram;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Firm\Program\Participant\TranscriptTable;
use Tests\TestBase;

class ParticipantEvaluationReportTranscriptResultTest extends TestBase
{
    protected $participantEvaluationReportTranscriptResult;
    protected $transcriptTableOne;
    protected $transcriptTableTwo;
    
    protected $evaluationReport;
    protected $spreadsheet;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participantEvaluationReportTranscriptResult = new TestableParticipantEvaluationReportTranscriptResult();
        
        $this->transcriptTableOne = $this->buildMockOfClass(TranscriptTable::class);
        $this->transcriptTableTwo = $this->buildMockOfClass(TranscriptTable::class);
        
        $this->participantEvaluationReportTranscriptResult->transcriptTables[] = $this->transcriptTableOne;
        $this->participantEvaluationReportTranscriptResult->transcriptTables[] = $this->transcriptTableTwo;
        
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->spreadsheet = $this->buildMockOfClass(Spreadsheet::class);
    }
    
    
    public function test_construct_setProperties()
    {
        $participantEvaluationReportTranscriptResult = new TestableParticipantEvaluationReportTranscriptResult();
        $this->assertEquals([], $participantEvaluationReportTranscriptResult->transcriptTables);
    }
    
    protected function includeEvaluationReport()
    {
        $this->participantEvaluationReportTranscriptResult->includeEvaluationReport($this->evaluationReport);
    }
    public function test_includeEvaluationReport_addNewTranscriptTable()
    {
        $this->includeEvaluationReport();
        $this->assertEquals(3, count($this->participantEvaluationReportTranscriptResult->transcriptTables));
        $this->assertInstanceOf(TranscriptTable::class, $this->participantEvaluationReportTranscriptResult->transcriptTables[2]);
    }
    public function test_includeEvaluationReport_hasTableCorrespondWithReport_includeToCorrespondingExistingTable()
    {
        $this->transcriptTableOne->expects($this->once())
                ->method('canInclude')
                ->with($this->evaluationReport)
                ->willReturn(true);
        $this->transcriptTableOne->expects($this->once())
                ->method('includeEvaluationReport')
                ->with($this->evaluationReport);
        $this->includeEvaluationReport();
    }
    public function test_includeEvaluationReport_hasTableCorrespondWithReport_preventAddNewTable()
    {
        $this->transcriptTableOne->expects($this->once())
                ->method('canInclude')
                ->with($this->evaluationReport)
                ->willReturn(true);
        $this->includeEvaluationReport();
        $this->assertEquals(2, count($this->participantEvaluationReportTranscriptResult->transcriptTables));
    }
    
    protected function saveToSpreadsheet()
    {
        $this->participantEvaluationReportTranscriptResult->saveToSpreadsheet($this->spreadsheet);
    }
    public function test_saveToSpreadsheet_saveAllTranscriptTableToSpreadsheet()
    {
        $this->transcriptTableOne->expects($this->once())
                ->method('saveToSpreadsheet')
                ->with($this->spreadsheet);
        $this->transcriptTableTwo->expects($this->once())
                ->method('saveToSpreadsheet')
                ->with($this->spreadsheet);
        $this->saveToSpreadsheet();
    }
    
    public function test_toRelationalArray_returnSetOfAllClientSummaryTableArray()
    {
        $this->transcriptTableOne->expects($this->once())
                ->method('toRelationalArray')
                ->willReturn($transcripTableRelationalArrayOne = ['string represent arrayTwo']);
        $this->transcriptTableTwo->expects($this->once())
                ->method('toRelationalArray')
                ->willReturn($transcripTableRelationalArrayTwo = ['string represent arrayTwo']);
        $result = [
            $transcripTableRelationalArrayOne,
            $transcripTableRelationalArrayTwo,
        ];
        $this->assertEquals($result, $this->participantEvaluationReportTranscriptResult->toRelationalArray());
    }
}

class TestableParticipantEvaluationReportTranscriptResult extends ParticipantEvaluationReportTranscriptResult
{
    public $transcriptTables;
}
