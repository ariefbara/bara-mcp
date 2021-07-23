<?php

namespace Personnel\Domain\Task\DedicatedMentor;

use Resources\Application\Event\Dispatcher;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\src\Personnel\Domain\Task\DedicatedMentor\EvaluationReportTestBase;

class SubmitEvaluationReportTaskTest extends EvaluationReportTestBase
{
    protected $formRecordData;
    protected $task;
    protected $dispatcher;


    protected function setUp(): void
    {
        parent::setUp();
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        
        $payload = new EvaluationReportPayload($this->evaluationPlanId, $this->formRecordData);
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->task = new SubmitEvaluationReportTask($this->evaluationPlanRepository, $payload, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->task->execute($this->dedicatedMentor);
    }
    
    public function test_execute_dedicateMentorSubmitEvaluationReport()
    {
        $this->dedicatedMentor->expects($this->once())
                ->method('submitEvaluationReport')
                ->with($this->evaluationPlan, $this->formRecordData);
        $this->execute();
    }
    public function test_execute_dispatchDedicatedMentor()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->dedicatedMentor);
        $this->execute();
    }
    
}
