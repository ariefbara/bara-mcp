<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Config\EventList;
use Personnel\Domain\Model\Firm\ {
    Personnel\ProgramConsultant,
    Program\Participant\Worksheet\Comment
};
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class ConsultantCommentTest extends TestBase
{

    protected $consultantComment;
    protected $programConsultant;
    protected $comment;
    protected $id = 'id';

    protected function setUp(): void
    {
        parent::setUp();
        $this->programConsultant = $this->buildMockOfClass(ProgramConsultant::class);
        $this->comment = $this->buildMockOfClass(Comment::class);
        $this->consultantComment = new TestableConsultantComment($this->programConsultant, 'id', $this->comment);
    }
    
    protected function executeConstruct()
    {
        return new TestableConsultantComment($this->programConsultant, $this->id, $this->comment);
    }
    public function test_construct_setProperties()
    {
        $consultantComment = $this->executeConstruct();
        $this->assertEquals($this->programConsultant, $consultantComment->programConsultant);
        $this->assertEquals($this->id, $consultantComment->id);
        $this->assertEquals($this->comment, $consultantComment->comment);
    }
    public function test_construct_logCommentActivity()
    {
        $this->comment->expects($this->once())
                ->method("logActivity")
                ->with($this->programConsultant);
        $this->executeConstruct();
    }
    public function test_construct_recordCommentSubmittedByConsultantEvent()
    {
        $consultantComment = $this->executeConstruct();
        $event = new CommonEvent(EventList::COMMENT_SUBMITTED_BY_CONSULTANT, $this->id);
        $this->assertEquals($event, $consultantComment->recordedEvents[0]);
    }
    
    public function test_remove_removeComment()
    {
        $this->comment->expects($this->once())
                ->method("remove");
        $this->consultantComment->remove();
    }

}

class TestableConsultantComment extends ConsultantComment
{

    public $programConsultant;
    public $id;
    public $comment;
    public $recordedEvents;

}
