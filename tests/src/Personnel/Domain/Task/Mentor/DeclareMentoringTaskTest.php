<?php

namespace Personnel\Domain\Task\Mentor;

use DateTimeImmutable;
use SharedContext\Domain\ValueObject\ScheduleData;
use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class DeclareMentoringTaskTest extends MentorTaskTestBase
{

    protected $scheduleData;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setDeclaredMentoringRelatedTask();
        $this->setParticipantRelatedTask();
        $this->setConsultationSetupRelatedTask();

        $this->scheduleData = $this->buildMockOfClass(ScheduleData::class);
        $payload = new DeclareMentoringPayload($this->participantId, $this->consultationSetupId, $this->scheduleData);
        $this->task = new DeclareMentoringTask(
                $this->declaredMentoringRepository, $this->participantRepository, $this->consultationSetupRepository,
                $payload);
    }

    protected function execute()
    {
        $this->declaredMentoringRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->declaredMentoringId);
        $this->task->execute($this->mentor);
    }
    public function test_execute_addDeclaredMentoringFromMentorToRepository()
    {
        $this->mentor->expects($this->once())
                ->method('declareMentoring')
                ->with($this->declaredMentoringId, $this->participant, $this->consultationSetup, $this->scheduleData)
                ->willReturn($this->declaredMentoring);
        $this->declaredMentoringRepository->expects($this->once())
                ->method('add')
                ->with($this->declaredMentoring);
        $this->execute();
    }
    public function test_execute_setDeclaredMentoringIdResult()
    {
        $this->execute();
        $this->assertEquals($this->declaredMentoringId, $this->task->declaredMentoringId);
    }

}
