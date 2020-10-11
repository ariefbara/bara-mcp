<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\ {
    Model\Firm\Personnel,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification,
    SharedModel\MailMessage
};
use Tests\TestBase;

class ConsultantTest extends TestBase
{
    protected $consultant;
    protected $personnel;
    protected $mailGenerator;
    protected $mailMessage;
    protected $notification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = new TestableConsultant();
        
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->consultant->personnel = $this->personnel;
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        
        $this->notification = $this->buildMockOfClass(ContainNotification::class);
    }
    
    public function test_getPersonnelFullName_returnPersonnelGetFullNameResult()
    {
        $this->personnel->expects($this->once())
                ->method("getFullName");
        $this->consultant->getPersonnelFullName();
    }
    
    protected function executeRegisterMailRecipient()
    {
        $this->consultant->registerMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    public function test_registerMailRecipient_prependConsultantPathToMailMessageUrlPath()
    {
        $this->mailMessage->expects($this->once())
                ->method("prependUrlPath")
                ->with("/program-consultations/{$this->consultant->id}");
        $this->executeRegisterMailRecipient();
    }
    public function test_registerMailRecipient_registerPersonnelAsRecipientOnModifiedMail()
    {
        $modifiedMailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->mailMessage->expects($this->once())
                ->method("prependUrlPath")
                ->willReturn($modifiedMailMessage);
        $this->personnel->expects($this->once())
                ->method("registerAsMailRecipient")
                ->with($this->mailGenerator, $modifiedMailMessage);
        $this->executeRegisterMailRecipient();
    }
    
    public function test_registerNotificationRecipient_addPersonnelAsNotificationRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addPersonnelRecipient")
                ->with($this->personnel);
        $this->consultant->registerNotificationRecipient($this->notification);
    }
    
/*    
    public function test_getPersonnelMailRecipient_returnPersonnelsGetMailRecipientResult()
    {
        $this->personnel->expects($this->once())
                ->method('getMailRecipient');
        $this->consultant->getPersonnelMailRecipient();
    }
    
    public function test_getPersonnelName_returnPersonnelName()
    {
        $this->personnel->expects($this->once())
                ->method('getName');
        $this->consultant->getPersonnelName();
        
    }
    
    public function test_createMailNotification_returnConsultantMailNotification()
    {
        $personnelMailNotification = $this->buildMockOfClass(Personnel\PersonnelMailNotification::class);
        $this->personnel->expects($this->once())
                ->method('createMailNotification')
                ->willReturn($personnelMailNotification);
        
        $consultantMailNotification = new ConsultantMailNotification($this->consultant, $personnelMailNotification);
        
        $this->assertEquals($consultantMailNotification, $this->consultant->createMailNotification());
    }
 * 
 */
}

class TestableConsultant extends Consultant
{
    public $program;
    public $id = "consultantId";
    public $personnel;
    
    function __construct()
    {
        parent::__construct();
    }
}
