<?php

namespace Participant\Domain\Model\Participant\Worksheet;

use Participant\Domain\ {
    DependencyModel\Firm\Program\Consultant\ConsultantComment,
    DependencyModel\Firm\Team,
    Model\Participant\Worksheet
};
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class CommentTest extends TestBase
{
    protected $comment;
    protected $worksheet;
    protected $consultantComment;
    
    protected $id = 'commentId', $message = 'new message';

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        
        $this->comment = new TestableComment($this->worksheet, 'id', 'root');
        
        $this->consultantComment = $this->buildMockOfClass(ConsultantComment::class);
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
    public function test_remove_commentBelongsToConsultant_forbiddenError()
    {
        $this->comment->consultantComment = $this->consultantComment;
        $operation = function (){
            $this->comment->remove();
        };
        $errorDetail = 'forbidden: unable to remove consultant comment';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
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
}
