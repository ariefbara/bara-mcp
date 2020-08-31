<?php

namespace Notification\Domain\Model\Firm\Program\Participant\Worksheet;

use Notification\Domain\Model\Firm\Program\ {
    Consultant\ConsultantComment,
    Participant\Worksheet
};
use Resources\ {
    Application\Service\Mailer,
    Domain\Model\Mail\Recipient
};
use Tests\TestBase;

class CommentTest extends TestBase
{

    protected $comment;
    protected $worksheet;
    protected $parent;
    protected $consultantComment;
    
    protected $recipient;
    
    protected $mailer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->comment = new TestableComment();
        
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->comment->worksheet = $this->worksheet;

        $this->parent = $this->buildMockOfClass(Comment::class);
        $this->comment->parent = $this->parent;
        
        $this->consultantComment = $this->buildMockOfClass(ConsultantComment::class);
        $this->comment->consultantComment = $this->consultantComment;
        
        $this->recipient = $this->buildMockOfClass(Recipient::class);
        
        $this->mailer = $this->buildMockOfInterface(Mailer::class);
    }
    
    protected function executeGetConsultantWriterMailRecipient()
    {
        return $this->comment->getConsultantWriterMailRecipient();
    }
    
    public function test_getConsultantWriterMailRecipient_return_expectedResult()
    {
        $this->consultantComment->expects($this->once())
                ->method('getConsultantMailRecipient')
                ->willReturn($this->recipient);
        $this->assertEquals($this->recipient, $this->executeGetConsultantWriterMailRecipient());
    }
    public function test_getConsultantWriterMailRecipient_emptyConsultantComment_forbiddenError()
    {
        $this->comment->consultantComment = null;
        $operation = function (){
            $this->executeGetConsultantWriterMailRecipient();
        };
        $errorDetail = 'forbidden: unable to retrieve consultant mail info';
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeSendMailToConsultantWhoseCommentBeingReplied()
    {
        $this->comment->sendMailToConsultantWhoseCommentBeingReplied($this->mailer);
    }
    public function test_sendMailToConsultantWhoseCommentBeingReplied_sendMailToMailer()
    {
        $this->mailer->expects($this->once())
                ->method('send');
        $this->executeSendMailToConsultantWhoseCommentBeingReplied();
    }
    public function test_sendMailToConsultantWhoseCommentBeingReplied_notParentComment_forbiddenError()
    {
        $this->comment->parent = null;
        $operation = function (){
            $this->executeSendMailToConsultantWhoseCommentBeingReplied();
        };
        $errorDetail = 'forbidden: empty parent comment';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

}

class TestableComment extends Comment
{
    public $worksheet;
    public $parent;
    public $id;
    public $removed = false;
    public $consultantComment = null;

    function __construct()
    {
        parent::__construct();
    }

}
