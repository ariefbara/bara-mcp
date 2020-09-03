<?php

namespace Notification\Domain\Model\Personnel;

use Notification\Domain\Model\ {
    Firm\Personnel,
    Firm\Personnel\PersonnelMailNotification,
    SharedEntity\KonsultaMailMessage
};
use Resources\Application\Service\ {
    Mailer,
    RecipientInterface,
    SenderInterface
};
use Tests\TestBase;

class PersonnelMailNotificationTest extends TestBase
{
    protected $personnelMailNotification;
    protected $personnel, $recipient;
    
    protected $mailer, $sender, $mailMessage;


    protected function setUp(): void
    {
        parent::setUp();
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->recipient = $this->buildMockOfInterface(RecipientInterface::class);
        $this->personnel->expects($this->any())
                ->method('getMailRecipient')
                ->willReturn($this->recipient);
        
        $this->personnelMailNotification = new PersonnelMailNotification($this->personnel);
        
        $this->mailer = $this->buildMockOfClass(Mailer::class);
        $this->sender = $this->buildMockOfInterface(SenderInterface::class);
        $this->mailMessage = $this->buildMockOfClass(KonsultaMailMessage::class);
    }
    
    public function test_construct_setProperties()
    {
        $mailNotification = new TestablePersonnelMailNotification($this->personnel);
        $this->assertEquals($this->personnel, $mailNotification->personnel);
    }
    
    public function test_send_send_expectedResult()
    {
        $this->mailer->expects($this->once())
                ->method('send')
                ->with($this->sender, $this->mailMessage, $this->recipient);
        $this->personnelMailNotification->send($this->mailer, $this->sender, $this->mailMessage);
    }
}

class TestablePersonnelMailNotification extends PersonnelMailNotification
{
    public $personnel;
}
