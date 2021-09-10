<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Tests\src\Query\Domain\Task\Participant\EvaluationReportTaskTestBase;

class ShowEvaluationReportTaskTest extends EvaluationReportTaskTestBase
{
    protected $id = 'evaluationReportId';
    protected $task;
    
    protected $evaluationReport;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->task = new ShowEvaluationReportTask($this->evaluationReportRepository, $this->id);
        
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
    }
    
    protected function execute()
    {
        $this->task->execute($this->participantId);
    }
    public function test_execute_setEvaluationReportFromRepositoryQueryAsResult()
    {
        $this->evaluationReportRepository->expects($this->once())
                ->method('anActiveEvaluationReportBelongsToParticipant')
                ->with($this->participantId, $this->id)
                ->willReturn($this->evaluationReport);
        $this->execute();
        $this->assertEquals($this->evaluationReport, $this->task->result);
    }
}
