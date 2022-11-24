<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Model\Firm\Program\Participant\Task;
use SharedContext\Domain\ValueObject\LabelData;
use Tests\TestBase;

class ConsultantTaskTest extends TestBase
{
    protected $consultant;
    protected $participant;
    protected $labelData;
    protected $consultantTask, $task;
    //
    protected $id = 'newId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = $this->buildMockOfClass(ProgramConsultant::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->labelData = new LabelData('name', 'description');
        
        $this->consultantTask = new TestableConsultantTask($this->consultant, $this->participant, 'id', $this->labelData);
        $this->task = $this->buildMockOfClass(Task::class);
        $this->consultantTask->task = $this->task;
    }
    
    protected function construct()
    {
        return new TestableConsultantTask($this->consultant, $this->participant, $this->id, $this->labelData);
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
        $this->consultantTask->update($this->labelData);
    }
    public function test_update_updateTask()
    {
        $this->task->expects($this->once())
                ->method('update')
                ->with($this->labelData);
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
}

class TestableConsultantTask extends ConsultantTask
{
    public $consultant;
    public $id;
    public $task;
}
