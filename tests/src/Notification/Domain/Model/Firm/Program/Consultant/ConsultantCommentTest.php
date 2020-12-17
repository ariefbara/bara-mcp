<?php

namespace Notification\Domain\Model\Firm\Program\Consultant;

use Notification\Domain\Model\Firm\Program\Consultant;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotification;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class ConsultantCommentTest extends TestBase
{

    protected $consultant;
    protected $consultantComment;
    protected $mailGenerator;
    protected $mailMessage;
    protected $notification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        
        $this->consultantComment = new TestableConsultantComment();
        $this->consultantComment->consultant = $this->consultant;
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        
        $this->notification = $this->buildMockOfInterface(ContainNotification::class);
    }
    
    public function test_registerConsultantAsMailRecipient_executeConsultantsRegisterMailRecipient()
    {
        $this->consultant->expects($this->once())
                ->method("registerAsCommentMailRecipient")
                ->with($this->mailGenerator, $this->mailMessage);
        $this->consultantComment->registerConsultantAsMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    
    public function test_registerConsultantAsNotificationRecipient_executeConsultantsRegisterNotificationRecipient()
    {
        $this->consultant->expects($this->once())
                ->method("registerNotificationRecipient")
                ->with($this->notification);
        $this->consultantComment->registerConsultantAsNotificationRecipient($this->notification);
    }
    
    public function test_register_getConsultantName_returnConsultantsGetPersonnelFullNameResult()
    {
        $this->consultant->expects($this->once())
                ->method("getPersonnelFullName");
        $this->consultantComment->getConsultantName();
    }

}

class TestableConsultantComment extends ConsultantComment
{
    public $consultant;
    public $id = "consultantCommentId";
    public $comment;
    
    function __construct()
    {
        ;
    }
}
