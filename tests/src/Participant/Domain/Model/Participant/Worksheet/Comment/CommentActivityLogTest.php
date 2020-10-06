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
    protected $activityLog;
    protected $id = "newId", $message = "new message";
    protected $teamMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->comment = $this->buildMockOfClass(Comment::class);
        $this->commentActivityLog = new TestableCommentActivityLog($this->comment, "id", "message");
        
        $this->activityLog = $this->buildMockOfClass(ActivityLog::class);
        $this->commentActivityLog->activityLog = $this->activityLog;
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
    }
    
    public function test_construct_setProperties()
    {
        $commentActivityLog = new TestableCommentActivityLog($this->comment, $this->id, $this->message);
        $this->assertEquals($this->comment, $commentActivityLog->comment);
        $this->assertEquals($this->id, $commentActivityLog->id);
        
        $activityLog = new ActivityLog($this->id, $this->message);
        $this->assertEquals($activityLog, $commentActivityLog->activityLog);
    }
    
    public function test_setOperator_executeActivityLogsSetOperatorMethod()
    {
        $this->activityLog->expects($this->once())
                ->method("setOperator")
                ->with($this->teamMember);
        $this->commentActivityLog->setOperator($this->teamMember);
    }
}

class TestableCommentActivityLog extends CommentActivityLog
{
    public $comment;
    public $id;
    public $activityLog;
}
