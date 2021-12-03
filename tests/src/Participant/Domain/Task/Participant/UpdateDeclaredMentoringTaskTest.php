<?php

namespace Participant\Domain\Task\Participant;

use Tests\src\Participant\Domain\Task\Participant\TaskExecutableByParticipantTestBase;

class UpdateDeclaredMentoringTaskTest extends TaskExecutableByParticipantTestBase
{
    protected $scheduleData;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDeclaredMentoringRelatedAsset();
        
        $this->scheduleData = $this->buildMockOfClass(\SharedContext\Domain\ValueObject\ScheduleData::class);
        $payload = new UpdateDeclaredMentoringPayload($this->declaredMentoringId, $this->scheduleData);
        $this->task = new UpdateDeclaredMentoringTask($this->declaredMentoringRepository, $payload);
    }
    
    protected function execute()
    {
        $this->task->execute($this->participant);
    }
    public function test_execute_updateMentoringDeclaration()
    {
        $this->declaredMentoring->expects($this->once())
                ->method('update')
                ->with($this->scheduleData);
        $this->execute();
    }
    public function test_execute_assertDeclaredMentoringManageableByParticipant()
    {
        $this->declaredMentoring->expects($this->once())
                ->method('assertManageableByParticipant')
                ->with($this->participant);
        $this->execute();
    }
}
