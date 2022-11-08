<?php

namespace SharedContext\Domain\Model;

use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class NoteTest extends TestBase
{
    protected $note;
    protected $id = 'newId', $content = 'new content';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->note = new TestableNote('id', 'content');
        $this->note->modifiedTime = new \DateTimeImmutable('-1 days');
    }
    
    protected function construct()
    {
        return new TestableNote($this->id, $this->content);
    }
    public function test_construct_setProperties()
    {
        $note = $this->construct();
        $this->assertSame($this->id, $note->id);
        $this->assertSame($this->content, $note->content);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $note->createdTime);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $note->modifiedTime);
        $this->assertFalse($note->removed);
    }
    public function test_construct_emptyContent_forbidden()
    {
        $this->content = '';
        $this->assertRegularExceptionThrowed(function () {
            $this->construct();
        }, 'Bad Request', 'note content is required');
    }
    
    protected function update()
    {
        $this->note->update($this->content);
    }
    public function test_update_updateContent()
    {
        $this->update();
        $this->assertSame($this->content, $this->note->content);
    }
    public function test_update_emptyContent_badRequest()
    {
        $this->content = '';
        $this->assertRegularExceptionThrowed(function () {
            $this->update();
        }, 'Bad Request', 'note content is required');
    }
    public function test_update_updateModifiedTime()
    {
        $this->update();
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $this->note->modifiedTime);
    }
    public function test_update_sameContent_keepModifiedTime()
    {
        $modifiedTime = $this->note->modifiedTime;
        $this->content = $this->note->content;
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
    public $content;
    public $createdTime;
    public $modifiedTime;
    public $removed;
}
