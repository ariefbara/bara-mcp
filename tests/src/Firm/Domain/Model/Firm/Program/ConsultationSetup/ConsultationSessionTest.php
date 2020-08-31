<?php

namespace Firm\Domain\Model\Firm\Program\ConsultationSetup;

use DateTimeImmutable;
use Firm\Domain\Model\Firm\Program\ {
    Consultant,
    ConsultationSetup,
    Participant
};
use Resources\ {
    Application\Service\Mailer,
    Domain\ValueObject\DateTimeInterval
};
use Tests\TestBase;

class ConsultationSessionTest extends TestBase
{
    protected $consultationSession;
    protected $participant;
    protected $consultationSetup;
    protected $consultant;
    
    protected $mailer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = new TestableConsultationSession();
        $this->consultationSession->startEndTime = new DateTimeInterval(new DateTimeImmutable(), new DateTimeImmutable());
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->consultationSession->participant = $this->participant;
        
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultationSession->consultationSetup = $this->consultationSetup;
        
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultationSession->consultant = $this->consultant;
        
        $this->mailer = $this->buildMockOfInterface(Mailer::class);
    }
    
    public function test_sendMail_sendMailToMailer()
    {
        $this->consultant->expects($this->once())
                ->method('getMailRecipient');
        
        $this->participant->expects($this->once())
                ->method('getMailRecipient');
        
        $this->mailer->expects($this->exactly(2))
                ->method('send');
        
        $this->consultationSession->sendMail($this->mailer);
    }
}

class TestableConsultationSession extends ConsultationSession
{
    public $participant;
    public $id;
    public $consultationSetup;
    public $consultant;
    public $startEndTime;
    
    function __construct()
    {
        parent::__construct();
    }
}
