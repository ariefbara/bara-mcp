<?php

namespace Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest;

use Notification\Domain\ {
    Model\Firm\Program\Participant\ConsultationRequest,
    SharedModel\Mail
};
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class ConsultationRequestMailTest extends TestBase
{

    protected $consultationRequest;
    protected $mailMessage;
    protected $id = "newId", $senderMailAddress = "sender@email.org", $senderName = "sender name",
            $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
    }

    public function test_construct_setProperties()
    {
        $consultationRequestMail = new TestableConsultationRequestMail(
                $this->consultationRequest, $this->id, $this->senderMailAddress, $this->senderName, $this->mailMessage,
                $this->recipientMailAddress, $this->recipientName);
        $this->assertEquals($this->consultationRequest, $consultationRequestMail->consultationRequest);
        $this->assertEquals($this->id, $consultationRequestMail->id);
        $this->assertInstanceOf(Mail::class, $consultationRequestMail->mail);
    }

}

class TestableConsultationRequestMail extends ConsultationRequestMail
{

    public $consultationRequest;
    public $id;
    public $mail;

}
