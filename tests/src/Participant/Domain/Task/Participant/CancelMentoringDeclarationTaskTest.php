<?php

namespace Participant\Domain\Task\Participant;

use Tests\src\Participant\Domain\Task\Participant\TaskExecutableByParticipantTestBase;

class CancelMentoringDeclarationTaskTest extends TaskExecutableByParticipantTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDeclaredMentoringRelatedAsset();
        
        $this->task = new CancelMentoringDeclarationTask($this->declaredMentoringRepository, $this->declaredMentoringId);
    }
    
    protected function execute()
    {
        $this->task->execute($this->participant);
    }
    public function test_execute_cancelMentoringDeclaration()
    {
        $this->declaredMentoring->expects($this->once())
                ->method('cancel');
        $this->execute();
    }
    public function test_execute_assertMentoringDeclarationManageableByParticipant()
    {
        $this->declaredMentoring->expects($this->once())
                ->method('assertManageableByParticipant')
                ->with($this->participant);
        $this->execute();
    }
}
