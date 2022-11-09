<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use Personnel\Domain\Model\Firm\Personnel\Coordinator\CoordinatorNote;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Task\Coordinator\CoordinatorTask;
use Tests\TestBase;

class CoordinatorTest extends TestBase
{
    protected $coordinator;
    //
    protected $coordinatorTask, $payload = 'string represent task payload';
    //
    protected $coordinatorNoteId = 'coordinatorNoteId', $participant, $content = 'note content', $viewableByParticipant = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinator = new TestableCoordinator();
        //
        $this->coordinatorTask = $this->buildMockOfInterface(CoordinatorTask::class);
        //
        $this->participant = $this->buildMockOfClass(Participant::class);
    }
    
    protected function executeTask()
    {
        $this->coordinator->executeTask($this->coordinatorTask, $this->payload);
    }
    public function test_executeTask_executeTask()
    {
        $this->coordinatorTask->expects($this->once())
                ->method('execute')
                ->with($this->coordinator, $this->payload);
        $this->executeTask();
    }
    public function test_executeTask_inactiveCoordinator_forbidden()
    {
        $this->coordinator->active = false;
        $this->assertRegularExceptionThrowed(function() {
            $this->executeTask();
        }, 'Forbidden', 'only active coordinator can make this request');
    }
    
    //
    protected function submitNote()
    {
        return $this->coordinator->submitNote($this->coordinatorNoteId, $this->participant, $this->content, $this->viewableByParticipant);
    }
    public function test_submitNote_returnCoordinatorNote()
    {
        $this->assertInstanceOf(CoordinatorNote::class, $this->submitNote());
    }
    public function test_submitNote_assertParticipantUsableInProgram()
    {
        $this->participant->expects($this->once())
                ->method('assertUsableInProgram')
                ->with($this->coordinator->programId);
        $this->submitNote();
    }
}

class TestableCoordinator extends Coordinator
{
    public $personnel;
    public $programId = 'programId';
    public $id = 'coordinatorId';
    public $active = true;
    
    function __construct()
    {
        parent::__construct();
    }
}
