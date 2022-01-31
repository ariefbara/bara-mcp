<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class DenyParticipantMentoringDeclarationTaskTest extends MentorTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDeclaredMentoringRelatedTask();
        $this->task = new DenyParticipantMentoringDeclarationTask($this->declaredMentoringRepository, $this->declaredMentoringId);
    }
    
    protected function execute()
    {
        $this->task->execute($this->mentor);
    }
    public function test_execute_denyDeclaredMentoring()
    {
        $this->declaredMentoring->expects($this->once())
                ->method('denyParticipantDeclaration');
        $this->execute();
    }
    public function test_execute_assertDeclaredMentoringBelongToMentor()
    {
        $this->declaredMentoring->expects($this->once())
                ->method('assertManageableByMentor')
                ->with($this->mentor);
        $this->execute();
    }
}
