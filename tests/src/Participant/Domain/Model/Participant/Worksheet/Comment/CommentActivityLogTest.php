<?php

namespace Participant\Domain\Model\Participant\Worksheet\Comment;

use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    Model\Participant\Worksheet\Comment,
    SharedModel\ActivityLog
};
use Tests\TestBase;

class CommentActivityLogTest extends TestBase
{
    protected $comment;
    protected $commentActivityLog;
    protected $id = "newId", $message = "new message";
    protected $teamMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->comment = $this->buildMockOfClass(Comment::class);
        $this->commentActivityLog = new TestableCommentActivityLog($this->comment, "id", "message", null);
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
    }
    
    public function test_construct_setProperties()
    {
        $commentActivityLog = new TestableCommentActivityLog($this->comment, $this->id, $this->message, $this->teamMember);
        $this->assertEquals($this->comment, $commentActivityLog->comment);
        $this->assertEquals($this->id, $commentActivityLog->id);
        
        $activityLog = new ActivityLog($this->id, $this->message, $this->teamMember);
        $this->assertEquals($activityLog, $commentActivityLog->activityLog);
    }
}

class TestableCommentActivityLog extends CommentActivityLog
{
    public $comment;
    public $id;
    public $activityLog;
}
