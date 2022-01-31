<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class ApproveParticipantMentoringDeclarationTaskTest extends MentorTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDeclaredMentoringRelatedTask();
        
        $this->task = new ApproveParticipantMentoringDeclarationTask($this->declaredMentoringRepository, $this->declaredMentoringId);
    }
    
    protected function execute()
    {
        $this->task->execute($this->mentor);
    }
    public function test_execute_approvedDeclaredMentoring()
    {
        $this->declaredMentoring->expects($this->once())
                ->method('approveParticipantDeclaration');
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
