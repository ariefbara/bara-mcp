<?php

namespace Personnel\Domain\Task\Coordinator;

use SharedContext\Domain\ValueObject\LabelData;
use Tests\src\Personnel\Domain\Task\Coordinator\CoordinatorTaskTestBase;

class SubmitTaskTest extends CoordinatorTaskTestBase
{
    protected $service;
    protected $payload, $labelData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCoordinatorTaskDependency();
        $this->setUpParticipantDependency();
        
        $this->service = new SubmitTask($this->coordinatorTaskRepository, $this->participantRepository);
        
        $this->labelData = new LabelData('name', 'description');
        $this->payload = new SubmitTaskPayload($this->participantId, $this->labelData);
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
                ->with($this->coordinatorTaskId, $this->participant, $this->labelData)
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
