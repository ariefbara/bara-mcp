<?php

namespace Personnel\Domain\Model\Firm\Program\Participant;

use Personnel\Domain\Model\Firm\Program\Participant;
use Resources\DateTimeImmutableBuilder;
use SharedContext\Domain\ValueObject\Label;
use SharedContext\Domain\ValueObject\LabelData;
use Tests\TestBase;

class TaskTest extends TestBase
{
    protected $participant;
    protected $labelData;
    protected $task, $label;
    //
    protected $id = 'newId';
    //
    protected $newLabel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->labelData = new LabelData('name', 'description');
        
        $this->task = new TestableTask($this->participant, 'id', $this->labelData);
        $this->label = $this->buildMockOfClass(Label::class);
        $this->task->label = $this->label;
        $this->task->modifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy('-1 days');
        
        $this->newLabel = $this->buildMockOfClass(Label::class);
    }
    
    //
    protected function construct()
    {
        return new TestableTask($this->participant, $this->id, $this->labelData);
    }
    public function test_construct_setProperties()
    {
        $task = $this->construct();
        $this->assertSame($this->participant, $task->participant);
        $this->assertSame($this->id, $task->id);
        $this->assertFalse($task->cancelled);
        $this->assertInstanceOf(Label::class, $task->label);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $task->createdTime);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $task->modifiedTime);
    }
    
    //
    protected function update()
    {
        $this->label->expects($this->once())
                ->method('update')
                ->with($this->labelData)
                ->willReturn($this->newLabel);
        $this->task->update($this->labelData);
    }
    public function test_update_updateLabel()
    {
        $this->update();
        $this->assertSame($this->newLabel, $this->task->label);
    }
    public function test_updateLabel_updateModifiedTime()
    {
        $this->update();
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $this->task->modifiedTime);
    }
    public function test_updateLabel_sameLabel_preventUpdateModifiedTime()
    {
        $this->label->expects($this->once())
                ->method('sameValueAs')
                ->with($this->newLabel)
                ->willReturn(true);
        $previousModifiedTime = $this->task->modifiedTime;
        
        $this->update();
        $this->assertEquals($previousModifiedTime, $this->task->modifiedTime);
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
}

class TestableTask extends Task
{
    public $participant;
    public $id;
    public $cancelled;
    public $label;
    public $createdTime;
    public $modifiedTime;
}
