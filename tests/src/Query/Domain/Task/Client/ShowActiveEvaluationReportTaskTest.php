<?php

namespace Query\Domain\Task\Client;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Tests\src\Query\Domain\Task\Client\TaskExecutableByClientTestBase;

class ShowActiveEvaluationReportTaskTest extends TaskExecutableByClientTestBase
{
    protected $evaluationReportRepository;
    protected $id = 'evaluationReportId';
    protected $task;
    
    protected $evaluationReport;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationReportRepository = $this->buildMockOfInterface(EvaluationReportRepository::class);
        $this->task = new ShowActiveEvaluationReportTask($this->evaluationReportRepository, $this->id);
        
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
    }
    
    protected function execute()
    {
        $this->task->execute($this->clientId);
    }
    public function test_execute_setEvaluationReportFromRepositoryAsResult()
    {
        $this->evaluationReportRepository->expects($this->once())
                ->method('anActiveEvaluationReportCorrespondWithClient')
                ->with($this->clientId, $this->id)
                ->willReturn($this->evaluationReport);
        $this->execute();
        $this->assertEquals($this->evaluationReport, $this->task->result);
    }
}
