<?php

namespace Notification\Domain\Model\Firm\Program\Consultant;

use Notification\Domain\Model\ {
    Firm\Personnel\PersonnelMailNotification,
    Firm\Program\Consultant,
    SharedEntity\KonsultaMailMessage
};
use Resources\Application\Service\ {
    Mailer,
    SenderInterface
};
use Tests\TestBase;

class ConsultantMailNotificationTest extends TestBase
{
    protected $consultantMailNotification;
    protected $consultant;
    protected $personnelMailNotification;
    
    protected $mailer, $sender, $mailMessage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->personnelMailNotification = $this->buildMockOfClass(PersonnelMailNotification::class);
        
        $this->consultantMailNotification = new TestableConsultantMailNotification($this->consultant, $this->personnelMailNotification);
        
        $this->mailer = $this->buildMockOfClass(Mailer::class);
        $this->sender = $this->buildMockOfInterface(SenderInterface::class);
        $this->mailMessage = $this->buildMockOfClass(KonsultaMailMessage::class);
    }
    
    public function test_construct_setProperties()
    {
        $consultantMailNotification = new TestableConsultantMailNotification($this->consultant, $this->personnelMailNotification);
        
        $this->assertEquals($this->consultant, $consultantMailNotification->consultant);
        $this->assertEquals($this->personnelMailNotification, $consultantMailNotification->personnelMailNotification);
    }
    
    public function test_send_sendPersonnelMailNotification()
    {
        $this->personnelMailNotification->expects($this->once())
                ->method('send')
                ->with($this->mailer, $this->sender, $this->mailMessage);
        $this->consultantMailNotification->send($this->mailer, $this->sender, $this->mailMessage);
    }
}

class TestableConsultantMailNotification extends ConsultantMailNotification
{
    public $consultant;
    public $personnelMailNotification;
}
