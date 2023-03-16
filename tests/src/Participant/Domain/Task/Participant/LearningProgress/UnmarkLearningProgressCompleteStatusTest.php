<?php

namespace Participant\Domain\Task\Participant\LearningProgress;

use Tests\src\Participant\Domain\Task\Participant\ParticipantTaskTestBase;

class UnmarkLearningProgressCompleteStatusTest extends ParticipantTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupLearningProgressDependency();
        //
        $this->task = new UnmarkLearningProgressCompleteStatus($this->learningProgressRepository);
    }
    
    //
    protected function execute()
    {
        $this->task->execute($this->participant, $this->learningProgressId);
    }
    public function test_execute_updateProgressMark()
    {
        $this->learningProgress->expects($this->once())
                ->method('unmarkCompleteStatus');
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
