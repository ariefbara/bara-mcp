<?php

namespace Personnel\Domain\Task\Coordinator;

use Personnel\Domain\Model\Firm\Program\Participant\TaskData;
use Tests\src\Personnel\Domain\Task\Coordinator\CoordinatorTaskTestBase;

class SubmitTaskTest extends CoordinatorTaskTestBase
{
    protected $service;
    protected $payload, $taskData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCoordinatorTaskDependency();
        $this->setUpParticipantDependency();
        
        $this->service = new SubmitTask($this->coordinatorTaskRepository, $this->participantRepository);
        
        $this->taskData = $this->buildMockOfClass(TaskData::class);
        $this->payload = new SubmitTaskPayload($this->participantId, $this->taskData);
    }
    
    //
    protected function execute()
    {
        $this->coordinatorTaskRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->coordinatorTaskId);
        
        $this->service->execute($this->coordinator, $this->payload);
    }
    public function test_execute_addCoordinatorTaskSubmittedByCoordinatorToRepository()
    {
        $this->coordinator->expects($this->once())
                ->method('submitTask')
                ->with($this->coordinatorTaskId, $this->participant, $this->taskData)
                ->willReturn($this->coordinatorTask);
        
        $this->coordinatorTaskRepository->expects($this->once())
                ->method('add')
                ->with($this->coordinatorTask);
        
        $this->execute();
    }
    public function test_execute_setSubmittedTaskId()
    {
        $this->execute();
        $this->assertSame($this->coordinatorTaskId, $this->payload->submittedTaskId);
    }
}
