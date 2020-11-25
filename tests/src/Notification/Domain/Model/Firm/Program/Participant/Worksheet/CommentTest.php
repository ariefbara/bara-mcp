<?php

namespace Notification\Domain\Model\Firm\Program\Participant\Worksheet;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\ {
    Model\Firm\Program\Consultant\ConsultantComment,
    Model\Firm\Program\Participant\Worksheet,
    Model\Firm\Program\Participant\Worksheet\Comment\CommentMail,
    Model\Firm\Program\Participant\Worksheet\Comment\CommentNotification,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification
};
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class CommentTest extends TestBase
{
    protected $worksheet;
    protected $parent;
    protected $consultantComment;
    protected $comment;
    protected $mailMessage, $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";
    
    protected $mailGenerator;
    protected $notification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->parent = $this->buildMockOfClass(Comment::class);
        $this->consultantComment = $this->buildMockOfClass(ConsultantComment::class);
        
        $this->comment = new TestableComment();
        $this->comment->worksheet = $this->worksheet;
        $this->comment->parent = $this->parent;
        $this->comment->consultantComment = $this->consultantComment;
        $this->comment->commentMails = new ArrayCollection();
        $this->comment->commentNotifications = new ArrayCollection();
        
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        
        $this->notification = $this->buildMockOfInterface(ContainNotification::class);
    }
    
    protected function executeGenerateNotificationsForRepliedConsultantComment()
    {
        $this->comment->generateNotificationsForRepliedConsultantComment();
    }
    public function test_generateNotificationsForRepliedConsultantComment_executeParentsRegisterConsultantAsMailRecipient()
    {
        $this->parent->expects($this->once())
                ->method("registerConsultantAsMailRecipient")
                ->with($this->comment, $this->anything());
        $this->executeGenerateNotificationsForRepliedConsultantComment();
    }
    public function test_generateNotificationsForRepliedConsultantComment_executeParentsRegisterConsultantAsNotificationRecipient()
    {
        $this->parent->expects($this->once())
                ->method("registerConsultantAsNotificationRecipient");
        $this->executeGenerateNotificationsForRepliedConsultantComment();
    }
    public function test_generateNotificationsForRepliedConsultantComment_addNotification()
    {
        $this->executeGenerateNotificationsForRepliedConsultantComment();
        $this->assertInstanceOf(CommentNotification::class, $this->comment->commentNotifications->first());
    }
    
    protected function executeGenerateNotificationTriggeredByConsultant()
    {
        $this->comment->generateNotificationsTriggeredByConsultant();
    }
    public function test_generateNotificationsTriggeredByConsultant_executeWorksheetsRegisterParticipantAsMailRecipient()
    {
        $this->worksheet->expects($this->once())
                ->method("registerParticipantAsMailRecipient")
                ->with($this->comment, $this->anything());
        $this->executeGenerateNotificationTriggeredByConsultant();
    }
    public function test_generateNotificationsTriggeredByConsultant_executeWorksheetsRegisterParticipantAsNotificationRecipient()
    {
        $this->worksheet->expects($this->once())
                ->method("registerParticipantAsNotificationRecipient");
        $this->executeGenerateNotificationTriggeredByConsultant();
    }
    public function test_generateNotificationsTriggeredByConsultant_addNotification()
    {
        $this->executeGenerateNotificationTriggeredByConsultant();
        $this->assertInstanceOf(CommentNotification::class, $this->comment->commentNotifications->first());
    }
    
    public function test_addMail_addCommentMailToCollection()
    {
        $this->comment->addMail($this->mailMessage, $this->recipientMailAddress, $this->recipientName);
        $this->assertInstanceOf(CommentMail::class, $this->comment->commentMails->first());
    }
    
    public function test_registerConsultantAsMailRecipient_executeConsultantCommentsRegisterConsultantAsMailRecipient()
    {
        $this->consultantComment->expects($this->once())
                ->method("registerConsultantAsMailRecipient")
                ->with($this->mailGenerator, $this->mailMessage);
        $this->comment->registerConsultantAsMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    
    public function test_registerConsultantAsNotificationRecipient_executeConsultantCommentRegisterConsultantAsNotificationRecipient()
    {
        $this->consultantComment->expects($this->once())
                ->method("registerConsultantAsNotificationRecipient")
                ->with($this->notification);
        $this->comment->registerConsultantAsNotificationRecipient($this->notification);
    }
    public function test_getConsultantName_returnConsultantCommentGetConsultantNameResult()
    {
        $this->consultantComment->expects($this->once())
                ->method("getConsultantName");
        $this->comment->getConsultantName();
    }
}

class TestableComment extends Comment
{
    public $worksheet;
    public $parent;
    public $id = "commentId";
    public $consultantComment = null;
    public $commentMails;
    public $commentNotifications;
    
    function __construct()
    {
        parent::__construct();
    }
}
