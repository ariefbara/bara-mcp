<?php

namespace Personnel\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

use Personnel\Domain\ {
    Model\Firm\Personnel\ProgramConsultant,
    Model\Firm\Program\Participant\Worksheet\Comment,
    SharedModel\ActivityLog
};
use Tests\TestBase;

class CommentActivityLogTest extends TestBase
{
    protected $comment;
    protected $consultant;
    protected $id = "newId", $message = "new message";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->comment = $this->buildMockOfClass(Comment::class);
        $this->consultant = $this->buildMockOfClass(ProgramConsultant::class);
    }
    
    public function test_construct_setProperties()
    {
        $commentActivityLog = new TestableCommentActivityLog($this->comment, $this->id, $this->message, $this->consultant);
        $this->assertEquals($this->comment, $commentActivityLog->comment);
        $this->assertEquals($this->id, $commentActivityLog->id);
        
        $activityLog = new ActivityLog($this->id, $this->message, $this->consultant);
        $this->assertEquals($activityLog, $commentActivityLog->activityLog);
    }
    
}

class TestableCommentActivityLog extends CommentActivityLog
{
    public $comment;
    public $id;
    public $activityLog;
}
