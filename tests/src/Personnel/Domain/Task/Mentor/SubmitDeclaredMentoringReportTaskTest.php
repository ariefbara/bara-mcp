<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class SubmitDeclaredMentoringReportTaskTest extends MentorTaskTestBase
{
    protected $formRecordData, $participantRating = 44;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setDeclaredMentoringRelatedTask();
        
        $this->formRecordData = $this->buildMockOfClass(\SharedContext\Domain\Model\SharedEntity\FormRecordData::class);
        $payload = new SubmitMentoringReportPayload($this->declaredMentoringId, $this->participantRating, $this->formRecordData);
        $this->task = new SubmitDeclaredMentoringReportTask($this->declaredMentoringRepository, $payload);
    }
    
    protected function execute()
    {
        $this->task->execute($this->mentor);
    }
    public function test_execute_submitDeclaredMentoringReport()
    {
        $this->declaredMentoring->expects($this->once())
                ->method('submitReport')
                ->with($this->formRecordData, $this->participantRating);
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
