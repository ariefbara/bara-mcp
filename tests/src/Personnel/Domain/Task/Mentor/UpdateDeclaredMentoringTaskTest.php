<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class UpdateDeclaredMentoringTaskTest extends MentorTaskTestBase
{
    protected $scheduleData;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDeclaredMentoringRelatedTask();
        
        $startTime = new \DateTimeImmutable('-25 hours');
        $endTime = new \DateTimeImmutable('-25 hours');
        $this->scheduleData = new \SharedContext\Domain\ValueObject\ScheduleData($startTime, $endTime, 'media type', 'location');
        $payload = new UpdateDeclaredMentoringPayload($this->declaredMentoringId, $this->scheduleData);
        $this->task = new UpdateDeclaredMentoringTask($this->declaredMentoringRepository, $payload);
    }
    
    protected function execute()
    {
        $this->task->execute($this->mentor);
    }
    public function test_execute_updateDeclaredMentoring()
    {
        $this->declaredMentoring->expects($this->once())
                ->method('update')
                ->with($this->scheduleData);
        $this->execute();
    }
    public function test_execute_assertDeclaredMentoringBelongsToMentor()
    {
        $this->declaredMentoring->expects($this->once())
                ->method('assertManageableByMentor')
                ->with($this->mentor);
        $this->execute();
    }
}
