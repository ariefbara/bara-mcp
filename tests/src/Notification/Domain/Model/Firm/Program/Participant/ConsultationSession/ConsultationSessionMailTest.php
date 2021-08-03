<?php

namespace Notification\Domain\Model\Firm\Program\Participant\ConsultationSession;

use Notification\Domain\ {
    Model\Firm\Program\Participant\ConsultationSession,
    SharedModel\Mail
};
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class ConsultationSessionMailTest extends TestBase
{

    protected $consultationSession;
    protected $consultationSessionMail, $mail;
    protected $mailMessage;
    protected $id = "newId", $senderMailAddress = "sender@email.org", $senderName = "sender name",
            $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";
    protected $icalContent = 'new ical content';

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        
        $this->consultationSessionMail = new TestableConsultationSessionMail(
                $this->consultationSession, 'id', 'sender@email.org', 'sender name', $this->mailMessage, 
                'recipient@email.org', 'recipient name');
        $this->mail = $this->buildMockOfClass(Mail::class);
        $this->consultationSessionMail->mail = $this->mail;
    }

    public function test_construct_setProperties()
    {
        $consultationSessionMail = new TestableConsultationSessionMail(
                $this->consultationSession, $this->id, $this->senderMailAddress, $this->senderName, $this->mailMessage,
                $this->recipientMailAddress, $this->recipientName);

        $this->assertEquals($this->consultationSession, $consultationSessionMail->consultationSession);
        $this->assertEquals($this->id, $consultationSessionMail->id);
        $this->assertInstanceOf(Mail::class, $consultationSessionMail->mail);
    }
    
    public function test_setIcalAttachment_setMailIcalAttachment()
    {
        $this->mail->expects($this->once())
                ->method('setIcalAttachment')
                ->with($this->icalContent);
        $this->consultationSessionMail->setIcalAttachment($this->icalContent);
    }

}

class TestableConsultationSessionMail extends ConsultationSessionMail
{

    public $consultationSession;
    public $id;
    public $mail;

}
