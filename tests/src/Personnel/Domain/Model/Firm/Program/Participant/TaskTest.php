<?php

namespace Personnel\Domain\Model\Firm\Program\Participant;

use DateTimeImmutable;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Model\Firm\Program\Participant\Task\TaskReport;
use Resources\DateTimeImmutableBuilder;
use SharedContext\Domain\ValueObject\Label;
use SharedContext\Domain\ValueObject\LabelData;
use Tests\TestBase;

class TaskTest extends TestBase
{
    protected $participant;
    protected $labelData;
    protected $task, $label, $taskReport;
    //
    protected $id = 'newId';
    //
    protected $dueDate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->labelData = new LabelData('name', 'description');
        
        $taskData = new TaskData($this->labelData, new DateTimeImmutable('+1 weeks'));
        $this->task = new TestableTask($this->participant, 'id', $taskData);
        
        $this->label = $this->buildMockOfClass(Label::class);
        $this->task->label = $this->label;
        
        $this->task->modifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy('-1 days');
        
        $this->taskReport = $this->buildMockOfClass(TaskReport::class);
        $this->task->taskReport = $this->taskReport;
        
        //
        $this->newLabel = $this->buildMockOfClass(Label::class);
        $this->dueDate = new DateTimeImmutable('tomorrow');
    }
    
    protected function getTaskData()
    {
        return new TaskData($this->labelData, $this->dueDate);
    }
    
    //
    protected function construct()
    {
        return new TestableTask($this->participant, $this->id, $this->getTaskData());
    }
    public function test_construct_setProperties()
    {
        $task = $this->construct();
        $this->assertSame($this->participant, $task->participant);
        $this->assertSame($this->id, $task->id);
        $this->assertFalse($task->cancelled);
        $this->assertInstanceOf(Label::class, $task->label);
        $this->assertSame($this->dueDate, $task->dueDate);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $task->createdTime);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $task->modifiedTime);
    }
    public function test_construct_dueDateNonDateTimeImmutableType_typeError()
    {
        $this->dueDate = 'non date time immutable type';
        $this->expectException(\TypeError::class);
        $this->construct();
    }
    public function test_construct_nullDueDate_void()
    {
        $this->dueDate = null;
        $this->construct();
        $this->markAsSuccess();
    }
    public function test_construct_notUpcomingDueDate_badRequest()
    {
        $this->dueDate = new DateTimeImmutable('today');
        $this->assertRegularExceptionThrowed(function() {
            $this->construct();
        }, 'Bad Request', 'if set, due date must be an upcoming date');
    }
    
    //
    protected function update()
    {
        $this->label->expects($this->any())
                ->method('update')
                ->with($this->labelData)
                ->willReturn($this->newLabel);
        $this->task->update($this->getTaskData());
    }
    public function test_update_updateLabelAndDueDate()
    {
        $this->update();
        $this->assertSame($this->newLabel, $this->task->label);
        $this->assertSame($this->dueDate, $this->task->dueDate);
    }
    public function test_updateLabel_updateModifiedTime()
    {
        $this->update();
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $this->task->modifiedTime);
    }
    public function test_updateLabel_sameLabelAndDueDate_preventUpdateModifiedTime()
    {
        $this->dueDate = $this->task->dueDate;
        $this->label->expects($this->once())
                ->method('sameValueAs')
                ->with($this->newLabel)
                ->willReturn(true);
        $previousModifiedTime = $this->task->modifiedTime;
        
        $this->update();
        $this->assertEquals($previousModifiedTime, $this->task->modifiedTime);
    }
    public function test_updateLabel_sameLabelDifferentDueDate_updateModifiedTime()
    {
        $this->label->expects($this->once())
                ->method('sameValueAs')
                ->with($this->newLabel)
                ->willReturn(true);
        $previousModifiedTime = $this->task->modifiedTime;
        
        $this->update();
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $this->task->modifiedTime);
    }
    public function test_update_alreadyCancelled_forbidden()
    {
        $this->task->cancelled = true;
        $this->assertRegularExceptionThrowed(function () {
            $this->update();
        }, 'Forbidden', 'task already cancelled, no further changes allowed');
    }
    public function test_update_nonDateTimeImmutableDueDate_typeError()
    {
        $this->dueDate = 'string';
        $this->expectException(\TypeError::class);
        $this->update();
    }
    
    //
    protected function cancel()
    {
        $this->task->cancel();
    }
    public function test_cancel_setCancelled()
    {
        $this->cancel();
        $this->assertTrue($this->task->cancelled);
    }
    
    //
    protected function approveReport()
    {
        $this->task->approveReport();
    }
    public function test_approveReport_approveReport()
    {
        $this->taskReport->expects($this->once())
                ->method('approve');
        $this->approveReport();
    }
    public function test_approveReport_taskAlreadyCancelled_forbidden()
    {
        $this->task->cancelled = true;
        $this->assertRegularExceptionThrowed(function () {
            $this->approveReport();
        }, 'Forbidden', 'task already cancelled, no further changes allowed');
    }
    
    //
    protected function askForReportRevision()
    {
        $this->task->askForReportRevision();
    }
    public function test_askForReportRevision_askForReportRevision()
    {
        $this->taskReport->expects($this->once())
                ->method('askForRevision');
        $this->askForReportRevision();
    }
    public function test_askForReportRevision_alreadyCancelled_forbidden()
    {
        $this->task->cancelled = true;
        $this->assertRegularExceptionThrowed(function () {
            $this->askForReportRevision();
        }, 'Forbidden', 'task already cancelled, no further changes allowed');
    }
}

class TestableTask extends Task
{
    public $participant;
    public $id;
    public $cancelled;
    public $label;
    public $dueDate;
    public $createdTime;
    public $modifiedTime;
    public $taskReport;
}
