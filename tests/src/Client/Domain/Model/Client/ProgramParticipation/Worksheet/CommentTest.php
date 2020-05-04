<?php

namespace Client\Domain\Model\Client\ProgramParticipation\Worksheet;

use Client\Domain\Model\Client\ {
    ClientNotification,
    ProgramParticipation\Worksheet
};
use Tests\TestBase;

class CommentTest extends TestBase
{

    protected $worksheet;
    protected $comment;
    protected $id = 'new-comment-id', $message = 'new comment message';

    protected function setUp(): void
    {
        parent::setUp();

        $this->worksheet = $this->buildMockOfClass(Worksheet::class);

        $this->comment = TestableWorksheetComment::createNew($this->worksheet, 'id', 'message');
    }

    public function test_createNew_setProperties()
    {
        $comment = TestableWorksheetComment::createNew($this->worksheet, $this->id, $this->message);
        $this->assertEquals($this->worksheet, $comment->worksheet);
        $this->assertEquals($this->id, $comment->id);
        $this->assertEquals($this->message, $comment->message);
        $this->assertEquals($this->YmdHisStringOfCurrentTime(), $comment->submitTime->format('Y-m-d H:i:s'));
        $this->assertFalse($comment->removed);
        $this->assertNull($comment->parent);
    }

    public function test_createReply_setProperties()
    {
        $comment = $this->comment->createReply($this->id, $this->message);
        $this->assertEquals($this->worksheet, $comment->worksheet);
        $this->assertEquals($this->id, $comment->id);
        $this->assertEquals($this->message, $comment->message);
        $this->assertEquals($this->YmdHisStringOfCurrentTime(), $comment->submitTime->format('Y-m-d H:i:s'));
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

    public function test_createClientNotification_returnResultCreateClientNotificationOnWorksheet()
    {
        $id = 'id';
        $message = 'message';
        $this->worksheet->expects($this->once())
                ->method('createClientNotification')
                ->with($id, $message, $this->comment);
        $this->assertInstanceOf(ClientNotification::class, $this->comment->createClientNotification($id, $message));
    }

}

class TestableWorksheetComment extends Comment
{

    public $worksheet, $parent, $id, $message, $submitTime, $removed;

}
