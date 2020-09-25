<?php

namespace Participant\Domain\DependencyModel\Firm\Program\Consultant;

use Participant\Domain\Model\Participant\Worksheet\Comment;
use Tests\TestBase;

class ConsultantCommentTest extends TestBase
{
    protected $consultantComment;
    protected $comment;
    
    protected $id = 'commentId', $message = 'message';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantComment = new TestableConsultantComment();
        $this->comment = $this->buildMockOfClass(Comment::class);
        $this->consultantComment->comment = $this->comment;
    }
    
    public function test_createReply_returnCommentCreateReplyResult()
    {
        $this->comment->expects($this->once())
                ->method('createReply')
                ->with($this->id, $this->message);
        
        $this->consultantComment->createReply($this->id, $this->message);
    }
}

class TestableConsultantComment extends ConsultantComment
{
    public $consultant;
    public $id;
    public $comment;
    
    function __construct()
    {
        parent::__construct();
    }
}
