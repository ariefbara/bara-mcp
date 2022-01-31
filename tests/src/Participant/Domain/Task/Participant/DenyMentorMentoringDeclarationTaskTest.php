<?php

namespace Participant\Domain\Task\Participant;

use Tests\src\Participant\Domain\Task\Participant\TaskExecutableByParticipantTestBase;

class DenyMentorMentoringDeclarationTaskTest extends TaskExecutableByParticipantTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDeclaredMentoringRelatedAsset();
        
        $this->task = new DenyMentorMentoringDeclarationTask($this->declaredMentoringRepository, $this->declaredMentoringId);
    }
    
    protected function execute()
    {
        $this->task->execute($this->participant);
    }
    public function test_execute_denyDeclaration()
    {
        $this->declaredMentoring->expects($this->once())
                ->method('denyMentorDeclaration');
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
