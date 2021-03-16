<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Notification\Domain\ {
    Model\Firm\Program\Participant,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification
};
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class WorksheetTest extends TestBase
{
    protected $participant;
    protected $worksheet;
    protected $mailGenerator;
    protected $mailMessage;
    protected $notification;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        
        $this->worksheet = new TestableWorksheet();
        $this->worksheet->participant = $this->participant;
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        
        $this->notification = $this->buildMockOfInterface(ContainNotification::class);
    }
    
    public function test_getParticipanName_returnParticipantGetNameResult()
    {
        $this->participant->expects($this->once())
                ->method("getName");
        $this->worksheet->getParticipantName();
    }
    public function test_getFirmDomain_returnParticipantsGetFirmDomainResult()
    {
        $this->participant->expects($this->once())
                ->method("getFirmDomain");
        $this->worksheet->getFirmDomain();
    }
    
    public function test_getFirmLogoPath_returnParticipantGetFirmLogoPathResult()
    {
        $this->participant->expects($this->once())
                ->method("getFirmLogoPath");
        $this->worksheet->getFirmLogoPath();
    }
    
    public function test_getFirmMailSenderAddress_returnParticipantsGetFirmMailSenderAddressResult()
    {
        $this->participant->expects($this->once())
                ->method("getFirmMailSenderAddress");
        $this->worksheet->getFirmMailSenderAddress();
    }
    public function test_getFirmMailSenderName_returnParticipantsGetFirmMailSenderNameResult()
    {
        $this->participant->expects($this->once())
                ->method("getFirmMailSenderName");
        $this->worksheet->getFirmMailSenderName();
    }
    
    protected function executeRegisterParticipantAsMailRecipient()
    {
        $this->worksheet->registerParticipantAsMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    public function test_registerParticipantAsMailRecipient_modifyMailMessageUrl()
    {
        $this->mailMessage->expects($this->once())
                ->method("prependUrlPath")
                ->with("/worksheet/{$this->worksheet->id}");
        $this->executeRegisterParticipantAsMailRecipient();
    }
    public function test_registerParticipantAsMailRecipient_executeParticipantsRegisterMailRecipient()
    {
        $this->mailMessage->expects($this->any())
                ->method("prependUrlPath")
                ->willReturn($mailMessage = $this->buildMockOfClass(MailMessage::class));
        $this->participant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->mailGenerator, $mailMessage);
        $this->executeRegisterParticipantAsMailRecipient();
    }
    
    public function test_registerParticipantAsNotificationRecipient_execugteParticipantsRegisterNotificationRecipient()
    {
        $this->participant->expects($this->once())
                ->method("registerNotificationRecipient");
        $this->worksheet->registerParticipantAsNotificationRecipient($this->notification);
    }
}

class TestableWorksheet extends Worksheet
{
    public $participant;
    public $id;
    
    function __construct()
    {
        parent::__construct();
    }
}
