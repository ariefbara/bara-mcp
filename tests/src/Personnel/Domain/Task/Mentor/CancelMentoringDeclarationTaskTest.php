<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class CancelMentoringDeclarationTaskTest extends MentorTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDeclaredMentoringRelatedTask();
        
        $this->task = new CancelMentoringDeclarationTask($this->declaredMentoringRepository, $this->declaredMentoringId);
    }
    
    protected function execute()
    {
        $this->task->execute($this->mentor);
    }
    public function test_execute_cancelDeclaredMentoring()
    {
        $this->declaredMentoring->expects($this->once())
                ->method('cancel');
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
