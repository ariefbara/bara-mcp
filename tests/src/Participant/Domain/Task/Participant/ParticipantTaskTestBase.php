<?php

namespace Tests\src\Participant\Domain\Task\Participant;

use Participant\Domain\DependencyModel\Firm\Program\Mission\LearningMaterial;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\LearningProgress;
use Participant\Domain\Model\Participant\ParticipantFileInfo;
use Participant\Domain\Model\Participant\ParticipantNote;
use Participant\Domain\Model\Participant\Task;
use Participant\Domain\Task\Dependency\Firm\Program\Mission\LearningMaterialRepository;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\LearningProgressRepository;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\ParticipantFileInfoRepository;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\ParticipantNoteRepository;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\TaskRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class ParticipantTaskTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $participant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
    }
    
    /**
     * 
     * @var MockObject
     */
    protected $participantNoteRepository;
    /**
     * 
     * @var MockObject
     */
    protected $participantNote;
    protected $participantNoteId = 'participantNoteId';
    protected function setupParticipantNoteDependency()
    {
        $this->participantNoteRepository = $this->buildMockOfInterface(ParticipantNoteRepository::class);
        $this->participantNote = $this->buildMockOfClass(ParticipantNote::class);
        $this->participantNoteRepository->expects($this->any())
                ->method('ofId')
                ->with($this->participantNoteId)
                ->willReturn($this->participantNote);
    }
    
    /**
     * 
     * @var MockObject
     */
    protected $participantFileInfoRepository;
    /**
     * 
     * @var MockObject
     */
    protected $participantFileInfo;
    protected $participantFileInfoId = 'participantFileInfoId';
    protected function setupParticipantFileInfoDependency()
    {
        $this->participantFileInfoRepository = $this->buildMockOfInterface(ParticipantFileInfoRepository::class);
        $this->participantFileInfo = $this->buildMockOfClass(ParticipantFileInfo::class);
        $this->participantFileInfoRepository->expects($this->any())
                ->method('ofId')
                ->with($this->participantFileInfoId)
                ->willReturn($this->participantFileInfo);
    }
    
    /**
     * 
     * @var MockObject
     */
    protected $taskRepository;
    /**
     * 
     * @var MockObject
     */
    protected $task;
    protected $taskId = 'taskId';
    protected function setupTaskDependency()
    {
        $this->taskRepository = $this->buildMockOfInterface(TaskRepository::class);
        $this->task = $this->buildMockOfClass(Task::class);
        $this->taskRepository->expects($this->any())
                ->method('ofId')
                ->with($this->taskId)
                ->willReturn($this->task);
    }
    
    /**
     * 
     * @var MockObject
     */
    protected $learningMaterialRepository;
    /**
     * 
     * @var MockObject
     */
    protected $learningMaterial;
    protected $learningMaterialId = 'learningMaterialId';
    protected function setupLearningMaterialDependency()
    {
        $this->learningMaterialRepository = $this->buildMockOfInterface(LearningMaterialRepository::class);
        $this->learningMaterial = $this->buildMockOfClass(LearningMaterial::class);
        $this->learningMaterialRepository->expects($this->any())
                ->method('ofId')
                ->with($this->learningMaterialId)
                ->willReturn($this->learningMaterial);
    }
    
    /**
     * 
     * @var MockObject
     */
    protected $learningProgressRepository;
    /**
     * 
     * @var MockObject
     */
    protected $learningProgress;
    protected $learningProgressId = 'learningProgressId';
    protected function setupLearningProgressDependency()
    {
        $this->learningProgressRepository = $this->buildMockOfInterface(LearningProgressRepository::class);
        $this->learningProgress = $this->buildMockOfClass(LearningProgress::class);
        $this->learningProgressRepository->expects($this->any())
                ->method('ofId')
                ->with($this->learningProgressId)
                ->willReturn($this->learningProgress);
    }
}
