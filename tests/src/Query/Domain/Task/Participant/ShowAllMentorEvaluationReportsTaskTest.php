<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Query\Domain\Task\PaginationPayload;
use Tests\src\Query\Domain\Task\Participant\EvaluationReportTaskTestBase;

class ShowAllMentorEvaluationReportsTaskTest extends EvaluationReportTaskTestBase
{
    protected $payload;
    protected $task;
    
    protected $participantId = 'participantId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->payload = new PaginationPayload(1, 25);
        $this->task = new ShowAllMentorEvaluationReportsTask($this->evaluationReportRepository, $this->payload);
    }
    protected function execute()
    {
        $this->task->execute($this->participantId);
    }
    public function test_execute_setRepositoryQueryToResult()
    {
        $this->evaluationReportRepository->expects($this->once())
                ->method('allActiveEvaluationReportsBelongsToParticipant')
                ->with($this->participantId, $this->payload->getPage(), $this->payload->getPageSize())
                ->willReturn($result = ['array of evaluation report query result']);
        $this->execute();
        $this->assertEquals($result, $this->task->result);
    }
}
