<?php

namespace Participant\Domain\Task\Participant;

use Tests\src\Participant\Domain\Task\Participant\TaskExecutableByParticipantTestBase;

class ApproveMentorMentoringDeclarationTaskTest extends TaskExecutableByParticipantTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDeclaredMentoringRelatedAsset();
        
        $this->task = new ApproveMentorMentoringDeclarationTask($this->declaredMentoringRepository, $this->declaredMentoringId);
    }
    
    protected function execute()
    {
        $this->task->execute($this->participant);
    }
    public function test_execute_approveDeclaration()
    {
        $this->declaredMentoring->expects($this->once())
                ->method('approveMentorDeclaration');
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
