<?php

namespace Participant\Domain\Task\Participant\LearningProgress;

use Tests\src\Participant\Domain\Task\Participant\ParticipantTaskTestBase;

class UpdateLearningProgressMarkTest extends ParticipantTaskTestBase
{
    protected $task;
    protected $learningProgressData, $progressMark = 'progress mark';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupLearningProgressDependency();
        //
        $this->task = new UpdateLearningProgressMark($this->learningProgressRepository);
        //
        $this->learningProgressData = (new \Participant\Domain\Model\Participant\LearningProgressData())
                ->setProgressMark($this->progressMark);
        $this->learningProgressData->id = $this->learningProgressId;
    }
    
    //
    protected function execute()
    {
        $this->task->execute($this->participant, $this->learningProgressData);
    }
    public function test_execute_updateProgressMark()
    {
        $this->learningProgress->expects($this->once())
                ->method('updateProgressMark')
                ->with($this->progressMark);
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
