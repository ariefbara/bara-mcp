<?php

namespace Personnel\Domain\Task\Mentor;

use SharedContext\Domain\ValueObject\LabelData;
use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class SubmitTaskTest extends MentorTaskTestBase
{
    protected $task;
    protected $payload, $labelData;
    protected function setUp(): void
    {
        parent::setUp();
        $this->setConsultantTaskRelatedTask();
        $this->setParticipantRelatedTask();
        
        $this->task = new SubmitTask($this->consultantTaskRepository, $this->participantRepository);
        //
        $this->labelData = new LabelData('name', 'description');
        $this->payload = new SubmitTaskPayload($this->participantId, $this->labelData);
    }
    
    protected function execute()
    {
        $this->consultantTaskRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->consultantTaskId);
        
        $this->task->execute($this->mentor, $this->payload);
    }
    public function test_execute_addConsultantTaskSubmittedByMentorToRepository()
    {
        $this->mentor->expects($this->once())
                ->method('submitTask')
                ->with($this->consultantTaskId, $this->participant, $this->labelData)
                ->willReturn($this->consultantTask);
        
        $this->consultantTaskRepository->expects($this->once())
                ->method('add')
                ->with($this->consultantTask);
        
        $this->execute();
    }
    public function test_execute_setPayloadSubmittedId()
    {
        $this->execute();
        $this->assertSame($this->payload->submittedTaskId, $this->consultantTaskId);
    }
}
