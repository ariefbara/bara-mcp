<?php

namespace Participant\Domain\Task\Participant;

use SharedContext\Domain\ValueObject\ScheduleData;
use Tests\src\Participant\Domain\Task\Participant\TaskExecutableByParticipantTestBase;

class DeclareMentoringTaskTest extends TaskExecutableByParticipantTestBase
{
    protected $scheduleData;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDeclaredMentoringRelatedAsset();
        $this->setMentorRelatedAsset();
        $this->setConsultationSetupRelatedAsset();
        
        $this->scheduleData = $this->buildMockOfClass(ScheduleData::class);
        $payload = new DeclareMentoringPayload($this->mentorId, $this->consultationSetupId, $this->scheduleData);
        
        $this->task = new DeclareMentoringTask(
                $this->declaredMentoringRepository, $this->mentorRepository, $this->consultationSetupRepository, $payload);
    }
    
    protected function execute()
    {
        $this->declaredMentoringRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->declaredMentoringId);
        $this->task->execute($this->participant);
    }
    public function test_execute_addMentoringDelcaredByParticipantToRepository()
    {
        $this->participant->expects($this->once())
                ->method('declareMentoring')
                ->with($this->declaredMentoringId, $this->mentor, $this->consultationSetup, $this->scheduleData)
                ->willReturn($this->declaredMentoring);
        
        $this->declaredMentoringRepository->expects($this->once())
                ->method('add')
                ->with($this->declaredMentoring);
        
        $this->execute();
    }
    public function test_execute_setDeclaredMentoringId()
    {
        $this->execute();
        $this->assertSame($this->declaredMentoringId, $this->task->declaredMentoringId);
    }
}
