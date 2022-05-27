<?php

namespace Query\Domain\Task\InProgram;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Client\TranscriptTable;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Task\InFirm\ClientEvaluationReportTranscriptResult;
use Tests\TestBase;

class ClientEvaluationReportTranscriptResultTest extends TestBase
{
    protected $result;
    protected $transcriptTable;
    protected $evaluationReport;
    protected $spreadsheet;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->result = new TestableClientEvaluationReportTranscriptResult();
        $this->transcriptTable = $this->buildMockOfClass(TranscriptTable::class);
        $this->result->transcriptTables[] = $this->transcriptTable;
        
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        
        $this->spreadsheet = $this->buildMockOfClass(Spreadsheet::class);
    }
    
    protected function includeEvaluationReport()
    {
        $this->result->includeEvaluationReport($this->evaluationReport);
    }
    public function test_includeEvaluationReport_includeToCorrespondingExistingTable()
    {
        $this->transcriptTable->expects($this->once())
                ->method('canInclude')
                ->with($this->evaluationReport)
                ->willReturn(true);
        $this->transcriptTable->expects($this->once())
                ->method('includeEvaluationReport')
                ->with($this->evaluationReport);
        $this->includeEvaluationReport();
    }
    
    protected function saveToSpreadsheet()
    {
        $this->result->saveToSpreadsheet($this->spreadsheet);
    }
    public function test_saveToSpreadsheet_saveAllTranscriptTableToSpreadsheet()
    {
        $this->transcriptTable->expects($this->once())
                ->method('saveToSpreadsheet')
                ->with($this->spreadsheet);
        $this->saveToSpreadsheet();
    }
    
    public function test_toRelationalArray_returnSetOfAllClientSummaryTableArray()
    {
        $this->transcriptTable->expects($this->once())
                ->method('toRelationalArray')
                ->willReturn($transcripTableRelationalArray = ['string represent array']);
        $result = [
            $transcripTableRelationalArray,
        ];
        $this->assertEquals($result, $this->result->toRelationalArray());
    }
}

class TestableClientEvaluationReportTranscriptResult extends ClientEvaluationReportTranscriptResult
{
    public $transcriptTables;
}
