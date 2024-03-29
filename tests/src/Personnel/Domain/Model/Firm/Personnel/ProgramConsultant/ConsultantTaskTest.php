<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use DateTimeImmutable;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Model\Firm\Program\Participant\Task;
use Personnel\Domain\Model\Firm\Program\Participant\TaskData;
use SharedContext\Domain\ValueObject\LabelData;
use Tests\TestBase;

class ConsultantTaskTest extends TestBase
{
    protected $consultant;
    protected $participant;
    protected $taskData;
    protected $consultantTask, $task;
    //
    protected $id = 'newId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = $this->buildMockOfClass(ProgramConsultant::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->taskData = new TaskData(new LabelData('name', 'description'), new DateTimeImmutable('+1 months'));
        
        $this->consultantTask = new TestableConsultantTask($this->consultant, $this->participant, 'id', $this->taskData);
        $this->task = $this->buildMockOfClass(Task::class);
        $this->consultantTask->task = $this->task;
    }
    
    protected function construct()
    {
        return new TestableConsultantTask($this->consultant, $this->participant, $this->id, $this->taskData);
    }
    public function test_construct_setProperties()
    {
        $consultantTask = $this->construct();
        $this->assertSame($this->consultant, $consultantTask->consultant);
        $this->assertSame($this->id, $consultantTask->id);
        $this->assertInstanceOf(Task::class, $consultantTask->task);
    }
    
    //
    protected function update()
    {
        $this->consultantTask->update($this->taskData);
    }
    public function test_update_updateTask()
    {
        $this->task->expects($this->once())
                ->method('update')
                ->with($this->taskData);
        $this->update();
    }
    
    //
    protected function cancel()
    {
        $this->consultantTask->cancel();
    }
    public function test_cancel_cancelTask()
    {
        $this->task->expects($this->once())
                ->method('cancel');
        $this->cancel();
    }
    
    //
    protected function assertManageableByConsultant()
    {
        $this->consultantTask->assertManageableByConsultant($this->consultant);
    }
    public function test_assertManageableByConsultant_differentConsultant_forbidden()
    {
        $this->consultantTask->consultant = $this->buildMockOfClass(ProgramConsultant::class);
        $this->assertRegularExceptionThrowed(function () {
            $this->assertManageableByConsultant();
        }, 'Forbidden', 'unmanaged consultant task, can only managed owned task');
    }
    public function test_assertManageableByConsultant_sameConsultant_void()
    {
        $this->assertManageableByConsultant();
        $this->markAsSuccess();
    }
    
    //
    protected function approveReport()
    {
        $this->consultantTask->approveReport();
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
        $this->consultantTask->askForReportRevision();
    }
    public function test_askForReportRevision_askForTaskReportRevision()
    {
        $this->task->expects($this->once())
                ->method('askForReportRevision');
        $this->askForReportRevision();
    }
}

class TestableConsultantTask extends ConsultantTask
{
    public $consultant;
    public $id;
    public $task;
}
