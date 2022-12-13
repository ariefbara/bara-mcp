<?php

namespace Personnel\Domain\Model\Firm\Personnel\Coordinator;

use DateTimeImmutable;
use Personnel\Domain\Model\Firm\Personnel\Coordinator;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Model\Firm\Program\Participant\TaskData;
use SharedContext\Domain\ValueObject\LabelData;
use Tests\TestBase;

class CoordinatorTaskTest extends TestBase
{
    protected $coordinator;
    protected $participant;
    protected $taskData;
    protected $coordinatorTask, $task;
    protected $id = 'newId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->taskData = new TaskData(new LabelData('name', 'description'), new DateTimeImmutable('+1 months'));
        
        $this->coordinatorTask = new TestableCoordinatorTask($this->coordinator, $this->participant, $this->id, $this->taskData);
        $this->task = $this->buildMockOfClass(Participant\Task::class);
        $this->coordinatorTask->task = $this->task;
    }
    
    //
    protected function construct()
    {
        return new TestableCoordinatorTask($this->coordinator, $this->participant, $this->id, $this->taskData);
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
        $this->coordinatorTask->update($this->taskData);
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
    
    //
    protected function approveReport()
    {
        $this->coordinatorTask->approveReport();
    }
    public function test_approveReport_approveTaskReport()
    {
        $this->task->expects($this->once())
                ->method('approveReport');
        $this->approveReport();
    }
    
    //
    protected function askForReportRevision()
    {
        $this->coordinatorTask->askForReportRevision();
    }
    public function test_askForReportRevision_askForTaskReportRevision()
    {
        $this->task->expects($this->once())
                ->method('askForReportRevision');
        $this->askForReportRevision();
    }
}

class TestableCoordinatorTask extends CoordinatorTask
{
    public $coordinator;
    public $id;
    public $task;
}
