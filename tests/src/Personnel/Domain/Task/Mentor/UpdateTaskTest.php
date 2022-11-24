<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class UpdateTaskTest extends MentorTaskTestBase
{
    protected $service;
    protected $payload, $labelData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setConsultantTaskRelatedTask();
        
        $this->service = new UpdateTask($this->consultantTaskRepository);
        //
        $this->labelData = new \SharedContext\Domain\ValueObject\LabelData('name', 'description');
        $this->payload = new UpdateTaskPayload($this->consultantTaskId, $this->labelData);
    }
    
    protected function execute()
    {
        $this->service->execute($this->mentor, $this->payload);
    }
    public function test_execute_updateConsultantTask()
    {
        $this->consultantTask->expects($this->once())
                ->method('update')
                ->with($this->labelData);
        $this->execute();
    }
    public function test_execute_assertConsultantTaskManageableByConsultant()
    {
        $this->consultantTask->expects($this->once())
                ->method('assertManageableByConsultant')
                ->with($this->mentor);
        $this->execute();
    }
}
