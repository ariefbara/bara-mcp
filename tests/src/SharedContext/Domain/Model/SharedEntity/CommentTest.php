<?php

namespace SharedContext\Domain\Model\SharedEntity;

use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class CommentTest extends TestBase
{
    protected $comment;
    protected $id = 'new-comment-id', $message = 'new comment message';

    protected function setUp(): void
    {
        parent::setUp();

        $this->comment = new TestableWorksheetComment('id', 'message');
    }

    public function test_construct_setProperties()
    {
        $comment = new TestableWorksheetComment($this->id, $this->message);
        $this->assertEquals($this->id, $comment->id);
        $this->assertEquals($this->message, $comment->message);
        $this->assertFalse($comment->removed);
        $this->assertNull($comment->parent);
    }
    public function test_createReply_setProperties()
    {
        $comment = $this->comment->createReply($this->id, $this->message);
        $this->assertEquals($this->id, $comment->id);
        $this->assertEquals($this->message, $comment->message);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $comment->submitTime);
        $this->assertFalse($comment->removed);
    }

    public function test_createReply_setParent()
    {
        $comment = $this->comment->createReply($this->id, $this->message);
        $this->assertEquals($this->comment, $comment->parent);
    }

    public function test_remove_setRemovedFlagTrue()
    {
        $this->comment->remove();
        $this->assertTrue($this->comment->removed);
    }

}

class TestableWorksheetComment extends Comment
{

    public $parent, $id, $message, $submitTime, $removed;

}
