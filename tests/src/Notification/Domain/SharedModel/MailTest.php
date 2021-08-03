<?php

namespace Notification\Domain\SharedModel;

use Notification\Domain\SharedModel\Mail\Recipient;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class MailTest extends TestBase
{

    protected $mail;
    protected $id = "newId";
    protected $senderMailAddress = "new_sender@email.org";
    protected $senderName = "new sender name";
    protected $mailMessage;
    protected $recipientMailAddress = "new_recipient@mail.org";
    protected $recipientName = "new recipient name";
    protected $icalAttachmentContent = 'new ical attachment content';

    protected function setUp(): void
    {
        parent::setUp();
        $this->recipients = $this->buildMockOfClass(Recipient::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->mail = new TestableMail(
                "id", "sender@mail.org", "sender name", $this->mailMessage, "recipient@mailorg", "recipient name");
    }

    protected function executeConstruct()
    {
        return new TestableMail(
                $this->id, $this->senderMailAddress, $this->senderName, $this->mailMessage, $this->recipientMailAddress,
                $this->recipientName);
    }
    public function test_construct_setProperties()
    {
        $mail = $this->executeConstruct();
        $this->assertEquals($this->id, $mail->id);
        $this->assertEquals($this->senderMailAddress, $mail->senderMailAddress);
        $this->assertEquals($this->senderName, $mail->senderName);
        $this->assertEquals($this->mailMessage, $mail->message);
    }

    public function test_construct_addRecipient()
    {
        $mail = $this->executeConstruct();
        $this->assertInstanceOf(Recipient::class, $mail->recipients->first());
    }
    
    public function test_getSubject_returnMailMessageGetSubject()
    {
        $this->mailMessage->expects($this->once())->method("getSubject");
        $this->mail->getSubject();
    }
    
    public function test_getMessage_returnMailMessageGetTextMessage()
    {
        $this->mailMessage->expects($this->once())->method("getTextMessage");
        $this->mail->getMessage();
    }
    
    public function test_getHtmlMessage_returnMailMessageGetHtmlMessage()
    {
        $this->mailMessage->expects($this->once())->method("getHtmlMessage");
        $this->mail->getHtmlMessage();
    }
    
    public function test_setIcalAttachmentContent_setIcalAttachment()
    {
        $this->mail->setIcalAttachment($this->icalAttachmentContent);
        $icalAttachment = new Mail\IcalAttachment($this->mail, $this->mail->id, $this->icalAttachmentContent);
        $this->assertEquals($icalAttachment, $this->mail->icalAttachment);
    }

}

class TestableMail extends Mail
{

    public $id;
    public $senderMailAddress;
    public $senderName;
    public $message;
    public $recipients;
    public $icalAttachment;

}
