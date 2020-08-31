<?php

namespace Participant\Domain\Model\Participant\Worksheet;

use Participant\Domain\Model\Participant\Worksheet;
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class CommentTest extends TestBase
{
    protected $comment;
    protected $worksheet;
    
    protected $id = 'commentId', $message = 'new message';

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        
        $this->comment = new TestableComment($this->worksheet, 'id', 'root');
    }
    
    public function test_construct_setProperties()
    {
        $comment = new TestableComment($this->worksheet, $this->id, $this->message);
        $this->assertEquals($this->worksheet, $comment->worksheet);
        $this->assertNull($comment->parent);
        $this->assertEquals($this->id, $comment->id);
        $this->assertEquals($this->message, $comment->message);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $comment->submitTime);
        $this->assertFalse($comment->removed);
    }
    
    public function test_createReply_returnRepliedComment()
    {
        $reply = new TestableComment($this->worksheet, $this->id, $this->message);
        $reply->parent = $this->comment;
        
        $this->assertEquals($reply, $this->comment->createReply($this->id, $this->message));
    }
    
    public function test_remove_setRemovedTrue()
    {
        $this->comment->remove();
        $this->assertTrue($this->comment->removed);
    }
}

class TestableComment extends Comment
{
    public $worksheet;
    public $parent;
    public $id;
    public $message;
    public $submitTime;
    public $removed;
}
