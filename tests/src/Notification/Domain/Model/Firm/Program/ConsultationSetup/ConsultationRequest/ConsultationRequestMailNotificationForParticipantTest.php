<?php

namespace Notification\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;

use Notification\Domain\Model\ {
    Firm\Program\ConsultationSetup\ConsultationRequest,
    Firm\Program\Participant\ParticipantMailNotification,
    SharedEntity\KonsultaMailMessage
};
use Resources\Application\Service\ {
    Mailer,
    SenderInterface
};
use Tests\TestBase;

class ConsultationRequestMailNotificationForParticipantTest extends TestBase
{

    protected $mailNotification;
    protected $consultationRequest, $sender;
    protected $participantMailNotification;
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
        
        $this->participantMailNotification = $this->buildMockOfClass(ParticipantMailNotification::class);
        $this->mailMessage = $this->buildMockOfClass(KonsultaMailMessage::class);
        
        $this->mailNotification = new TestableConsultationRequestMailNotificationForParticipant(
                $this->consultationRequest, $this->participantMailNotification, $this->mailMessage);
        
        $this->mailer = $this->buildMockOfInterface(Mailer::class);
    }
    
    public function test_construct_setProperties()
    {
        $mailNotification = new TestableConsultationRequestMailNotificationForParticipant($this->consultationRequest, $this->participantMailNotification, $this->mailMessage);
        $this->assertEquals($this->consultationRequest, $mailNotification->consultationRequest);
        $this->assertEquals($this->participantMailNotification, $mailNotification->participantMailNotification);
        $this->assertEquals($this->mailMessage, $mailNotification->mailMessage);
    }
    
    public function test_send_sendParticipantmailNotification()
    {
        
        $this->participantMailNotification->expects($this->once())
                ->method('send')
                ->with($this->mailer, $this->sender, $this->mailMessage);
        
        $this->mailNotification->send($this->mailer);
    }
}

class TestableConsultationRequestMailNotificationForParticipant extends ConsultationRequestMailNotificationForParticipant
{
    public $consultationRequest;
    public $participantMailNotification;
    public $mailMessage;
}
