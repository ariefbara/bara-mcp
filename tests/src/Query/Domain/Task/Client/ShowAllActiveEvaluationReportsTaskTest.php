<?php

namespace Query\Domain\Task\Client;

use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Query\Domain\Task\PaginationPayload;
use Tests\src\Query\Domain\Task\Client\TaskExecutableByClientTestBase;

class ShowAllActiveEvaluationReportsTaskTest extends TaskExecutableByClientTestBase
{
    protected $evaluationReportRepository;
    protected $payload;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationReportRepository = $this->buildMockOfInterface(EvaluationReportRepository::class);
        $this->payload = new PaginationPayload(1, 25);
        $this->task = new ShowAllActiveEvaluationReportsTask($this->evaluationReportRepository, $this->payload);
    }
    
    protected function execute()
    {
        $this->task->execute($this->clientId);
    }
    public function test_execute_setRepositoryQueryAsResult()
    {
        $this->evaluationReportRepository->expects($this->once())
                ->method('allActiveEvaluationReportCorrespondWithClient')
                ->with($this->clientId, 1, 25)
                ->willReturn($result = ['active evaluation report list result']);
        $this->execute();
        $this->assertEquals($result, $this->task->result);
    }
}
