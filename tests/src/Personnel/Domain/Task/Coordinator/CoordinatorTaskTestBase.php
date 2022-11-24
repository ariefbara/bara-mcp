<?php

namespace Tests\src\Personnel\Domain\Task\Coordinator;

use Personnel\Domain\Model\Firm\Personnel\Coordinator;
use Personnel\Domain\Model\Firm\Personnel\Coordinator\CoordinatorNote;
use Personnel\Domain\Model\Firm\Personnel\Coordinator\CoordinatorTask;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Coordinator\CoordinatorNoteRepository;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Coordinator\CoordinatorTaskRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ParticipantRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class CoordinatorTaskTestBase extends TestBase
{

    /**
     * 
     * @var MockObject
     */
    protected $coordinator;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
    }
    
    /**
     * 
     * @var MockObject
     */
    protected $coordinatorNoteRepository;
    /**
     * 
     * @var MockObject
     */
    protected $coordinatorNote;
    protected $coordinatorNoteId = 'coordinatorNoteId';
    protected function setUpCoordinatorNoteDependency()
    {
        $this->coordinatorNoteRepository = $this->buildMockOfInterface(CoordinatorNoteRepository::class);
        $this->coordinatorNote = $this->buildMockOfClass(CoordinatorNote::class);
        $this->coordinatorNoteRepository->expects($this->any())
                ->method('ofId')
                ->with($this->coordinatorNoteId)
                ->willReturn($this->coordinatorNote);
    }
    
    /**
     * 
     * @var MockObject
     */
    protected $participantRepository;
    /**
     * 
     * @var MockObject
     */
    protected $participant;
    protected $participantId = 'participantId';
    protected function setUpParticipantDependency()
    {
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->participantId)
                ->willReturn($this->participant);
    }
    
    /**
     * 
     * @var MockObject
     */
    protected $coordinatorTaskRepository;
    /**
     * 
     * @var MockObject
     */
    protected $coordinatorTask;
    protected $coordinatorTaskId = 'coordinatorTaskId';
    protected function setUpCoordinatorTaskDependency()
    {
        $this->coordinatorTaskRepository = $this->buildMockOfInterface(CoordinatorTaskRepository::class);
        $this->coordinatorTask = $this->buildMockOfClass(CoordinatorTask::class);
        $this->coordinatorTaskRepository->expects($this->any())
                ->method('ofId')
                ->with($this->coordinatorTaskId)
                ->willReturn($this->coordinatorTask);
    }
    

}
