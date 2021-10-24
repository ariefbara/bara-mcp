<?php

namespace Participant\Domain\Task\Participant;

use Tests\src\Participant\Domain\Task\Participant\ConsultationSessionTaskTestBase;

class CancelConsultationSessionTaskTest extends ConsultationSessionTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->task = new CancelConsultationSessionTask($this->consultationSessionRepository, $this->consultationSessionId);
    }
    
    protected function execute()
    {
        $this->task->execute($this->participant);
    }
    public function test_execute_cancelConsultationSession()
    {
        $this->consultationSession->expects($this->once())
                ->method('cancel');
        $this->execute();
    }
    public function test_execute_assertSessionManageableByParticipant()
    {
        $this->consultationSession->expects($this->once())
                ->method('assertManageableByParticipant')
                ->with($this->participant);
        $this->execute();
    }
}
