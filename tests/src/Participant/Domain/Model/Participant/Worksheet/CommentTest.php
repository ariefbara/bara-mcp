<?php

namespace Participant\Domain\Model\Participant\Worksheet;

use Config\EventList;
use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    DependencyModel\Firm\Program\Consultant\ConsultantComment,
    DependencyModel\Firm\Team,
    Model\Participant\Worksheet,
    Model\Participant\Worksheet\Comment\CommentActivityLog
};
use Resources\ {
    DateTimeImmutableBuilder,
    Domain\Event\CommonEvent
};
use Tests\TestBase;

class CommentTest extends TestBase
{
    protected $comment;
    protected $worksheet;
    protected $consultantComment;
    
    protected $id = 'commentId', $message = 'new message';
    protected $teamMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        
        $this->comment = new TestableComment($this->worksheet, 'id', 'root', null);
        $this->comment->commentActivityLogs->clear();
        
        $this->consultantComment = $this->buildMockOfClass(ConsultantComment::class);
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableComment($this->worksheet, $this->id, $this->message, null);
    }
    public function test_construct_setProperties()
    {
        $comment = $this->executeConstruct();
        $this->assertEquals($this->worksheet, $comment->worksheet);
        $this->assertNull($comment->parent);
        $this->assertEquals($this->id, $comment->id);
        $this->assertEquals($this->message, $comment->message);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $comment->submitTime);
        $this->assertFalse($comment->removed);
    }
    public function test_construct_logActivity()
    {
        $comment = $this->executeConstruct();
        $this->assertInstanceOf(CommentActivityLog::class, $comment->commentActivityLogs->first());
    }
    
    protected function executeCreateReply()
    {
        return $this->comment->createReply($this->id, $this->message, null);
    }
    public function test_createReply_returnRepliedComment()
    {
        $this->assertInstanceOf(Comment::class, $this->executeCreateReply());
    }
    public function test_createReply_aConsultantComment_recordEventInReply()
    {
        $this->comment->consultantComment = $this->consultantComment;
        $event = new CommonEvent(EventList::COMMENT_FROM_CONSULTANT_REPLIED, $this->id);
        $reply = $this->executeCreateReply();
        $this->assertEquals($event, $reply->pullRecordedEvents()[0]);
    }
    
    protected function executeRemove()
    {
        $this->comment->remove($this->teamMember);
    }
    public function test_remove_setRemovedTrue()
    {
        $this->executeRemove();
        $this->assertTrue($this->comment->removed);
    }
    public function test_remove_commentBelongsToConsultant_forbiddenError()
    {
        $this->comment->consultantComment = $this->consultantComment;
        $operation = function (){
            $this->executeRemove();
        };
        $errorDetail = 'forbidden: unable to remove consultant comment';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_remove_logActivity()
    {
        $this->executeRemove();
        $this->assertInstanceOf(CommentActivityLog::class, $this->comment->commentActivityLogs->first());
    }
    
    public function test_getWorksheetId_returnWorksheetsGetIdResult()
    {
        $this->worksheet->expects($this->once())
                ->method('getId');
        $this->comment->getWorksheetId();
    }
    
    public function test_isConsultantComment_returnFalse()
    {
        $this->assertFalse($this->comment->isConsultantComment());
    }
    public function test_isConsultantComment_commentIsConsultantComment_returnTrue()
    {
        $this->comment->consultantComment = $this->consultantComment;
        $this->assertTrue($this->comment->isConsultantComment());
    }
    
    public function test_belongsToTeam_returnWorksheetBelongsToTeamResult()
    {
        $this->worksheet->expects($this->once())
                ->method("belongsToTeam")
                ->with($team = $this->buildMockOfClass(Team::class))
                ->willReturn(true);
        $this->assertTrue($this->comment->belongsToTeam($team));
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
    public $consultantComment;
    public $commentActivityLogs;
}
