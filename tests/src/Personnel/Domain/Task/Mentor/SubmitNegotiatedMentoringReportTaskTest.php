<?php

namespace Personnel\Domain\Task\Mentor;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class SubmitNegotiatedMentoringReportTaskTest extends MentorTaskTestBase
{
    protected $participantRating = 55, $formRecordData;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupNegotiatedMentoringRelatedTask();
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $payload = new SubmitMentoringReportPayload(
                $this->negotiatedMentoringId, $this->participantRating, $this->formRecordData);
        
        $this->task = new SubmitNegotiatedMentoringReportTask($this->negotiatedMentoringRepository, $payload);
    }
    
    protected function execute()
    {
        $this->task->execute($this->mentor);
    }
    public function test_execute_submitNegotiatedMentoringReport()
    {
        $this->negotiatedMentoring->expects($this->once())
                ->method('submitReport')
                ->with($this->formRecordData, $this->participantRating);
        $this->execute();
    }
    public function test_execute_assertNegotiatedMentoringBelongsToMentor()
    {
        $this->negotiatedMentoring->expects($this->once())
                ->method('assertBelongsToMentor')
                ->with($this->mentor);
        $this->execute();
    }
}
