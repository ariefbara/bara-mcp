<?php

namespace Participant\Domain\Task\Participant\LearningProgress;

use Participant\Domain\Model\Participant\LearningProgressData;
use Tests\src\Participant\Domain\Task\Participant\ParticipantTaskTestBase;

class SubmitLearningProgressTest extends ParticipantTaskTestBase
{
    protected $task;
    protected $payload, $learningProgressData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupLearningMaterialDependency();
        //
        $this->task = new SubmitLearningProgress($this->learningMaterialRepository);
        //
        $this->learningProgressData = (new LearningProgressData())
                ->setProgressMark('progress mark')
                ->setMarkAsCompleted(false);
        $this->payload = (new SubmitLearningProgressPayload())
                ->setLearningProgressData($this->learningProgressData)
                ->setLearningMaterialId($this->learningMaterialId);
    }
    
    //
    protected function execute()
    {
        $this->task->execute($this->participant, $this->payload);
    }
    public function test_execute_participantSubmitLearningProgress()
    {
        $this->participant->expects($this->once())
                ->method('submitLearningProgress')
                ->with($this->learningMaterial, $this->learningProgressData);
        $this->execute();
    }
    public function test_execute_assertLearningMaterialAccessibleByParticipant()
    {
        $this->learningMaterial->expects($this->once())
                ->method('assertAccessibleByParticipant')
                ->with($this->participant);
        $this->execute();
    }
}
