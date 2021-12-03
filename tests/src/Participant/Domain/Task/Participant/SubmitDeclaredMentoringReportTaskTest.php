<?php

namespace Participant\Domain\Task\Participant;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\src\Participant\Domain\Task\Participant\TaskExecutableByParticipantTestBase;

class SubmitDeclaredMentoringReportTaskTest extends TaskExecutableByParticipantTestBase
{
    protected $formRecordData, $mentorRating = 333;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDeclaredMentoringRelatedAsset();
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $payload = new SubmitMentoringReportPayload($this->declaredMentoringId, $this->mentorRating, $this->formRecordData);
        $this->task = new SubmitDeclaredMentoringReportTask($this->declaredMentoringRepository, $payload);
    }
    
    protected function execute()
    {
        $this->task->execute($this->participant);
    }
    public function test_execute_submitDeclarationReport()
    {
        $this->declaredMentoring->expects($this->once())
                ->method('submitReport')
                ->with($this->formRecordData, $this->mentorRating);
        $this->execute();
    }
    public function test_execute_assertDeclarationManageableByParticipant()
    {
        $this->declaredMentoring->expects($this->once())
                ->method('assertManageableByParticipant')
                ->with($this->participant);
        $this->execute();
    }
}
