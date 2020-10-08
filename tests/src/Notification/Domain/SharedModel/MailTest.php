<?php

namespace Notification\Domain\SharedModel;

use Notification\Domain\SharedModel\Mail\Recipient;
use Tests\TestBase;

class MailTest extends TestBase
{

    protected $mail;
    protected $senderMailAddress = "new_sender@email.org";
    protected $senderName = "new sender name";
    protected $subject = "new subject";
    protected $message = "new message";
    protected $htmlMessage = "new html message";
    protected $recipientMailAddress = "new_recipient@mail.org";
    protected $recipientName = "new recipient name";

    protected function setUp(): void
    {
        parent::setUp();
        $this->recipients = $this->buildMockOfClass(Recipient::class);
        $this->mail = new TestableMail(
                "sender@mail.org", "sender name", "subject", "message", "html message", "recipient@mailorg",
                "recipient name");
    }

    protected function executeConstruct()
    {
        return new TestableMail(
                $this->senderMailAddress, $this->senderName, $this->subject, $this->message, $this->htmlMessage,
                $this->recipientMailAddress, $this->recipientName);
    }

    public function test_construct_setProperties()
    {
        $mail = $this->executeConstruct();
        $this->assertEquals($this->senderMailAddress, $mail->senderMailAddress);
        $this->assertEquals($this->senderName, $mail->senderName);
        $this->assertEquals($this->subject, $mail->subject);
        $this->assertEquals($this->message, $mail->message);
        $this->assertEquals($this->htmlMessage, $mail->htmlMessage);
    }

    public function test_construct_addRecipient()
    {
        $mail = $this->executeConstruct();
        $recipient = new Recipient($this->recipientMailAddress, $this->recipientName);
        $this->assertEquals($recipient, $mail->recipients->first());
    }

}

class TestableMail extends Mail
{

    public $senderMailAddress;
    public $senderName;
    public $subject;
    public $message;
    public $htmlMessage;
    public $recipients;

}
