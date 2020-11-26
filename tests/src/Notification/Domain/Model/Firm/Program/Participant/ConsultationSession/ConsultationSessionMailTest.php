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
    protected $mailMessage;
    protected $id = "newId", $senderMailAddress = "sender@email.org", $senderName = "sender name",
            $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
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

}

class TestableConsultationSessionMail extends ConsultationSessionMail
{

    public $consultationSession;
    public $id;
    public $mail;

}
