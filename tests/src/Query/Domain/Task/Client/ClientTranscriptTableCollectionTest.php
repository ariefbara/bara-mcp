<?php

namespace Query\Domain\Task\Client;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Firm\Program\Participant\TranscriptTable;
use Tests\TestBase;

class ClientTranscriptTableCollectionTest extends TestBase
{
    protected $collection;
    protected $transcriptTable;
    
    protected $evaluationReport;
    protected $spreadsheet, $summaryStyleView = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->collection = new TestableClientTranscriptTableCollection();
        
        $this->transcriptTable = $this->buildMockOfClass(TranscriptTable::class);
        $this->collection->transcriptTableCollection[] = $this->transcriptTable;
        
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->spreadsheet = $this->buildMockOfClass(Spreadsheet::class);
    }
    
    protected function include()
    {
        $this->collection->include($this->evaluationReport);
    }
    public function test_include_addEvaluationReportAsNewTranscriptTable()
    {
        $this->include();
        $this->assertEquals(2, count($this->collection->transcriptTableCollection));
        $this->assertInstanceOf(TranscriptTable::class, $this->collection->transcriptTableCollection[1]);
    }
    public function test_include_evaluationReportCanBeIncludedInExistingTable_includeToExistingTable()
    {
        $this->transcriptTable->expects($this->once())
                ->method('canInclude')
                ->with($this->evaluationReport)
                ->willReturn(true);
        $this->transcriptTable->expects($this->once())
                ->method('includeEvaluationReport')
                ->with($this->evaluationReport);
        $this->include();
    }
    public function test_include_evaluationReportCanBeIncludedInExistingTable_preventAddNewTable()
    {
        $this->transcriptTable->expects($this->once())
                ->method('canInclude')
                ->with($this->evaluationReport)
                ->willReturn(true);
        $this->include();
        $this->assertEquals(1, count($this->collection->transcriptTableCollection));
    }
    
    protected function saveToSpreadsheet()
    {
        $this->collection->saveToSpreadsheet($this->spreadsheet, $this->summaryStyleView);
    }
    public function test_saveToSpreadsheet_saveAllTranscriptAsProgramSheetOfSpreadsheet()
    {
        $this->transcriptTable->expects($this->once())
                ->method('saveAsProgramSheet')
                ->with($this->spreadsheet, $this->summaryStyleView);
        $this->saveToSpreadsheet();
    }
    
    protected function toRelationalArray()
    {
        return $this->collection->toRelationalArray();
    }
    public function test_toRelationalArray_returnArrayOfAllTranscriptTableRelationalArrayOfProgram()
    {
        $this->transcriptTable->expects($this->once())
                ->method('toRelationalArrayOfProgram')
                ->willReturn($programOne = ['array of evaluation reports in program']);
        $this->assertEquals([$programOne], $this->toRelationalArray());
    }
}

class TestableClientTranscriptTableCollection extends ClientTranscriptTableCollection
{
    public $transcriptTableCollection;
}
