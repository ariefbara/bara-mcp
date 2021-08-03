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
    protected $consultationRequestMail, $mail;
    protected $mailMessage;
    protected $id = "newId", $senderMailAddress = "sender@email.org", $senderName = "sender name",
            $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";
    protected $icalContent = 'new ical content';

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        
        $this->consultationRequestMail = new TestableConsultationRequestMail(
                $this->consultationRequest, 'id', 'sender@email.org', 'sender name', $this->mailMessage, 
                'recipient@email.org', 'recipient name');
        $this->mail = $this->buildMockOfClass(Mail::class);
        $this->consultationRequestMail->mail = $this->mail;
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
    
    public function test_setIcalAttachment_setMailIcalAttachment()
    {
        $this->mail->expects($this->once())
                ->method('setIcalAttachment')
                ->with($this->icalContent);
        $this->consultationRequestMail->setIcalAttachment($this->icalContent);
    }

}

class TestableConsultationRequestMail extends ConsultationRequestMail
{

    public $consultationRequest;
    public $id;
    public $mail;

}
