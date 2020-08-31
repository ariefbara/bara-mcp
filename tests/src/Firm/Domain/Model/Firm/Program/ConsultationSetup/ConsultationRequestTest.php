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

class ConsultationRequestTest extends TestBase
{
    protected $consultationRequest;
    protected $participant;
    protected $consultationSetup;
    protected $consultant;
    
    protected $mailer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequest = new TestableConsultationRequest();
        $this->consultationRequest->status = 'proposed';
        $this->consultationRequest->startEndTime = new DateTimeInterval(new DateTimeImmutable(), new DateTimeImmutable());
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->consultationRequest->participant = $this->participant;
        
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultationRequest->consultationSetup = $this->consultationSetup;
        
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultationRequest->consultant = $this->consultant;
        
        $this->mailer = $this->buildMockOfInterface(Mailer::class);
    }
    
    protected function executeSendMail()
    {
        $this->consultationRequest->sendMail($this->mailer);
    }
    
    public function test_sendMail_sendMailToMailer()
    {
        $this->consultant->expects($this->once())
                ->method('getMailRecipient');
        
        $this->mailer->expects($this->once())
                ->method('send');
        
        $this->executeSendMail();
    }
    public function test_sendMail_offeredStatus_setMailToParticipant()
    {
        $this->consultationRequest->status = 'offered';
        
        $this->participant->expects($this->once())
                ->method('getMailRecipient');
        
        $this->mailer->expects($this->once())
                ->method('send');
        
        $this->executeSendMail();
    }
    
    
}

class TestableConsultationRequest extends ConsultationRequest
{
    public $participant;
    public $id;
    public $consultationSetup;
    public $consultant;
    public $startEndTime;
    public $concluded;
    public $status;
    
    function __construct()
    {
        parent::__construct();
    }
}
