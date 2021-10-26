<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\ConsultationSessionTaskTestBase;

class DenyDeclaredConsultationSessionTaskTest extends ConsultationSessionTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->task = new DenyDeclaredConsultationSessionTask($this->consultationSessionRepository, $this->consultationSessionId);
    }
    
    protected function execute()
    {
        $this->task->execute($this->mentor);
    }
    public function test_execute_denyConsultationSession()
    {
        $this->consultationSession->expects($this->once())
                ->method('deny');
        $this->execute();
    }
    public function test_execute_assertConsultationSessionManageableByMentor()
    {
        $this->consultationSession->expects($this->once())
                ->method('assertManageableByMentor');
        $this->execute();
    }
}
