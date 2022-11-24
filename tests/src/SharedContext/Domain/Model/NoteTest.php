<?php

namespace SharedContext\Domain\Model;

use DateTimeImmutable;
use Resources\DateTimeImmutableBuilder;
use SharedContext\Domain\ValueObject\Label;
use SharedContext\Domain\ValueObject\LabelData;
use Tests\TestBase;

class NoteTest extends TestBase
{
    protected $note, $label;
    protected $id = 'newId', $name = 'new name', $description = 'new description';
    protected $newLabel;

    protected function setUp(): void
    {
        parent::setUp();
        $labelData = new LabelData('name', 'description');
        $this->note = new TestableNote('id', $labelData);
        $this->note->modifiedTime = new DateTimeImmutable('-1 days');
        $this->label = $this->buildMockOfClass(Label::class);
        $this->note->label = $this->label;
        
        $this->newLabel = $this->buildMockOfClass(Label::class);
    }
    
    protected function getLabelData()
    {
        return new LabelData($this->name, $this->description);
    }
    
    protected function construct()
    {
        return new TestableNote($this->id, $this->getLabelData());
    }
    public function test_construct_setProperties()
    {
        $note = $this->construct();
        $this->assertSame($this->id, $note->id);
        $this->assertInstanceOf(Label::class, $note->label);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $note->createdTime);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $note->modifiedTime);
        $this->assertFalse($note->removed);
    }
    
    protected function update()
    {
        $this->label->expects($this->any())
                ->method('update')
                ->with($this->getLabelData())
                ->willReturn($this->newLabel);
        $this->note->update($this->getLabelData());
    }
    public function test_update_updateLabel()
    {
        $this->update();
        $this->assertSame($this->newLabel, $this->note->label);
    }
    public function test_update_updateModifiedTime()
    {
        $this->update();
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $this->note->modifiedTime);
    }
    public function test_update_sameLabel_keepModifiedTime()
    {
        $this->newLabel->expects($this->once())
                ->method('sameValueAs')
                ->with($this->label)
                ->willReturn(true);
        $modifiedTime = $this->note->modifiedTime;
        $this->update();
        $this->assertEquals($modifiedTime, $this->note->modifiedTime);
    }
    
    protected function remove()
    {
        $this->note->remove();
    }
    public function test_remove_setRemoved()
    {
        $this->remove();
        $this->assertTrue($this->note->removed);
    }
}

class TestableNote extends Note
{
    public $id;
    public $label;
    public $createdTime;
    public $modifiedTime;
    public $removed;
}
