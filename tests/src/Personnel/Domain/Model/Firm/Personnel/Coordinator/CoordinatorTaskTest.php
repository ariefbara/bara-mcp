<?php

namespace Personnel\Domain\Model\Firm\Personnel\Coordinator;

use Personnel\Domain\Model\Firm\Personnel\Coordinator;
use Personnel\Domain\Model\Firm\Program\Participant;
use SharedContext\Domain\ValueObject\LabelData;
use Tests\TestBase;

class CoordinatorTaskTest extends TestBase
{
    protected $coordinator;
    protected $participant;
    protected $labelData;
    protected $coordinatorTask, $task;
    protected $id = 'newId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->labelData = new LabelData('name', 'description');
        
        $this->coordinatorTask = new TestableCoordinatorTask($this->coordinator, $this->participant, $this->id, $this->labelData);
        $this->task = $this->buildMockOfClass(Participant\Task::class);
        $this->coordinatorTask->task = $this->task;
    }
    
    //
    protected function construct()
    {
        return new TestableCoordinatorTask($this->coordinator, $this->participant, $this->id, $this->labelData);
    }
    public function test_construct_setProperties()
    {
        $coordinatorTask = $this->construct();
        $this->assertSame($this->coordinator, $coordinatorTask->coordinator);
        $this->assertSame($this->id, $coordinatorTask->id);
        $this->assertInstanceOf(Participant\Task::class, $coordinatorTask->task);
    }
    
    //
    protected function update()
    {
        $this->coordinatorTask->update($this->labelData);
    }
    public function test_update_updateTask()
    {
        $this->task->expects($this->once())
                ->method('update');
        $this->update();
    }
    
    //
    protected function cancel()
    {
        $this->coordinatorTask->cancel();
    }
    public function test_cancel_cancelTask()
    {
        $this->task->expects($this->once())
                ->method('cancel');
        $this->cancel();
    }
    
    //
    protected function assertManageableByCoordinator()
    {
        $this->coordinatorTask->assertManageableByCoordinator($this->coordinator);
    }
    public function test_assertManageableByCoordinator_differentCoordinator_forbidden()
    {
        $this->coordinatorTask->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->assertRegularExceptionThrowed(function () {
            $this->assertManageableByCoordinator();
        }, 'Forbidden', 'unmanaged coordinator task, can only managed owned task');
    }
    public function test_assertManageablyByCoordinator_sameCoordinator_void()
    {
        $this->assertManageableByCoordinator();
        $this->markAsSuccess();
    }
}

class TestableCoordinatorTask extends CoordinatorTask
{
    public $coordinator;
    public $id;
    public $task;
}
