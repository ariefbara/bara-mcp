<?php

namespace Participant\Domain\Task\Participant\LearningProgress;

use Tests\src\Participant\Domain\Task\Participant\ParticipantTaskTestBase;

class MarkLearningProgressCompleteTest extends ParticipantTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupLearningProgressDependency();
        //
        $this->task = new MarkLearningProgressComplete($this->learningProgressRepository);
    }
    
    //
    protected function execute()
    {
        $this->task->execute($this->participant, $this->learningProgressId);
    }
    public function test_execute_updateProgressMark()
    {
        $this->learningProgress->expects($this->once())
                ->method('markComplete');
        $this->execute();
    }
    public function test_execute_assertLearningProgressManageableByParticipant()
    {
        $this->learningProgress->expects($this->once())
                ->method('assertManageableByParticipant')
                ->with($this->participant);
        $this->execute();
    }
}
