<?php

namespace Query\Domain\Task\Client;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Firm\Program\Participant\TranscriptTable;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Tests\src\Query\Domain\Task\Client\TaskExecutableByClientTestBase;

class WriteTranscriptToSpreadsheetTaskTest extends TaskExecutableByClientTestBase
{

    protected $evaluationReportRepository;
    protected $evaluationReport;
    protected $spreadsheet;
    protected $summaryStyleView = false;
    protected $task;
    protected $clientTranscripTableCollection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationReportRepository = $this->buildMockOfInterface(EvaluationReportRepository::class);
        $this->spreadsheet = $this->buildMockOfClass(Spreadsheet::class);
        $this->task = new TestableWriteTranscriptToSpreadsheetTask($this->evaluationReportRepository, $this->spreadsheet, $this->summaryStyleView);
        
        $this->clientTranscripTableCollection = $this->buildMockOfClass(ClientTranscriptTableCollection::class);
        $this->task->clientTranscriptTableCollection = $this->clientTranscripTableCollection;
        
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
    }
    
    protected function construct()
    {
        return new TestableWriteTranscriptToSpreadsheetTask($this->evaluationReportRepository, $this->spreadsheet, $this->summaryStyleView);
    }
    public function test_construct_setProperties()
    {
        $task = $this->construct();
        $this->assertSame($this->evaluationReportRepository, $task->evaluationReportRepository);
        $this->assertSame($this->spreadsheet, $task->spreadsheet);
        $this->assertSame($this->summaryStyleView, $task->summaryStyleView);
        $this->assertInstanceOf(ClientTranscriptTableCollection::class, $task->clientTranscriptTableCollection);
    }

    
    protected function execute()
    {
        $this->evaluationReportRepository->expects($this->once())
                ->method('allNonPaginatedActiveEvaluationReportCorrespondWithClient')
                ->with($this->clientId)
                ->willReturn([$this->evaluationReport]);
        $this->task->execute($this->clientId);
    }
    public function test_execute_includeAllEvaluationReportToClientTranscriptTableCollection()
    {
        $this->clientTranscripTableCollection->expects($this->once())
                ->method('include')
                ->with($this->evaluationReport);
        $this->execute();
    }
    public function test_execute_saveClientTranscripTableCollectionToSpreadsheet()
    {
        $this->clientTranscripTableCollection->expects($this->once())
                ->method('saveToSpreadsheet')
                ->with($this->spreadsheet, $this->summaryStyleView);
        $this->execute();
    }

}

class TestableWriteTranscriptToSpreadsheetTask extends WriteTranscriptToSpreadsheetTask
{
    public $evaluationReportRepository;
    public $spreadsheet;
    public $summaryStyleView;
    public $clientTranscriptTableCollection;
}
