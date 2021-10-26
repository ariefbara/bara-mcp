<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\ConsultationSessionTaskTestBase;

class CancelDeclaredConsultationSessionTaskTest extends ConsultationSessionTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->task = new CancelDeclaredConsultationSessionTask(
                $this->consultationSessionRepository, $this->consultationSessionId);
    }
    
    protected function execute()
    {
        $this->task->execute($this->mentor);
    }
    public function test_execute_cancelConsultationSession()
    {
        $this->consultationSession->expects($this->once())
                ->method('cancel');
        $this->execute();
    }
    public function test_execute_assertConsultatioNSessionManageableByMentor()
    {
        $this->consultationSession->expects($this->once())
                ->method('assertManageableByMentor')
                ->with($this->mentor);
        $this->execute();
    }
}
