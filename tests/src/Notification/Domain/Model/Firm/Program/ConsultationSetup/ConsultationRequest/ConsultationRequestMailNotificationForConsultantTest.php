<?php

namespace Notification\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;

use Notification\Domain\Model\ {
    Firm\Program\Consultant\ConsultantMailNotification,
    Firm\Program\ConsultationSetup\ConsultationRequest,
    SharedEntity\KonsultaMailMessage
};
use Resources\Application\Service\ {
    Mailer,
    SenderInterface
};
use Tests\TestBase;

class ConsultationRequestMailNotificationForConsultantTest extends TestBase
{
    protected $mailNotification;
    protected $consultationRequest, $sender;
    protected $consultantMailNotification;
    protected $mailMessage;
    
    protected $mailer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->sender = $this->buildMockOfInterface(SenderInterface::class);
        $this->consultationRequest->expects($this->any())
                ->method('getFirmMailSender')
                ->willReturn($this->sender);
        
        $this->consultantMailNotification = $this->buildMockOfClass(ConsultantMailNotification::class);
        $this->mailMessage = $this->buildMockOfClass(KonsultaMailMessage::class);
        
        $this->mailNotification = new TestableConsultationRequestMailNotificationForConsultant($this->consultationRequest, $this->consultantMailNotification, $this->mailMessage);
        
        $this->mailer = $this->buildMockOfInterface(Mailer::class);
    }
    
    public function test_construct_setProperties()
    {
        $mailNotification = new TestableConsultationRequestMailNotificationForConsultant($this->consultationRequest, $this->consultantMailNotification, $this->mailMessage);
        $this->assertEquals($this->consultationRequest, $mailNotification->consultationRequest);
        $this->assertEquals($this->consultantMailNotification, $mailNotification->consultantMailNotification);
        $this->assertEquals($this->mailMessage, $mailNotification->mailMessage);
    }
    
    public function test_send_sendConsultantMailNotification()
    {
        $this->consultantMailNotification->expects($this->once())
                ->method('send')
                ->with($this->mailer, $this->sender, $this->mailMessage);
        $this->mailNotification->send($this->mailer);
    }
}

class TestableConsultationRequestMailNotificationForConsultant extends ConsultationRequestMailNotificationForConsultant
{
    public $consultationRequest;
    public $consultantMailNotification;
    public $mailMessage;
}
