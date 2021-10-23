<?php

namespace Query\Domain\Task\Client;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Tests\src\Query\Domain\Task\Client\TaskExecutableByClientTestBase;

class GenerateTranscriptTaskTest extends TaskExecutableByClientTestBase
{
    protected $evaluationReportRepository;
    protected $evaluationReport;
    protected $task;
    protected $clientTranscripTableCollection;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->evaluationReportRepository = $this->buildMockOfInterface(EvaluationReportRepository::class);
        $this->evaluationReportRepository->expects($this->any())
                ->method('allNonPaginatedActiveEvaluationReportCorrespondWithClient')
                ->with($this->clientId)
                ->willReturn([$this->evaluationReport]);
        
        $this->task = new TestableGenerateTranscriptTask($this->evaluationReportRepository);
        
        $this->clientTranscripTableCollection = $this->buildMockOfClass(ClientTranscriptTableCollection::class);
        $this->task->clientTranscriptTableCollection = $this->clientTranscripTableCollection;
    }
    
    protected function construct()
    {
        return new TestableGenerateTranscriptTask($this->evaluationReportRepository);
    }
    public function test_construct_setProperties()
    {
        $task = $this->construct();
        $this->assertSame($this->evaluationReportRepository, $task->evaluationReportRepository);
        $this->assertNull($task->result);
        $this->assertInstanceOf(ClientTranscriptTableCollection::class, $task->clientTranscriptTableCollection);
    }
    
    protected function execute()
    {
        $this->task->execute($this->clientId);
    }
    public function test_execute_includeAllEvaluationReportToClientTranscriptTableCollection()
    {
        $this->clientTranscripTableCollection->expects($this->once())
                ->method('include')
                ->with($this->evaluationReport);
        $this->execute();
    }
    public function test_execute_setClientTranscriptTableCollectionPerProgramRelationalArrayAsResult()
    {
        $this->clientTranscripTableCollection->expects($this->once())
                ->method('toRelationalArray')
                ->willReturn($result = ['represent array of results']);
        $this->execute();
        $this->assertSame($result, $this->task->result);
    }
}

class TestableGenerateTranscriptTask extends GenerateTranscriptTask
{
    public $evaluationReportRepository;
    public $result;
    public $clientTranscriptTableCollection;
}
