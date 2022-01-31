<?php

namespace Participant\Domain\Task\Participant;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\src\Participant\Domain\Task\Participant\TaskExecutableByParticipantTestBase;

class SubmitNegotiatedMentoringReportTaskTest extends TaskExecutableByParticipantTestBase
{
    protected $mentorRating = 7, $formRecordData;
    protected $payload;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setNegotiatedMentoringRelatedAsset();
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $payload = new SubmitMentoringReportPayload($this->negotiatedMentoringId, $this->mentorRating, $this->formRecordData);
        $this->task = new SubmitNegotiatedMentoringReportTask($this->negotiatedMentoringRepository, $payload);
    }
    
    public function execute()
    {
        $this->task->execute($this->participant);
    }
    public function test_execute_submitNegotiatedMentoringReport()
    {
        $this->negotiatedMentoring->expects($this->once())
                ->method('submitReport')
                ->with($this->mentorRating, $this->formRecordData);
        $this->execute();
    }
    public function test_execute_assertNegotiatedMentoringManageableByParticipant()
    {
        $this->negotiatedMentoring->expects($this->once())
                ->method('assertManageableByParticipant')
                ->with($this->participant);
        $this->execute();
    }
}
